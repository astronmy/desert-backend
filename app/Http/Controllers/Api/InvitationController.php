<?php

namespace App\Http\Controllers\Api;

use App\Enums\DocumentType;
use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function show(string $code): JsonResponse
    {
        $invitation = Invitation::query()
            ->with(['event', 'guest'])
            ->where('code', Str::upper(trim($code)))
            ->first();

        if (! $invitation) {
            return response()->json(['message' => 'Invitación no encontrada.'], 404);
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            return response()->json(['message' => 'La invitación está cancelada.'], 410);
        }

        return response()->json([
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => [
                'id' => $invitation->event->id,
                'name' => $invitation->event->name,
                'init_date' => $invitation->event->init_date->toDateString(),
                'end_date' => $invitation->event->end_date->toDateString(),
                'type' => $invitation->event->type->value,
            ],
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
            ->with(['event', 'guest', 'access'])
            ->where('code', Str::upper(trim($code)))
            ->first();

        if (! $invitation) {
            return response()->json(['message' => 'Invitación no encontrada.'], 404);
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            return response()->json(['message' => 'La invitación está cancelada.'], 410);
        }

        $hasEntered = $invitation->access !== null;

        return response()->json([
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'event' => [
                'id' => $invitation->event->id,
                'name' => $invitation->event->name,
                'init_date' => $invitation->event->init_date->toDateString(),
                'end_date' => $invitation->event->end_date->toDateString(),
                'type' => $invitation->event->type->value,
            ],
            'guest' => [
                'first_name' => $invitation->guest->first_name,
                'last_name' => $invitation->guest->last_name,
                'document_number' => $invitation->guest->document_number,
                'id_type' => $invitation->guest->id_type->value,
            ],
            'confirmed_at' => $invitation->confirmed_at?->toIso8601String(),
            'selfie_url' => $invitation->selfieUrl(),
            'access' => [
                'has_entered' => $hasEntered,
                'accessed_at' => $invitation->access?->accessed_at?->toIso8601String(),
            ],
        ]);
    }

    public function confirm(Request $request, string $code): JsonResponse
    {
        $invitation = Invitation::query()
            ->with('guest')
            ->where('code', Str::upper(trim($code)))
            ->first();

        if (! $invitation) {
            return response()->json(['message' => 'Invitación no encontrada.'], 404);
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            return response()->json(['message' => 'La invitación está cancelada.'], 410);
        }

        if ($invitation->status === InvitationStatus::Confirmed) {
            return response()->json(['message' => 'La invitación ya fue confirmada.'], 409);
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:50'],
            'id_type' => ['required', Rule::enum(DocumentType::class)],
            'selfie' => ['required', 'image', 'max:5120'],
        ]);

        $documentNumber = preg_replace('/\D+/', '', $data['document_number']) ?: $data['document_number'];

        if (
            $invitation->guest->document_number !== $documentNumber
            || $invitation->guest->id_type->value !== $data['id_type']
        ) {
            return response()->json([
                'message' => 'El documento no coincide con la invitación.',
            ], 422);
        }

        DB::transaction(function () use ($invitation, $data, $documentNumber, $request) {
            $invitation->guest->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_number' => $documentNumber,
                'id_type' => $data['id_type'],
            ]);

            $path = $request->file('selfie')->store(
                'invitations/'.$invitation->id,
                'public'
            );

            $invitation->update([
                'status' => InvitationStatus::Confirmed,
                'selfie_path' => $path,
                'confirmed_at' => now(),
            ]);
        });

        $invitation->refresh()->load(['event', 'guest']);

        return response()->json([
            'message' => 'Invitación confirmada correctamente.',
            'code' => $invitation->code,
            'status' => $invitation->status->value,
            'confirmed_at' => $invitation->confirmed_at?->toIso8601String(),
            'selfie_url' => $invitation->selfieUrl(),
        ]);
    }
}
