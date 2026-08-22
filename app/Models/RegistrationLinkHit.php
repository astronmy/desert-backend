<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_registration_link_id',
    'event_id',
    'visited_at',
    'ip_hash',
    'user_agent',
    'device_type',
    'os',
    'browser',
    'referrer',
    'is_store_click',
    'store',
])]
class RegistrationLinkHit extends Model
{
    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'is_store_click' => 'boolean',
        ];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(EventRegistrationLink::class, 'event_registration_link_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
