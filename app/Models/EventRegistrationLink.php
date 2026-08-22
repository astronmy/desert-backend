<?php

namespace App\Models;

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
            ->where('expires_at', '>=', now());
    }

    public function isUsable(): bool
    {
        return $this->revoked_at === null && $this->expires_at->isFuture();
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
