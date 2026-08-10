<?php

namespace App\Services\Accesses;

use App\Enums\InvitationStatus;
use App\Models\Access;
use App\Models\Invitation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RegisterInvitationAccessService
{
    /**
     * @return array{access: Access, invitation: Invitation}
     */
    public function register(string $code): array
    {
        $normalized = Str::upper(trim($code));

        $invitation = Invitation::query()
            ->with(['guest', 'event', 'access'])
            ->where('code', $normalized)
            ->first();

        if (! $invitation) {
            throw new RuntimeException('not_found', 404);
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            throw new RuntimeException('cancelled', 410);
        }

        if ($invitation->status !== InvitationStatus::Confirmed) {
            throw new RuntimeException('not_confirmed', 422);
        }

        if ($invitation->access) {
            throw new RuntimeException('already_accessed', 409);
        }

        try {
            $access = DB::transaction(function () use ($invitation) {
                $locked = Invitation::query()
                    ->with('guest')
                    ->whereKey($invitation->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (Access::query()->where('invitation_id', $locked->id)->exists()) {
                    throw new RuntimeException('already_accessed', 409);
                }

                return Access::create([
                    'invitation_id' => $locked->id,
                    'event_id' => $locked->event_id,
                    'invitation_code' => $locked->code,
                    'guest_first_name' => $locked->guest->first_name,
                    'guest_last_name' => $locked->guest->last_name,
                    'guest_document_number' => $locked->guest->document_number,
                    'guest_id_type' => $locked->guest->id_type->value,
                    'accessed_at' => now(),
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            throw new RuntimeException('already_accessed', 409);
        }

        $access->load('event');

        return [
            'access' => $access,
            'invitation' => $invitation,
        ];
    }
}
