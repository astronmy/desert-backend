<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Services\Accesses\RegisterInvitationAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class AccessController extends Controller
{
    public function store(Request $request, RegisterInvitationAccessService $service): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        try {
            $result = $service->register($data['code']);
        } catch (RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), (int) $e->getCode(), $data['code']);
        }

        /** @var Access $access */
        $access = $result['access'];

        return response()->json([
            'message' => 'Acceso registrado correctamente.',
            'access' => [
                'id' => $access->id,
                'invitation_code' => $access->invitation_code,
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

        $payload = match ($reason) {
            'not_found' => ['message' => 'Invitación no encontrada.'],
            'cancelled' => ['message' => 'La invitación está cancelada.'],
            'not_confirmed' => ['message' => 'La invitación aún no está confirmada.'],
            'already_accessed' => [
                'message' => 'Este invitado ya registró un acceso.',
                'accessed_at' => Access::query()
                    ->where('invitation_code', Str::upper(trim($code)))
                    ->first()
                    ?->accessed_at
                    ?->toIso8601String(),
            ],
            default => ['message' => 'No se pudo registrar el acceso.'],
        };

        return response()->json($payload, $status);
    }
}
