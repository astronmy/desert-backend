<?php

namespace App\Models;

use App\Enums\EventType;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'init_date', 'end_date', 'type'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'init_date' => 'date',
            'end_date' => 'date',
            'type' => EventType::class,
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class);
    }

    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(Guest::class, 'invitations')
            ->withPivot(['id', 'code', 'status', 'selfie_path', 'confirmed_at'])
            ->withTimestamps();
    }
}

