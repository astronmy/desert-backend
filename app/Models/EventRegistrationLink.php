<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'event_id',
    'short_code',
    'token',
    'jti',
    'expires_at',
    'revoked_at',
])]
class EventRegistrationLink extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function hits(): HasMany
    {
        return $this->hasMany(RegistrationLinkHit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('revoked_at')
            ->whereHas('event', fn (Builder $event) => $event->openForRegistration());
    }

    public function isUsable(): bool
    {
        if ($this->revoked_at !== null) {
            return false;
        }

        $this->loadMissing('event');

        return $this->event?->isRegistrationOpen() ?? false;
    }

    public function closesAt(): CarbonInterface
    {
        $this->loadMissing('event');

        return $this->event->registrationClosesAt();
    }

    public function shortUrl(): string
    {
        $base = rtrim((string) config('services.deeplink.base_url', 'https://desert.rxstudio.dev'), '/');

        return $base.'/r/'.$this->short_code;
    }

    public function longActivateUrl(): string
    {
        $base = rtrim((string) config('services.deeplink.base_url', 'https://desert.rxstudio.dev'), '/');
        $feature = (string) config('services.deeplink.feature', 'event_register');

        return $base.'/activar?feature='.rawurlencode($feature).'&token='.rawurlencode($this->token);
    }
}
