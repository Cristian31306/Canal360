# Guía Definitiva de Despliegue - Canal360 (CRM & Sistema de Gestión)

Esta guía paso a paso te explicará qué debes hacer desde que abres el proyecto en **un computador nuevo** o cuando vayas a **subir las actualizaciones a tu servidor VPS**. El objetivo es que el proyecto funcione de forma idéntica y sin errores.

---

## 1. Despliegue en un Computador Nuevo (Desarrollo Local)

Si descargas el proyecto de GitHub en un equipo nuevo donde apenas vas a trabajar, sigue estos pasos rigurosamente en la terminal o consola:

### Requisitos previos:
Asegúrate de tener instalado en ese PC:
- PHP (v8.2 o superior)
- Composer
- Node.js (v18 o superior)
- Git

### Paso a paso para levantar el proyecto:

**A. Instalar dependencias del Backend (PHP)**
Abre la consola en la carpeta del proyecto y ejecuta:
```bash
composer install
```

**B. Configurar el Entorno (.env)**
Laravel necesita un archivo oculto con la configuración de la base de datos y contraseñas.
Copia el archivo de ejemplo para crear el tuyo:
```bash
# En Windows (PowerShell/CMD):
copy .env.example .env
# En Linux/Mac:
cp .env.example .env
```
Luego genera la llave de seguridad del proyecto:
```bash
php artisan key:generate
```

**C. Preparar la Base de Datos (SQLite)**
Dado que el proyecto usa SQLite local, crea el archivo físico de base de datos dentro de la carpeta `database`:
1. Ve a la carpeta `database/` y busca si existe el archivo `database.sqlite`.
2. Si no existe, créalo manualmente vacío. (Click derecho -> Nuevo archivo: `database.sqlite`).

**D. Migrar las Tablas a la Base de Datos**
Aplica toda la "arquitectura" que hemos creado (Tablas de Riesgos, Minerales, CRM, etc.):
```bash
php artisan migrate
```

**E. Restaurar la información estática del CRM (DatosCRM)**
Si este PC nuevo necesita tener la data base de los títulos mineros de "DatosCRM.xlsx":
1. Asegúrate de que tu archivo `DatosCRM.xlsx` esté pegado en la **carpeta raíz** (Canal360).
2. Corre el script mágico antiduplicados:
```bash
php artisan crm:import DatosCRM.xlsx
```

**F. Instalar dependencias del Frontend (Vue 3 / JavaScript)**
Ahora instala los paquetes visuales del proyecto:
```bash
npm install
```

**G. Levantar el sistema (Dos pestañas de consola requeridas)**

Para ver los cambios visuales en vivo (Hot Reload), abre una pestaña de consola y ejecuta:
```bash
npm run dev
```

Abre **otra pestaña de consola** al mismo tiempo para encender el servidor PHP y ejecuta:
```bash
php artisan serve
```

¡Listo! Ya puedes ir a `http://127.0.0.1:8000` en tu navegador.

---

## 2. Despliegue en Servidor Producción (VPS)

Cuando haces cambios en local y subes a GitHub (Push), tu servidor no se entera mágicamente. Para aplicar todos esos cambios (Como el nuevo Módulo CRM) en el VPS, el escenario es el siguiente:

### Pasos en el VPS:

Entra a la consola de comandos de tu VPS por SSH o mediante la terminal que tengas habilitada, ve a la carpeta de tu sistema (ej: `cd /var/www/canal360`) y ejecuta exactamente en este orden:

**A. Traer el nuevo código**
Descarga lo nuevo de GitHub al servidor:
```bash
git pull
```

**B. Instalar novedades de Backend y Frontend**
Como quizá añadimos librerías nuevas (como la de Excel que usamos para el CRM), debes actualizar los "motores":
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
```

**C. Aplicar las nuevas estructuras de Base de Datos**
Si en las mejoras creamos tablas nuevas (ej. `titulos_360`), debes incluirlas corriendo:
```bash
php artisan migrate --force
```
*(El `--force` le dice al VPS "Sí, estoy seguro, hazlo" ya que está en producción, no lo dudes).*

**D. Compilar el Frontend (CRÍTICO 🚨)**
En un VPS nunca usamos `npm run dev`. El código del servidor necesita ser "comprimido" al máximo para que tus clientes lo vean súper rápido. Haz que Vite compile todo el CSS y Vue.js nuevo:
```bash
npm run build
```

**E. Limpiar caché del Sistema**
Esto asegura que el servidor olvide cualquier "versión vieja" que tenga guardada y empiece a mostrar de inmediato lo nuevo:
```bash
php artisan optimize:clear
```

### 📂 ¿Qué hacer con el Excel en el VPS? (`DatosCRM.xlsx`)
Si la base de datos de tu VPS no tenía esos 13,000 registros, o si simplemente tienes un Excel con actualizaciones y correcciones masivas semanales:

1. **Sube el Excel:** Ya sea usando un programa FTP (FileZilla) o arrastrándolo a tu panel de archivos en la consola del VPS, pon el arcvhivo `DatosCRM.xlsx` en la carpeta principal de tu aplicación web (donde están archivos como `composer.json` y `package.json`).
2. **Corre la magia:** Desde la terminal en el VPS ejecuta:
   ```bash
   php artisan crm:import DatosCRM.xlsx
   ```
Recuerda, la lógica de *Upsert* **no te va a dañar nada que ya tengas**, solo creará lo nuevo o ajustará diferencias en los existentes sin triplicar registros.

---

## 💡 Recomendaciones de Oro
- **Para que NO te de error de memoria en VPS:** Importar miles de registros en producción gasta "memoria RAM" del servidor. Si ves un error de RAM, añade al comando el extra de memoria:
  `php -d memory_limit=512M artisan crm:import DatosCRM.xlsx`
- **¿Subir el Excel a GitHub? NO.** Es mala práctica subir bases de datos pesadas en el código fuente. Pásalo por FTP directamente si es tu VPS. Por eso el archivo `.gitignore` no suele permitir las bases de datos. 
- **Backups antes de tocar producción:** Antes de darle un `php artisan migrate` en el VPS, es sano hacer una copia rápida de tu archivo SQLite de producción.

Si sigues esta guía al pie de la letra, cualquier persona o desarrollador que contrates en el futuro sabrá exactamente cómo levantar tu sistema en 5 minutos.
