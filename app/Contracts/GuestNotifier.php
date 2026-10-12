<?php

namespace App\Contracts;

use App\Models\Invitation;

interface GuestNotifier
{
    /**
     * Push al registrarse en el evento. Sync; no debe lanzar.
     */
    public function registrationReceived(Invitation $invitation): void;

    /**
     * Push al aprobar la invitación. Sync; no debe lanzar.
     */
    public function invitationApproved(Invitation $invitation): void;

    /**
     * Push de bienvenida al registrar el ingreso. Sync; no debe lanzar.
     */
    public function welcomeToEvent(Invitation $invitation): void;
}
