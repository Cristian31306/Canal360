<?php

/**
 * Script de Asistencia para Despliegue en VPS - Canal360
 * Este script automatiza los pasos de configuración inicial en el servidor.
 */

echo "--- Iniciando Asistente de Despliegue Canal360 ---\n";

function run($command) {
    echo "> Ejecutando: $command\n";
    $output = [];
    $resultCode = 0;
    exec($command, $output, $resultCode);
    foreach ($output as $line) {
        echo "  $line\n";
    }
    return $resultCode === 0;
}

// 1. Verificar .env
if (!file_exists('.env')) {
    echo "[!] Error: No se encontró el archivo .env. Por favor, crea uno basado en .env.example.\n";
    exit(1);
}

// 2. Ejecutar comandos de Laravel
$commands = [
    'php artisan key:generate --force',
    'php artisan migrate --force',
    'php artisan storage:link',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'php artisan event:cache',
    'php artisan optimize',
    'php artisan clear-compiled'
];

foreach ($commands as $cmd) {
    if (!run($cmd)) {
        echo "[!] Falló el comando: $cmd\n";
    }
}

echo "\n--- Despliegue Completado con Éxito ---\n";
echo "Tu aplicación debería estar lista en el dominio configurado.\n";
