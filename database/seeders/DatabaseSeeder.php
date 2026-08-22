<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $adminRoleId = Role::query()->where('slug', Role::SLUG_ADMIN)->value('id');

        User::factory()->create([
            'name' => 'Admin Desert',
            'email' => 'admin@deserteventos.com.ar',
            'password' => 'password',
            'role_id' => $adminRoleId,
            'event_id' => null,
        ]);

        $this->call([
            EventSeeder::class,
            InvitationSeeder::class,
        ]);
    }
}
