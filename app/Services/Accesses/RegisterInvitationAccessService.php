<?php

namespace App\Services\Accesses;

use App\Contracts\GuestNotifier;
use App\Enums\AccessCheckpoint;
use App\Enums\InvitationStatus;
use App\Models\Access;
use App\Models\Invitation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RegisterInvitationAccessService
{
    public function __construct(
        private readonly GuestNotifier $notifier
    ) {}

    /**
     * @return array{access: Access, invitation: Invitation, checkpoint: AccessCheckpoint, completed_now: bool}
     */
    public function register(string $code, AccessCheckpoint $checkpoint): array
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

        $column = $checkpoint->timestampColumn();
        $completedNow = false;

        try {
            $access = DB::transaction(function () use ($invitation, $checkpoint, $column, &$completedNow) {
                return $this->registerCheckpoint($invitation->id, $checkpoint, $column, $completedNow);
            });
        } catch (UniqueConstraintViolationException) {
            // Race on first create: retry as an update on the row the other request inserted.
            $access = DB::transaction(function () use ($invitation, $checkpoint, $column, &$completedNow) {
                return $this->registerCheckpoint($invitation->id, $checkpoint, $column, $completedNow);
            });
        }

        $access->load('event');
        $invitation->setRelation('access', $access);
        $invitation->loadMissing('event');

        if ($completedNow) {
            $this->notifier->welcomeToEvent($invitation);
        }

        return [
            'access' => $access,
            'invitation' => $invitation,
            'checkpoint' => $checkpoint,
            'completed_now' => $completedNow,
        ];
    }

    /**
     * @param-out bool $completedNow
     */
    private function registerCheckpoint(
        int $invitationId,
        AccessCheckpoint $checkpoint,
        string $column,
        bool &$completedNow
    ): Access {
        $locked = Invitation::query()
            ->with(['guest', 'access'])
            ->whereKey($invitationId)
            ->lockForUpdate()
            ->firstOrFail();

        $existing = Access::query()
            ->where('invitation_id', $locked->id)
            ->lockForUpdate()
            ->first();

        if ($existing && $existing->{$column} !== null) {
            throw new RuntimeException($checkpoint->alreadyReason(), 409);
        }

        $now = now();

        if (! $existing) {
            $payload = [
                'invitation_id' => $locked->id,
                'event_id' => $locked->event_id,
                'invitation_code' => $locked->code,
                'guest_first_name' => $locked->guest->first_name,
                'guest_last_name' => $locked->guest->last_name,
                'guest_document_number' => $locked->guest->document_number,
                'guest_id_type' => $locked->guest->id_type->value,
                'entrada_at' => null,
                'salon_at' => null,
                'accessed_at' => $now,
                $column => $now,
            ];

            $access = Access::create($payload);
        } else {
            $existing->update([
                $column => $now,
            ]);
            $access = $existing->fresh();
        }

        $completedNow = $access->isComplete();

        return $access;
    }
}
