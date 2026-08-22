<?php

namespace App\Services\Invitations;

use App\Enums\DocumentType;
use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SelfRegisterInvitationService
{
    public function __construct(
        private readonly InvitationCodeGenerator $codeGenerator
    ) {}

    /**
     * @param  array{first_name: string, last_name: string, document_number: string, id_type: string}  $data
     * @return array{invitation: Invitation, created: bool}
     */
    public function register(Event $event, array $data, UploadedFile $selfie): array
    {
        if ($event->end_date->lt(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'event' => 'El evento ya finalizó.',
            ]);
        }

        $documentNumber = preg_replace('/\D+/', '', $data['document_number']) ?: $data['document_number'];
        $idType = DocumentType::from($data['id_type']);

        return DB::transaction(function () use ($event, $data, $selfie, $documentNumber, $idType) {
            $guest = Guest::query()->updateOrCreate(
                [
                    'id_type' => $idType,
                    'document_number' => $documentNumber,
                ],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                ]
            );

            $existing = Invitation::query()
                ->where('event_id', $event->id)
                ->where('guest_id', $guest->id)
                ->first();

            if ($existing) {
                if ($existing->status === InvitationStatus::Cancelled) {
                    throw ValidationException::withMessages([
                        'document_number' => 'Tu registro para este evento fue rechazado.',
                    ]);
                }

                throw new HttpResponseException(response()->json([
                    'message' => 'Ya tenés un registro para este evento.',
                    'code' => $existing->code,
                    'status' => $existing->status->value,
                ], 409));
            }

            $invitation = Invitation::create([
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'code' => $this->codeGenerator->generate(),
                'status' => InvitationStatus::Pending,
                'confirmed_at' => null,
            ]);

            $path = $selfie->store('invitations/'.$invitation->id, 'public');
            $invitation->update(['selfie_path' => $path]);

            return [
                'invitation' => $invitation->fresh()->load(['event.images', 'guest']),
                'created' => true,
            ];
        });
    }
}
