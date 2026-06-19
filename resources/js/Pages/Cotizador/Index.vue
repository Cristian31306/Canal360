<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { extractQuoteData } from '@/Services/gemini';
import html2pdf from 'html2pdf.js';
import { Download, User, CreditCard, Phone, Calendar, Car, FileUp, Clock, File, Trash2, Play } from 'lucide-vue-next';

const appState = ref('upload'); // 'upload' | 'processing' | 'results'
const quotes = ref([]);
const selectedFiles = ref([]);
const clientInfo = ref({ nombre: '', tipo: '', documento: '', contacto: '', vehiculo: '' });
const recentComparison = ref(null);
const loadingText = ref('Analizando documentos...');

const fileInputRef = ref(null);
const pdfContainerRef = ref(null);

onMounted(() => {
    const saved = localStorage.getItem('recent_quotes');
    if (saved) {
        try {
            recentComparison.value = JSON.parse(saved);
        } catch (e) {
            console.error('Error parsing recent quotes from local storage');
        }
    }
});

const handleDragOver = (e) => {
    e.preventDefault();
};

const handleDrop = async (e) => {
    e.preventDefault();
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        const newFiles = Array.from(e.dataTransfer.files).filter(f => f.type === "application/pdf");
        selectedFiles.value = [...selectedFiles.value, ...newFiles];
    }
};

const handleFileSelect = async (e) => {
    if (e.target.files && e.target.files.length > 0) {
        const newFiles = Array.from(e.target.files).filter(f => f.type === "application/pdf");
        selectedFiles.value = [...selectedFiles.value, ...newFiles];
    }
};

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const removeFile = (idx) => {
    selectedFiles.value = selectedFiles.value.filter((_, i) => i !== idx);
};

const loadRecent = () => {
    quotes.value = recentComparison.value.quotes;
    clientInfo.value = recentComparison.value.clientInfo;
    appState.value = 'results';
};

const processFiles = async (files) => {
    appState.value = 'processing';
    const newQuotes = [];

    let detectedType = '';
    let detectedClient = '';
    let detectedDocument = '';
    let detectedContact = '';
    let detectedVehicle = '';

    try {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type === "application/pdf") {
                loadingText.value = `Analizando: ${file.name}...`;
                const data = await extractQuoteData(file);
                newQuotes.push(data);

                if (!detectedType && data.tipoSeguro && data.tipoSeguro !== "No especificado") {
                    detectedType = data.tipoSeguro;
                }
                if (!detectedClient && data.nombreCliente && data.nombreCliente !== "No especificado") {
                    detectedClient = data.nombreCliente;
                }
                if (!detectedDocument && data.documentoCliente && data.documentoCliente !== "No especificado") {
                    detectedDocument = data.documentoCliente;
                }
                if (!detectedContact && data.contactoCliente && data.contactoCliente !== "No especificado") {
                    detectedContact = data.contactoCliente;
                }
                if (!detectedVehicle && data.vehiculo && data.vehiculo !== "No especificado" && data.vehiculo !== "No aplica") {
                    detectedVehicle = data.vehiculo;
                }
            }
        }

        const dataToSave = {
            quotes: newQuotes,
            clientInfo: {
                nombre: detectedClient || 'Cliente Desconocido',
                tipo: detectedType || 'Seguros Varios',
                documento: detectedDocument || 'No disponible',
                contacto: detectedContact || 'No disponible',
                vehiculo: detectedVehicle || ''
            }
        };

        quotes.value = dataToSave.quotes;
        clientInfo.value = dataToSave.clientInfo;

        localStorage.setItem('recent_quotes', JSON.stringify(dataToSave));
        recentComparison.value = dataToSave;

        selectedFiles.value = [];
        appState.value = 'results';
    } catch (error) {
        console.error(error);
        alert("Hubo un error analizando los documentos. Revisa la consola y tu llave de API.");
        appState.value = 'upload';
    }
};

