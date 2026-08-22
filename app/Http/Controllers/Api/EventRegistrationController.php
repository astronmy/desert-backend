<?php

namespace App\Http\Controllers\Api;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Invitations\SelfRegisterInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventRegistrationController extends Controller
{
    public function store(
        Request $request,
        Event $event,
        SelfRegisterInvitationService $register
    ): JsonResponse {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:50'],
            'id_type' => ['required', Rule::enum(DocumentType::class)],
            'selfie' => ['required', 'image', 'max:5120'],
        ]);

        $result = $register->register($event, $data, $request->file('selfie'));
        $invitation = $result['invitation'];

        return response()->json([
            'message' => 'Registro recibido. Queda pendiente de aprobación.',
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => [
                'id' => $invitation->event->id,
                'name' => $invitation->event->name,
                'init_date' => $invitation->event->init_date->toDateString(),
                'end_date' => $invitation->event->end_date->toDateString(),
                'type' => $invitation->event->type->value,
                'description' => $invitation->event->description,
                'short_description' => $invitation->event->short_description,
                'host' => $invitation->event->host,
                'image_url' => $invitation->event->imageUrl(),
                'mobile_image_url' => $invitation->event->mobileImageUrl(),
                'gallery' => $invitation->event->galleryUrls(),
            ],
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
