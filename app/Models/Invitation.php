<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'event_id',
    'guest_id',
    'code',
    'status',
    'selfie_path',
    'uuid_notification',
    'confirmed_at',
])]
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => InvitationStatus::class,
            'confirmed_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function access(): HasOne
    {
        return $this->hasOne(Access::class);
    }

    public function eventNotifications(): BelongsToMany
    {
        return $this->belongsToMany(EventNotification::class, 'event_notification_invitation')
            ->withTimestamps();
    }

    public function selfieUrl(): ?string
    {
        if (! $this->selfie_path) {
            return null;
        }

        return Storage::disk('public')->url($this->selfie_path);
    }
}
