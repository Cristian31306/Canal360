<?php

use Illuminate\Support\Facades\Mail;

try {
    echo "Intentando enviar correo de prueba...\n";
    Mail::raw('Prueba de funcionamiento de correo de Canal360', function ($message) {
        $message->to('cristian@gmail.com') // Reemplazar con un correo real si es necesario o dejar así solo para ver el error
                ->subject('Prueba de Correo');
    });
    echo "Correo enviado (según Laravel).\n";
} catch (\Exception $e) {
    echo "ERROR al enviar correo:\n";
    echo $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " Lín: " . $e->getLine() . "\n";
    if ($e->getPrevious()) {
        echo "Error previo: " . $e->getPrevious()->getMessage() . "\n";
    }
}
