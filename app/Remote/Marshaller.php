<?php

namespace App\Remote;

/**
 * Clase Marshaller: Proceso de Marshaling/Unmarshaling (Guía 5)
 * Se encarga de convertir objetos complejos en datos serializados y viceversa.
 */
class Marshaller
{
    /**
     * Marshaling: Convierte un objeto en una cadena para su transporte.
     */
    public static function marshal($object): string
    {
        $data = [
            'class' => get_class($object),
            'payload' => serialize($object)
        ];
        
        return json_encode($data);
    }

    /**
     * Unmarshaling: Reconstruye el objeto original a partir de la cadena recibida.
     */
    public static function unmarshal(string $marshaled): object
    {
        $data = json_decode($marshaled, true);
        
        if (!isset($data['class']) || !isset($data['payload'])) {
            throw new \Exception("Formato de datos inválido para Unmarshaling.");
        }

        // Reconstrucción del objeto preservando su tipo de clase
        return unserialize($data['payload']);
    }
}
