<?php

namespace App\Models;

use App\Enums\InvitationLogAction;
use App\Enums\InvitationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invitation_id',
    'event_id',
    'user_id',
    'action',
    'status_from',
    'status_to',
    'created_at',
])]
class InvitationLog extends Model
{
    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => InvitationLogAction::class,
            'status_from' => InvitationStatus::class,
            'status_to' => InvitationStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
