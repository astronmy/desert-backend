<?php

namespace App\Http\Resources\Api;

use App\Models\EventNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventNotification
 */
class EventNotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var EventNotification $notification */
        $notification = $this->resource;

        if (! $notification->relationLoaded('invitations')) {
            $notification->load('invitations:id');
        }

        return [
            'id' => $notification->id,
            'event_id' => $notification->event_id,
            'type' => $notification->type->value,
            'scope' => $notification->scope->value,
            'status' => $notification->status->value,
            'title' => $notification->title,
            'message' => $notification->message,
            'external_id' => $notification->external_id,
            'send_at' => $notification->send_at?->toIso8601String(),
            'sent_at' => $notification->sent_at?->toIso8601String(),
            'invitation_ids' => $notification->invitations->pluck('id')->values()->all(),
        ];
    }
}
