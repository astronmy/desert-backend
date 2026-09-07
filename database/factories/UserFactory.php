<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => null,
            'event_id' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(function () {
            $roleId = Role::query()->where('slug', Role::SLUG_ADMIN)->value('id');

            return [
                'role_id' => $roleId,
                'event_id' => null,
            ];
        });
    }

    public function client(?int $eventId = null): static
    {
        return $this->state(function () use ($eventId) {
            $roleId = Role::query()->where('slug', Role::SLUG_CLIENT)->value('id');

            return [
                'role_id' => $roleId,
                'event_id' => $eventId,
            ];
        });
    }

    public function accessControl(): static
    {
        return $this->state(function () {
            $roleId = Role::query()->where('slug', Role::SLUG_ACCESS_CONTROL)->value('id');

            return [
                'role_id' => $roleId,
                'event_id' => null,
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
