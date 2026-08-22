<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'requires_event',
    'is_system',
    'is_active',
])]
class Role extends Model
{
    public const SLUG_ADMIN = 'administrador';

    public const SLUG_CLIENT = 'cliente';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requires_event' => 'boolean',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('slug', $slug);
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
