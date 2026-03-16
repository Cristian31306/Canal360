<?php

/**
 * Script de Actualización y Despliegue Canal360
 * Este script automatiza todos los pasos necesarios para que el VPS esté al día.
 */

echo "\n🚀 --- INICIANDO ACTUALIZACIÓN CANAL360 ---\n";

/**
 * Ejecuta un comando y muestra el progreso de forma elegante
 */
function ejecutar($comando, $descripcion) {
    echo "\n🔹 [$descripcion]...\n";
    echo "> $comando\n";
    
    $output = [];
    $resultCode = 0;
    exec($comando, $output, $resultCode);
    
    foreach ($output as $line) {
        echo "  $line\n";
    }
    
    if ($resultCode === 0) {
        echo "✅ Completado con éxito.\n";
        return true;
    } else {
        echo "❌ Error al ejecutar: $comando\n";
        return false;
    }
}

// 1. Sincronizar Código con Git (Sugerido pero requiere estar en un repo)
ejecutar('git pull origin main', 'Bajando últimos cambios de Git');

// 2. Comandos Vitales de Laravel
$pasos = [
    ['cmd' => 'composer install --no-dev --optimize-autoloader', 'desc' => 'Instalando dependencias de PHP (Composer)'],
    ['cmd' => 'php artisan key:generate --force', 'desc' => 'Generando llave de seguridad'],
    ['cmd' => 'php artisan migrate --force', 'desc' => 'Actualizando tablas de base de datos'],
    ['cmd' => 'php artisan db:seed --class=LandingPageAgenciaSeeder --force', 'desc' => 'Sincronizando textos comerciales de la Agencia de Seguros'],
    ['cmd' => 'php artisan storage:link', 'desc' => 'Activando enlace de fotos/archivos'],
    ['cmd' => 'php artisan cache:clear', 'desc' => 'Limpiando caché de la aplicación'],
    ['cmd' => 'php artisan view:clear', 'desc' => 'Limpiando caché de vistas'],
    ['cmd' => 'php artisan config:cache', 'desc' => 'Optimizando configuración'],
    ['cmd' => 'php artisan route:cache', 'desc' => 'Optimizando rutas'],
    ['cmd' => 'php artisan view:cache', 'desc' => 'Optimizando vistas'],
    ['cmd' => 'php artisan optimize', 'desc' => 'Optimización general de Laravel'],
];

foreach ($pasos as $paso) {
    ejecutar($paso['cmd'], $paso['desc']);
}

// 3. Comandos de Frontend (Si es necesario compilar en el VPS)
// echo "\n⚙️ ¿Deseas compilar el frontend? (Solo si tienes Node.js en el VPS)\n";
// ejecutar('npm install && npm run build', 'Compilando activos (Vite/Vue)');

echo "\n✨ --- ¡TODO LISTO! EL SISTEMA HA SIDO ACTUALIZADO --- ✨\n";
echo "Recuerda revisar que el .env tenga los datos correctos.\n\n";
