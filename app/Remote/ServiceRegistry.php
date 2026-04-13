<?php

namespace App\Remote;

/**
 * Clase ServiceRegistry: Módulo Registry (Guía 6)
 * Gestiona el diccionario de nombres de servicios e IPs/Puertos.
 */
class ServiceRegistry
{
    private $storagePath;

    public function __construct()
    {
        // Usamos un archivo local para simular la persistencia del Registry
        $this->storagePath = storage_path('app/remote_registry.json');
        
        if (!file_exists($this->storagePath)) {
            file_put_contents($this->storagePath, json_encode([
                // Valores por defecto
                'RegistryServer' => '127.0.0.1:9000'
            ]));
        }
    }

    /**
     * Operación Bind (Servidor): Publica un servicio.
     */
    public function bind(string $serviceName, string $endpoint): bool
    {
        $registry = json_decode(file_get_contents($this->storagePath), true);
        $registry[$serviceName] = $endpoint;
        return (bool) file_put_contents($this->storagePath, json_encode($registry));
    }

    /**
     * Operación Lookup (Cliente): Busca la dirección de un servicio.
     */
    public function lookup(string $serviceName): string
    {
        $registry = json_decode(file_get_contents($this->storagePath), true);
        
        if (!isset($registry[$serviceName])) {
            throw new \Exception("Error: El servicio '$serviceName' no está registrado en el Registry.");
        }

        return $registry[$serviceName];
    }

    /**
     * Lista todos los servicios registrados (Para inspección).
     */
    public function listServices(): array
    {
        return json_decode(file_get_contents($this->storagePath), true);
    }
}
