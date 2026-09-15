<?php

namespace App\Http\Controllers\Api;

use App\Enums\AccessCheckpoint;
use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Services\Accesses\RegisterInvitationAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class AccessController extends Controller
{
    public function store(Request $request, RegisterInvitationAccessService $service): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32'],
            'checkpoint' => ['required', 'string', Rule::enum(AccessCheckpoint::class)],
        ]);

        $checkpoint = AccessCheckpoint::from($data['checkpoint']);

        try {
            $result = $service->register($data['code'], $checkpoint);
        } catch (RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), (int) $e->getCode(), $data['code']);
        }

        /** @var Access $access */
        $access = $result['access'];

        $message = $access->isComplete()
            ? 'Acceso completo registrado correctamente.'
            : 'Checkpoint registrado correctamente.';

        return response()->json([
            'message' => $message,
            'access' => [
                'id' => $access->id,
                'invitation_code' => $access->invitation_code,
                'checkpoint' => $checkpoint->value,
                'entrada_at' => $access->entrada_at?->toIso8601String(),
                'salon_at' => $access->salon_at?->toIso8601String(),
                'is_complete' => $access->isComplete(),
                'accessed_at' => $access->accessed_at->toIso8601String(),
                'event' => [
                    'id' => $access->event_id,
                    'name' => $access->event?->name,
                ],
                'guest' => [
                    'first_name' => $access->guest_first_name,
                    'last_name' => $access->guest_last_name,
                    'document_number' => $access->guest_document_number,
                    'id_type' => $access->guest_id_type,
                ],
            ],
        ], 201);
    }

    private function errorResponse(string $reason, int $status, string $code): JsonResponse
    {
        $status = $status > 0 ? $status : 400;

        $access = Access::query()
            ->where('invitation_code', Str::upper(trim($code)))
            ->first();

        $payload = match ($reason) {
            'not_found' => ['message' => 'Invitación no encontrada.'],
            'cancelled' => ['message' => 'La invitación está cancelada.'],
            'not_confirmed' => ['message' => 'La invitación aún no está confirmada.'],
            'already_entrada' => [
                'message' => 'Este invitado ya registró el acceso de entrada.',
                'checkpoint' => AccessCheckpoint::Entrada->value,
                'entrada_at' => $access?->entrada_at?->toIso8601String(),
                'salon_at' => $access?->salon_at?->toIso8601String(),
                'is_complete' => $access?->isComplete() ?? false,
                'accessed_at' => $access?->accessed_at?->toIso8601String(),
            ],
            'already_salon' => [
                'message' => 'Este invitado ya registró el acceso de salón.',
                'checkpoint' => AccessCheckpoint::Salon->value,
                'entrada_at' => $access?->entrada_at?->toIso8601String(),
                'salon_at' => $access?->salon_at?->toIso8601String(),
                'is_complete' => $access?->isComplete() ?? false,
                'accessed_at' => $access?->accessed_at?->toIso8601String(),
            ],
            'already_accessed' => [
                'message' => 'Este invitado ya registró un acceso.',
                'accessed_at' => $access?->accessed_at?->toIso8601String(),
            ],
            default => ['message' => 'No se pudo registrar el acceso.'],
        };

        return response()->json($payload, $status);
    }
}
