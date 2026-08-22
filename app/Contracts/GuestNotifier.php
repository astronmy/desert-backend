<?php

namespace App\Contracts;

use App\Models\Invitation;

interface GuestNotifier
{
    /**
     * Notifica al invitado que su registro fue aprobado.
     * Stub OneSignal: no envía push hasta configurar el provider real.
     */
    public function invitationApproved(Invitation $invitation): void;
}
