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
}
