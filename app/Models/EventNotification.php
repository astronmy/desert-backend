<?php

namespace App\Models;

use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'event_id',
    'type',
    'scope',
    'status',
    'title',
    'message',
    'external_id',
    'send_at',
    'sent_at',
])]
class EventNotification extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => NotificationType::class,
            'scope' => NotificationScope::class,
            'status' => NotificationStatus::class,
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function invitations(): BelongsToMany
    {
        return $this->belongsToMany(Invitation::class, 'event_notification_invitation')
            ->withTimestamps();
    }
}
