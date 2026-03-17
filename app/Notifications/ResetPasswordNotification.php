<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;

class ResetPasswordNotification extends ResetPasswordBase implements ShouldQueue
{
    use Queueable;

    // Hereda toda la lógica de ResetPassword de Laravel, 
    // pero al implementar ShouldQueue y usar el trait Queueable, 
    // el envío se hará a través del sistema de colas.
}
