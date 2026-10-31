<?php

namespace App\Http\Controllers\Api;

use App\Enums\EventType;
use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateInvitationFirebaseTokenRequest;
use App\Http\Resources\Api\EventResource;
use App\Models\Invitation;
use App\Services\Invitations\UpdateInvitationFirebaseTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function show(string $code): JsonResponse
    {
        $code = Str::upper(trim($code));

        $invitation = Invitation::query()
            ->with(['event.images', 'guest'])
            ->where('code', $code)
            ->first();

        if (! $invitation) {
            return response()->json($this->mockInvitation($code));
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            return response()->json(['message' => 'La invitación está cancelada.'], 410);
        }

        return response()->json([
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => EventResource::make($invitation->event)->resolve(),
            'guest' => [
                'first_name' => $invitation->guest->first_name,
                'last_name' => $invitation->guest->last_name,
                'document_number' => $invitation->guest->document_number,
                'id_type' => $invitation->guest->id_type->value,
            ],
            'confirmed_at' => $invitation->confirmed_at?->toIso8601String(),
            'selfie_url' => $invitation->selfieUrl(),
        ]);
    }

    /**
     * Consulta para scanner/puerta: datos + selfie + si ingresó y cuándo.
     */
    public function entry(string $code): JsonResponse
    {
        $invitation = Invitation::query()
            ->with(['event.images', 'guest', 'access'])
            ->where('code', Str::upper(trim($code)))
            ->first();

        if (! $invitation) {
            return response()->json(['message' => 'Invitación no encontrada.'], 404);
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            return response()->json(['message' => 'La invitación está cancelada.'], 410);
        }

        if ($invitation->status !== InvitationStatus::Confirmed) {
            return response()->json(['message' => 'La invitación aún no está aprobada.'], 422);
        }

        $access = $invitation->access;
        $isComplete = $access?->isComplete() ?? false;

        return response()->json([
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => EventResource::make($invitation->event)->resolve(),
            'guest' => [
                'first_name' => $invitation->guest->first_name,
                'last_name' => $invitation->guest->last_name,
                'document_number' => $invitation->guest->document_number,
                'id_type' => $invitation->guest->id_type->value,
            ],
            'confirmed_at' => $invitation->confirmed_at?->toIso8601String(),
            'selfie_url' => $invitation->selfieUrl(),
            'access' => [
                'has_entered' => $isComplete,
                'accessed_at' => $access?->accessed_at?->toIso8601String(),
                'entrada_at' => $access?->entrada_at?->toIso8601String(),
                'salon_at' => $access?->salon_at?->toIso8601String(),
                'has_entrada' => $access?->hasEntrada() ?? false,
                'has_salon' => $access?->hasSalon() ?? false,
                'is_complete' => $isComplete,
            ],
        ]);
    }

    /**
     * Respuesta simulada cuando el código no existe (en lugar de un 404).
     *
     * @return array<string, mixed>
     */
    private function mockInvitation(string $code): array
    {
        return [
            'code' => $code,
            'status' => InvitationStatus::Pending->value,
            'event' => [
                'id' => 12,
                'name' => 'Cumpleaños de 15',
                'init_date' => now()->toDateString(),
                'type' => EventType::Birthday->value,
            ],
            'guest' => [
                'first_name' => '***',
                'last_name' => '***',
            ],
        ];
    }

    public function updateFirebaseToken(
        UpdateInvitationFirebaseTokenRequest $request,
        string $code,
        UpdateInvitationFirebaseTokenService $tokens
    ): JsonResponse {
        $invitation = $tokens->update(
            $code,
            $request->validated('firebase_token'),
            $request->validated('device_platform'),
            $request->validated('device_app_version'),
        );

        return response()->json([
            'message' => 'Token de Firebase actualizado.',
            'code' => $invitation->code,
        ]);
    }

    public function confirm(string $code): JsonResponse
    {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
}
