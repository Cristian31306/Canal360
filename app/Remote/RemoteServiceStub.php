<?php

namespace App\Remote;

/**
 * Clase RemoteServiceStub: Implementación del Proxy (Guia 5)
 * Este objeto simula ser el servicio real y oculta la lógica de red.
 */
class RemoteServiceStub
{
    private $serviceName;
    private $registry;

    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->registry = new ServiceRegistry();
    }

    /**
     * Interceptor de llamadas: Aquí ocurre la "magia" del RPC/RMI.
     * Al llamar a $stub->guardar($objeto), este método lo intercepta.
     */
    public function __call($method, $args)
    {
        // 1. Localización mediante el Registry (Operación Lookup - Guía 6)
        $endpoint = $this->registry->lookup($this->serviceName);

        // 2. Transparencia: El usuario no ve que estamos haciendo un "envío"
        // echo "[Stub] Simulando llamada remota al método '$method' en $endpoint...\n";

        // 3. Marshaling de los argumentos (Guía 5)
        $marshaledArgs = array_map(function ($arg) {
            return is_object($arg) ? Marshaller::marshal($arg) : $arg;
        }, $args);

        // 4. Lógica de Red (Simulada mediante una petición local/mock)
        // En una implementación real, aquí se usaría un Socket o Guzzle.
        $response = $this->simulateNetworkCall($endpoint, $method, $marshaledArgs);

        return $response;
    }

    private function simulateNetworkCall($endpoint, $method, $data)
    {
        // Simulamos la recepción en el servidor
        // echo "[Network] Transfiriendo datos a $endpoint...\n";

        // Unmarshaling en el "servidor" (Guía 5)
        $receivedArgs = array_map(function ($item) {
            if (is_string($item) && strpos($item, '{"class":') === 0) {
                return Marshaller::unmarshal($item);
            }
            return $item;
        }, $data);

        // Simulación de éxito
        return "Éxito: Método '$method' ejecutado en el servidor remoto para los datos enviados.";
    }
}
