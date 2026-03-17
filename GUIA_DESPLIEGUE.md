# Guía Definitiva de Despliegue - Canal360 (CRM & Sistema de Gestión)

Este documento describe el procedimiento requerido para ejecutar el proyecto tanto en un entorno local como en un servidor VPS. Su finalidad es garantizar un funcionamiento consistente, replicable y libre de errores.

---

## 1. Despliegue en un Computador Nuevo (Desarrollo Local)

Al clonar o descargar el proyecto en un equipo nuevo, se debe seguir el siguiente procedimiento desde la terminal.

### Requisitos previos:

Se requiere tener instalado en el equipo:

* PHP (v8.2 o superior)
* Composer
* Node.js (v18 o superior)
* Git

### Procedimiento de instalación:

**A. Instalación de dependencias del Backend (PHP)**
Ejecutar en la raíz del proyecto:

```bash
composer install
```

**B. Configuración del entorno (.env)**
Es necesario generar el archivo de configuración del entorno:

```bash
# En Windows (PowerShell/CMD):
copy .env.example .env
# En Linux/Mac:
cp .env.example .env
```

Posteriormente, generar la clave de la aplicación:

```bash
php artisan key:generate
```

**C. Preparación de la Base de Datos (SQLite)**
Dado que el sistema utiliza SQLite en entorno local, se debe verificar la existencia del archivo `database.sqlite` en la carpeta `database/`.

1. Acceder a la carpeta `database/`.
2. Si el archivo no existe, crearlo manualmente vacío (`database.sqlite`).

**D. Ejecución de migraciones**
Crear la estructura de la base de datos:

```bash
php artisan migrate
```

**E. Carga de información base del CRM (DatosCRM)**
En caso de requerir la importación de datos iniciales:

1. Ubicar el archivo `DatosCRM.xlsx` en la carpeta raíz del proyecto.
2. Ejecutar:

```bash
php artisan crm:import DatosCRM.xlsx
```

**F. Instalación de dependencias del Frontend (Vue 3 / JavaScript)**

```bash
npm install
```

**G. Ejecución del sistema (requiere dos terminales)**

Para habilitar la compilación en tiempo real:

```bash
npm run dev
```

En una segunda terminal, iniciar el servidor:

```bash
php artisan serve
```

El sistema estará disponible en:

```
http://127.0.0.1:8000
```

---

## 2. Despliegue en Servidor Producción (VPS)

Para aplicar cambios en el servidor, se debe acceder vía SSH y ubicarse en el directorio del proyecto (ejemplo: `/var/www/canal360`).

### Procedimiento:

**A. Actualización del código fuente**

```bash
git pull
```

**B. Actualización de dependencias**

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
```

**C. Ejecución de migraciones en producción**

```bash
php artisan migrate --force
```

*(El parámetro `--force` permite ejecutar migraciones en entorno de producción sin confirmación interactiva).*

**D. Compilación del Frontend (CRÍTICO)**
En entorno de producción no se debe utilizar `npm run dev`. Se debe compilar el proyecto:

```bash
npm run build
```

**E. Limpieza de caché del sistema**

```bash
php artisan optimize:clear
```

---

### 📂 Gestión del archivo Excel en el VPS (`DatosCRM.xlsx`)

En caso de requerir carga o actualización masiva de datos:

1. Subir el archivo `DatosCRM.xlsx` al directorio raíz del proyecto mediante FTP o gestor de archivos.
2. Ejecutar:

```bash
php artisan crm:import DatosCRM.xlsx
```

El proceso utiliza lógica de actualización (*Upsert*), lo que evita duplicados y permite insertar o actualizar registros según corresponda.

---

## 💡 Recomendaciones

* **Gestión de memoria en el VPS:**
  En caso de errores por límite de memoria durante importaciones:

  ```bash
  php -d memory_limit=512M artisan crm:import DatosCRM.xlsx
  ```

* **Gestión del archivo Excel:**
  No se recomienda incluir archivos de datos pesados en el repositorio. Estos deben transferirse directamente al servidor.

* **Respaldo en producción:**
  Antes de ejecutar migraciones, se recomienda realizar una copia de seguridad del archivo SQLite en producción.

---

El cumplimiento de este procedimiento permite que cualquier entorno reproduzca el sistema de manera confiable y en condiciones operativas consistentes.