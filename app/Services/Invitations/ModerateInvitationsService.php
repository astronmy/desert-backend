<?php

namespace App\Services\Invitations;

use App\Contracts\GuestNotifier;
use App\Enums\InvitationLogAction;
use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ModerateInvitationsService
{
    public function __construct(
        private readonly GuestNotifier $notifier,
        private readonly InvitationLogService $logs,
    ) {}

    /**
     * @param  list<int>  $ids
     * @return array{updated: int}
     */
    public function approve(Event $event, array $ids): array
    {
        /** @var list<Invitation> $approved */
        $approved = [];
        $userId = auth()->id();

        $result = DB::transaction(function () use ($event, $ids, $userId, &$approved) {
            /** @var Collection<int, Invitation> $invitations */
            $invitations = Invitation::query()
                ->where('event_id', $event->id)
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            $updated = 0;
            foreach ($invitations as $invitation) {
                if ($invitation->status === InvitationStatus::Confirmed) {
                    continue;
                }

                $from = $invitation->status;
                $invitation->update([
                    'status' => InvitationStatus::Confirmed,
                    'confirmed_at' => $invitation->confirmed_at ?? now(),
                ]);
                $this->logs->record(
                    $invitation,
                    InvitationLogAction::Approve,
                    $from,
                    InvitationStatus::Confirmed,
                    $userId
                );
                $approved[] = $invitation->fresh(['guest', 'event']);
                $updated++;
            }

            return ['updated' => $updated];
        });

        foreach ($approved as $invitation) {
            $this->notifier->invitationApproved($invitation);
        }

        return $result;
    }

    /**
     * @param  list<int>  $ids
     * @return array{updated: int}
     */
    public function reject(Event $event, array $ids): array
    {
        $userId = auth()->id();

        return DB::transaction(function () use ($event, $ids, $userId) {
            /** @var Collection<int, Invitation> $invitations */
            $invitations = Invitation::query()
                ->where('event_id', $event->id)
                ->whereIn('id', $ids)
                ->where('status', '!=', InvitationStatus::Cancelled)
                ->lockForUpdate()
                ->get();

            $updated = 0;
            foreach ($invitations as $invitation) {
                $from = $invitation->status;
                $invitation->update([
                    'status' => InvitationStatus::Cancelled,
                    'confirmed_at' => null,
                ]);
                $this->logs->record(
                    $invitation,
                    InvitationLogAction::Reject,
                    $from,
                    InvitationStatus::Cancelled,
                    $userId
                );
                $updated++;
            }

            return ['updated' => $updated];
        });
    }
}
