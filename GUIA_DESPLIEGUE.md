# 🚀 Guía Definitiva de Despliegue - Canal360 (CRM & Sistema de Gestión)

Este documento describe el procedimiento requerido para ejecutar el proyecto tanto en un entorno local como en un servidor VPS. Su finalidad es garantizar un funcionamiento consistente, replicable y libre de errores.

---

## 1. 💻 Desarrollo Local (Computador Nuevo)

Al clonar o descargar el proyecto en un equipo nuevo, siga este procedimiento:

### 📋 Requisitos Previos
> [!IMPORTANT]
> Asegúrese de tener instaladas las siguientes versiones mínimas:
> - **PHP:** v8.2+
> - **Composer:** v2.0+
> - **Node.js:** v18+
> - **Git:** v2.x+

### 🛠️ Procedimiento de Instalación

1.  **Instalar Dependencias de Backend:**
    ```bash
    composer install
    ```

2.  **Configurar Entorno (.env):**
    ```bash
    copy .env.example .env    # Windows
    php artisan key:generate
    ```

3.  **Preparar Base de Datos (SQLite):**
    Asegúrese de que exista el archivo `database/database.sqlite`. Si no existe:
    ```bash
    touch database/database.sqlite
    php artisan migrate
    ```

4.  **Instalar Dependencias de Frontend:**
    ```bash
    npm install
    ```

5.  **Ejecución:**
    Use `npm run dev` para compilación y `php artisan serve` para el servidor.

---

## 2. 🌐 Despliegue en Producción (VPS)

> [!WARNING]
> Nunca ejecute `npm run dev` en producción. Utilice siempre el comando de compilación final.

### ⚙️ Comandos de Actualización Rápidos
```bash
git pull
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize:clear
```

---

## 🔄 Sincronización Automática (Local ➡️ VPS)

El proyecto incluye un script de sincronización potente: `sync_db.ps1`.

> [!CAUTION]
> El script `sync_db.ps1` **sobrescribirá** los datos del servidor con los datos de su entorno local. Úselo solo para migraciones controladas.

1.  Abra PowerShell en la raíz del proyecto.
2.  Ejecute: `.\sync_db.ps1`

---

## 📂 Importación Masiva (Excel)

Si necesita cargar datos desde `DatosCRM.xlsx`:

1.  Cargue el archivo a la raíz del proyecto.
2.  Ejecute:
    ```bash
    php artisan crm:import DatosCRM.xlsx
    ```
> [!TIP]
> Si encuentra errores de memoria en el VPS, use:
> `php -d memory_limit=512M artisan crm:import DatosCRM.xlsx`