const getUniqueCategories = () => {
    const categoryMap = new Map();
    quotes.value.forEach(q => {
        if (q.categorias) {
            q.categorias.forEach(cat => {
                if (!categoryMap.has(cat.nombre)) {
                    categoryMap.set(cat.nombre, new Set());
                }
                if (cat.caracteristicas) {
                    cat.caracteristicas.forEach(feat => categoryMap.get(cat.nombre).add(feat.nombre));
                }
            });
        } else if (q.caracteristicas) {
            if (!categoryMap.has("Otras Características")) {
                categoryMap.set("Otras Características", new Set());
            }
            q.caracteristicas.forEach(c => categoryMap.get("Otras Características").add(c.nombre));
        }
    });

    return Array.from(categoryMap.entries()).map(([name, featureSet]) => ({
        name,
        features: Array.from(featureSet)
    }));
};

const handleDownloadPDF = async () => {
    const element = pdfContainerRef.value;

    const opt = {
        margin: 10,
        filename: `Cotizacion_${clientInfo.value.nombre.replace(/ /g, '_')}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    await html2pdf().set(opt).from(element).save();
};

const startNew = () => {
    quotes.value = [];
    appState.value = 'upload';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Cotizador AI" />

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
            <header class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-slate-500 tracking-tight">
                    Comparador de Cotizaciones
                </h1>
                <p class="mt-2 text-lg text-slate-500">Extrae y compara datos de cualquier cotización en PDF al instante con IA.</p>
            </header>

            <main>
                <!-- UPLOAD STATE -->
                <div v-if="appState === 'upload'" class="max-w-2xl mx-auto space-y-8">
                    <div 
                        @dragover="handleDragOver"
                        @drop="handleDrop"
                        @click="triggerFileInput"
                        class="bg-white border-2 border-dashed border-slate-300 rounded-2xl p-12 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-300 shadow-sm"
                    >
                        <div class="flex justify-center mb-4 text-blue-600">
                            <FileUp :size="64" />
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">Arrastra los PDFs de las cotizaciones aquí</h2>
                        <p class="text-slate-500">Soportamos cualquier formato o aseguradora. También puedes hacer clic para buscar.</p>
                        <input 
                            type="file" 
                            multiple 
                            accept=".pdf" 
                            ref="fileInputRef" 
                            class="hidden" 
                            @change="handleFileSelect"
                        />
                    </div>

                    <div v-if="selectedFiles.length > 0" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Archivos listos para comparar ({{ selectedFiles.length }})</h3>
                        <div class="space-y-2 mb-6">
                            <div v-for="(file, idx) in selectedFiles" :key="idx" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="flex items-center gap-3">
                                    <File :size="20" class="text-blue-600" />
                                    <span class="text-sm font-medium text-slate-700">{{ file.name }}</span>
                                </div>
                                <button @click.stop="removeFile(idx)" class="text-red-500 hover:text-red-700 p-1">
                                    <Trash2 :size="18" />
                                </button>
                            </div>
                        </div>
                        <button 
                            @click="processFiles(selectedFiles)"
                            class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all duration-200"
                        >
                            <Play :size="20" /> Iniciar Comparación
                        </button>
                    </div>

                    <div v-if="recentComparison && selectedFiles.length === 0" 
                        @click="loadRecent"
                        class="bg-white p-6 rounded-2xl cursor-pointer shadow-sm border border-slate-200 flex items-center gap-4 transition-all duration-200 hover:-translate-y-1 hover:shadow-md"
                    >
                        <div class="bg-blue-100 p-3 rounded-full flex items-center justify-center">
                            <Clock :size="24" class="text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Cargar última comparación guardada</h3>
                            <p class="text-sm text-slate-500">{{ recentComparison.clientInfo.tipo }} - {{ recentComparison.clientInfo.nombre }}</p>
                        </div>
                    </div>
                </div>

                <!-- PROCESSING STATE -->
                <div v-if="appState === 'processing'" class="text-center p-12 bg-white rounded-2xl border border-slate-200 shadow-md max-w-2xl mx-auto">
                    <div class="inline-block w-12 h-12 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-6"></div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ loadingText }}</h2>
                    <p class="text-slate-500">Estructurando la información con IA. Esto puede tardar unos segundos.</p>
                </div>

                <!-- RESULTS STATE -->
                <div v-if="appState === 'results'" class="space-y-6">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <h2 class="text-2xl font-bold text-slate-800">Resumen Comparativo</h2>
                        <button @click="handleDownloadPDF" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all duration-200">
                            <Download :size="18" /> Descargar PDF
                        </button>
                    </div>

                    <div ref="pdfContainerRef" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                        <div class="mb-8 border-b-2 border-slate-200 pb-6">
                            <h1 class="text-3xl font-black text-slate-900 mb-6 uppercase tracking-wide">Cotización Comparativa: {{ clientInfo.tipo }}</h1>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <User :size="16" class="text-slate-500" />
                                    <span><strong>Cliente:</strong> {{ clientInfo.nombre }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <CreditCard :size="16" class="text-slate-500" />
                                    <span><strong>Documento:</strong> {{ clientInfo.documento }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <Phone :size="16" class="text-slate-500" />
                                    <span><strong>Contacto:</strong> {{ clientInfo.contacto }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <Calendar :size="16" class="text-slate-500" />
                                    <span><strong>Fecha:</strong> {{ new Date().toLocaleDateString() }}</span>
                                </div>
                                <div v-if="clientInfo.vehiculo" class="col-span-full flex items-center gap-2 text-sm text-slate-700">
                                    <Car :size="16" class="text-slate-500" />
                                    <span><strong>Vehículo:</strong> {{ clientInfo.vehiculo }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="p-3 border-b border-slate-200 bg-slate-50 font-bold text-slate-800 border-r w-1/4">Datos Principales</th>
                                        <th v-for="(quote, idx) in quotes" :key="idx" class="p-3 border-b border-slate-200 bg-slate-50 text-center border-r last:border-r-0">
                                            <h3 class="text-lg font-bold text-slate-800">{{ quote.aseguradora }}</h3>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-3 border-b border-slate-200 bg-slate-50 font-semibold text-slate-600 border-r text-sm">Prima Total (con IVA)</td>
                                        <td v-for="(quote, idx) in quotes" :key="idx" class="p-3 border-b border-slate-200 text-center font-bold text-blue-600 text-lg border-r last:border-r-0">
                                            {{ quote.precioTotal }}
                                        </td>
                                    </tr>
                                    <template v-for="(cat, catIdx) in getUniqueCategories()" :key="catIdx">
                                        <tr>
                                            <td :colspan="quotes.length + 1" class="p-2 border-b-2 border-t-2 border-slate-300 bg-slate-200 text-slate-900 font-extrabold uppercase text-xs tracking-wider">
                                                {{ cat.name }}
                                            </td>
                                        </tr>
                                        <tr v-for="(feature, featIdx) in cat.features" :key="`${catIdx}-${featIdx}`" class="hover:bg-blue-50/50">
                                            <td class="p-3 border-b border-slate-200 bg-slate-50 font-semibold text-slate-600 border-r text-sm">{{ feature }}</td>
                                            <td v-for="(quote, qIdx) in quotes" :key="qIdx" class="p-3 border-b border-slate-200 text-center text-sm text-slate-800 border-r last:border-r-0">
                                                <template v-if="quote.categorias">
                                                    <span v-if="quote.categorias.find(c => c.nombre === cat.name)?.caracteristicas.find(f => f.nombre === feature)?.valor">
                                                        {{ quote.categorias.find(c => c.nombre === cat.name).caracteristicas.find(f => f.nombre === feature).valor }}
                                                    </span>
                                                    <span v-else class="italic text-slate-400">-</span>
                                                </template>
                                                <template v-else-if="quote.caracteristicas">
                                                    <span v-if="quote.caracteristicas.find(c => c.nombre === feature)?.valor">
                                                        {{ quote.caracteristicas.find(c => c.nombre === feature).valor }}
                                                    </span>
                                                    <span v-else class="italic text-slate-400">-</span>
                                                </template>
                                                <span v-else class="italic text-slate-400">-</span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-center mt-8">
                        <button @click="startNew" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition-all duration-200">
                            Nueva Comparación
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    * {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }
}
</style>
