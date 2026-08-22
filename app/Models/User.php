<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'event_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function canPermission(string $slug): bool
    {
        $role = $this->role;

        if (! $role || ! $role->is_active) {
            return false;
        }

        return $role->hasPermission($slug);
    }

    public function requiresEvent(): bool
    {
        return (bool) $this->role?->requires_event;
    }

    public function isClient(): bool
    {
        return $this->role?->slug === Role::SLUG_CLIENT;
    }

    public function isAdminRole(): bool
    {
        return $this->role?->slug === Role::SLUG_ADMIN;
    }

    /**
     * Cliente (u otro rol con requires_event) solo puede operar sobre su evento.
     */
    public function canAccessEvent(int|Event $event): bool
    {
        if (! $this->requiresEvent()) {
            return true;
        }

        $eventId = $event instanceof Event ? $event->id : $event;

        return $this->event_id !== null && (int) $this->event_id === (int) $eventId;
    }
}
