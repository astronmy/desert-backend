<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEventStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_dashboard_summarizes_current_events_and_their_invitations(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $today = now()->timezone(Event::REGISTRATION_TIMEZONE)->toDateString();
        $current = Event::factory()->create([
            'name' => 'Fiesta vigente',
            'host' => 'Familia Koepp',
            'init_date' => $today,
            'end_date' => $today,
            'invitation_limit' => 80,
        ]);
        $anotherCurrent = Event::factory()->create([
            'name' => 'Cumpleaños vigente',
            'host' => 'Familia Perez',
            'init_date' => $today,
            'end_date' => $today,
            'invitation_limit' => 30,
        ]);
        $past = Event::factory()->create([
            'name' => 'Fiesta terminada',
            'host' => 'Familia Vieja',
            'init_date' => now()->subDays(4)->toDateString(),
            'end_date' => now()->subDays(3)->toDateString(),
            'invitation_limit' => 50,
        ]);

        Invitation::factory()->confirmed()->count(4)->create(['event_id' => $current->id]);
        Invitation::factory()->count(7)->create([
            'event_id' => $current->id,
            'status' => InvitationStatus::Pending,
        ]);
        Invitation::factory()->create([
            'event_id' => $current->id,
            'status' => InvitationStatus::Cancelled,
        ]);
        Invitation::factory()->confirmed()->create(['event_id' => $anotherCurrent->id]);
        Invitation::factory()->confirmed()->count(9)->create(['event_id' => $past->id]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('eventStats', [
                'current' => 2,
                'confirmed' => 5,
                'pending' => 7,
            ])
            ->assertSee('Fiesta vigente')
            ->assertSee('Familia Koepp')
            ->assertSee('Cumpleaños vigente')
            ->assertSee('Familia Perez')
            ->assertSee('80')
            ->assertSee('30')
            ->assertDontSee('Fiesta terminada')
            ->assertDontSee('Familia Vieja');
    }

    public function test_the_client_dashboard_does_not_show_the_events_summary(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $client = User::factory()->client($event->id)->create();

        $this->actingAs($client)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('currentEvents', null)
            ->assertDontSee(__('dashboard.stats.confirmed_quota'));
    }
}
