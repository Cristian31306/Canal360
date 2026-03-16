<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

trait AuditoriaHelper
{
    /**
     * Registra una acción en la tabla de auditoría.
     *
     * @param string $accion Descripción de la acción (Ej: 'Crear Cliente')
     * @param string $entidad Nombre de la entidad afectada (Ej: 'Cliente')
     * @param int|null $id ID de la entidad afectada
     * @param array|null $detalles Datos adicionales o cambios realizados
     * @return void
     */
    public function registrarAuditoria(string $accion, string $entidad, $id = null, array $nuevo = null, array $anterior = null)
    {
        $detalles = $nuevo;
        
        // Si hay datos anteriores, estructuramos para mostrar cambios
        if ($anterior !== null) {
            $detalles = [
                'antes' => $anterior,
                'despues' => $nuevo
            ];
        }

        Auditoria::create([
            'usuario_id' => Auth::id(),
            'accion' => $accion,
            'entidad_afectada' => $entidad,
            'entidad_id' => $id ?? 0,
            'detalles_json' => $detalles,
        ]);
    }
}
