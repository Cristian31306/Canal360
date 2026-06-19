import { GoogleGenerativeAI } from "@google/generative-ai";

const API_KEY = import.meta.env.VITE_GEMINI_API_KEY;

// Inicializa el SDK de Gemini
const genAI = new GoogleGenerativeAI(API_KEY);

export async function extractQuoteData(file) {
  try {
    const base64Data = await fileToBase64(file);
    
    // Prepara el archivo para enviarlo a Gemini
    const filePart = {
      inlineData: {
        data: base64Data,
        mimeType: file.type || "application/pdf"
      }
    };

    const prompt = `
Eres un experto analista de seguros de Colombia. Voy a proporcionarte un documento PDF que es una cotización de seguro (puede ser de auto, vida, salud, copropiedades, etc.).
Tu tarea es leer la cotización, identificar los datos clave y extraerlos en formato estructurado.

DEVUELVE ÚNICAMENTE UN JSON VÁLIDO CON ESTA ESTRUCTURA EXACTA, sin texto adicional:
{
  "aseguradora": "Nombre corto de la aseguradora (ej. Sura, Allianz, Bolívar, Mapfre, etc.)",
  "tipoSeguro": "Tipo de seguro que detectes (ej. Seguro Todo Riesgo Auto, Póliza de Salud, Vida, etc.)",
  "nombreCliente": "El nombre de la persona o empresa a la que va dirigida la cotización",
  "documentoCliente": "El número de documento de identidad, cédula, NIT o pasaporte (o 'No especificado')",
  "contactoCliente": "Teléfono, celular o correo electrónico del cliente (o 'No especificado')",
  "vehiculo": "Si es un seguro de auto, pon el vehículo, modelo y placa (ej. FORD TERRITORY 2026 - ABC123). Si no, 'No aplica'",
  "precioTotal": "El valor total o prima total a pagar formateado en pesos colombianos (ej. $ 2.500.000)",
  "categorias": [
    {
      "nombre": "Nombre de la categoría (ej. 'Responsabilidad Civil', 'Daños y Hurto', 'Asistencias', 'Garantías de Movilidad')",
      "caracteristicas": [
        { "nombre": "Responsabilidad Civil (Límite global)", "valor": "Monto o detalle" },
        { "nombre": "Deducible Pérdida Total", "valor": "Porcentaje o detalle" }
      ]
    }
  ]
}

// IMPORTANTE: Para que la tabla comparativa sea idéntica a la plantilla profesional, DEBES usar estrictamente las siguientes CATEGORÍAS y NOMBRES ESTÁNDAR según el tipo de seguro.
//
// === DICCIONARIO PARA SEGUROS DE AUTO ===
// Categorías permitidas: "Responsabilidad Civil", "Protección Ante Daños o Hurto", "Garantías de Movilidad", "Asistencias al Vehículo", "Valor Diferencial".
// Características permitidas: "Responsabilidad Civil (Límite global)", "Deducible Pérdida Total Daños", "Deducible Pérdida Parcial Daños", "Deducible Pérdida Total Hurto", "Deducible Pérdida Parcial Hurto", "Vehículo de reemplazo", "Servicio de grúa", "Carro taller", "Conductor elegido", "Amparo Patrimonial", "Asistencia Jurídica".
//
// === DICCIONARIO PARA SEGUROS DE SALUD / VIDA ===
// Categorías permitidas: "Hospitalización y Urgencias", "Consultas y Terapias", "Cobertura Internacional", "Asistencias y Extras".
// Características permitidas: "Hospitalización y Cirugía", "Habitación Individual", "Honorarios Médicos", "Urgencias Nacionales", "Urgencias Internacionales", "Terapias", "Exámenes de Diagnóstico", "Asistencia Domiciliaria", "Cobertura de Medicamentos", "Maternidad".

REGLA DE ORO: Analiza de qué trata el seguro y agrupa la información en las "categorias" exactas del catálogo. Usa exclusivamente los nombres de "caracteristicas" del diccionario para la clave "nombre". Nunca uses el nombre comercial propio de la aseguradora.

REGLAS DE FORMATO Y UNIFORMIDAD (ESTRICTO):
1. NUNCA uses textos TODO EN MAYÚSCULAS (ej. prohibido usar "SI AMPARA" o "INCLUIDO"). Usa formato tipo oración (ej. "Incluido").
2. Unifica respuestas afirmativas: Si la cobertura existe y no tiene un límite o valor específico, escribe siempre "Incluido" (no uses "Si ampara", "Ampara", "Si", "Incluida").
3. Estandariza los salarios: Escribe siempre "SMMLV" (sin puntos, no uses "S.M.M.L.V.").
4. Estandariza el dinero: Usa siempre el signo pesos separado por un espacio (ej. "$ 50.000.000").
5. Si no aplica o no tiene la cobertura, escribe "No incluido" o "No aplica".

Si no encuentras un dato específico de la estructura principal, pon "No especificado".
Asegúrate de que la salida sea estrictamente JSON analizable. No uses bloques de código tipo \`\`\`json.
`;

    // Intentamos primero con los modelos que tienen mayor límite en tu cuenta gratuita
    const modelosParaProbar = [
      "gemini-3.1-flash-lite", // Este te da 500 peticiones por día!
      "gemini-3-flash",
      "gemini-2.5-flash-lite",
      "gemini-2.5-flash"
    ];
    return await tryModelWithFallback(modelosParaProbar, prompt, filePart);

  } catch (error) {
    console.error("Error al procesar el PDF con Gemini:", error);
    throw error;
  }
}

// Función para intentar con varios modelos en caso de alta demanda (Error 503)
async function tryModelWithFallback(modelNames, prompt, filePart) {
  let lastError;
  for (const modelName of modelNames) {
    try {
      const model = genAI.getGenerativeModel({ model: modelName });
      const result = await model.generateContent([prompt, filePart]);
      const responseText = result.response.text();
      
      const cleanJsonString = responseText.replace(/```json/gi, '').replace(/```/g, '').trim();
      return JSON.parse(cleanJsonString);
    } catch (error) {
      lastError = error;
      if (error.message.includes("503") || error.message.includes("high demand") || error.message.includes("404") || error.message.includes("not found")) {
        continue;
      }
      throw error;
    }
  }
  // Si fallan todos los modelos, lanzamos el último error
  throw lastError;
}

// Función auxiliar para convertir el archivo a Base64
function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
      const base64String = reader.result.split(',')[1];
      resolve(base64String);
    };
    reader.onerror = (error) => reject(error);
  });
}
