<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEventRegistrationRequest;
use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use App\Services\Invitations\SelfRegisterInvitationService;
use Illuminate\Http\JsonResponse;

class EventRegistrationController extends Controller
{
    public function store(
        StoreEventRegistrationRequest $request,
        Event $event,
        SelfRegisterInvitationService $register
    ): JsonResponse {
        $result = $register->register($event, $request->validated(), $request->file('selfie'));
        $invitation = $result['invitation'];
        $invitation->loadMissing(['event.images', 'guest']);

        return response()->json([
            'message' => 'Registro recibido. Queda pendiente de aprobación.',
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => EventResource::make($invitation->event)->resolve(),
            'guest' => [
                'first_name' => $invitation->guest->first_name,
                'last_name' => $invitation->guest->last_name,
                'document_number' => $invitation->guest->document_number,
                'id_type' => $invitation->guest->id_type->value,
            ],
            'confirmed_at' => null,
            'selfie_url' => $invitation->selfieUrl(),
        ], 201);
    }
}
