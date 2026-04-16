<?php

/**
 * Script de Actualización y Despliegue Canal360
 * Este script automatiza todos los pasos necesarios para que el VPS esté al día.
 * Uso: php actualizar.php
 */

function ejecutar($comando)
{
    echo "\n\033[32m[EJECUTANDO]\033[0m: $comando\n";
    passthru($comando . ' 2>&1');
}

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[34m    INICIANDO ACTUALIZACIÓN - CANAL360              \033[0m\n";
echo "\033[34m====================================================\033[0m\n";

// 1. Entrar en modo mantenimiento
echo "\n\033[33mEntrando en modo mantenimiento...\033[0m\n";
ejecutar('php artisan down');

// 2. Sincronizar Código con Git
echo "\n\033[33mObteniendo últimos cambios de Git...\033[0m\n";
ejecutar('git pull origin main');

// 3. Instalando dependencias de PHP (Composer)
echo "\n\033[33mInstalando dependencias de PHP (Composer)...\033[0m\n";
ejecutar('composer install --no-dev --optimize-autoloader');

// 4. Actualizando tablas de base de datos
echo "\n\033[33mEjecutando migraciones de base de datos...\033[0m\n";
ejecutar('php artisan migrate --force');

// 5. Sincronizando textos comerciales (Seeders específicos)
echo "\n\033[33mSincronizando textos comerciales y configuraciones...\033[0m\n";
ejecutar('php artisan db:seed --class=LandingPageAgenciaSeeder --force');
ejecutar('php artisan db:seed --class=SettingSeeder --force');

// 6. Activando enlace de fotos/archivos
echo "\n\033[33mVerificando enlace simbólico de storage...\033[0m\n";
ejecutar('php artisan storage:link');

// 7. Compilando activos (Frontend) - Solo si Node.js está disponible
echo "\n\033[33mInstalando dependencias de JS y compilando assets...\033[0m\n";
ejecutar('npm install && npm run build');

// 8. Limpieza y Optimización de Caché
echo "\n\033[33mOptimizando caché y rendimiento de Laravel...\033[0m\n";
ejecutar('php artisan optimize:clear');
ejecutar('php artisan config:cache');
ejecutar('php artisan route:cache');
ejecutar('php artisan view:cache');

// 9. Salir de mantenimiento
echo "\n\033[33mSaliendo del modo mantenimiento...\033[0m\n";
ejecutar('php artisan up');

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[32m       ¡ACTUALIZACIÓN COMPLETADA CON ÉXITO!        \033[0m\n";
echo "\033[34m====================================================\033[0m\n\n";
echo "Recuerda revisar que el archivo .env tenga los datos correctos.\n\n";
