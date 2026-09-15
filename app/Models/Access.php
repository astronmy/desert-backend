<?php

namespace App\Models;

use Database\Factories\AccessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invitation_id',
    'event_id',
    'invitation_code',
    'guest_first_name',
    'guest_last_name',
    'guest_document_number',
    'guest_id_type',
    'entrada_at',
    'salon_at',
    'accessed_at',
])]
class Access extends Model
{
    /** @use HasFactory<AccessFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entrada_at' => 'datetime',
            'salon_at' => 'datetime',
            'accessed_at' => 'datetime',
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

    public function guestFullName(): string
    {
        return trim($this->guest_first_name.' '.$this->guest_last_name);
    }

    public function hasEntrada(): bool
    {
        return $this->entrada_at !== null;
    }

    public function hasSalon(): bool
    {
        return $this->salon_at !== null;
    }

    public function isComplete(): bool
    {
        return $this->hasEntrada() && $this->hasSalon();
    }
}
