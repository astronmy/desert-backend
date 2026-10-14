<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInvitationReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_going_back_from_an_invitation_keeps_the_client_dashboard_page_and_filters(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $guest = Guest::factory()->create(['first_name' => 'Ana', 'last_name' => 'Perez']);
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'status' => InvitationStatus::Pending,
            'created_at' => now()->subDay(),
        ]);
        Invitation::factory()->count(15)->create([
            'event_id' => $event->id,
            'status' => InvitationStatus::Pending,
        ]);
        $client = User::factory()->client($event->id)->create();

        $this->actingAs($client)
            ->get(route('admin.dashboard', ['page' => 2, 'status' => 'pending']))
            ->assertOk()
            ->assertSee(route('admin.events.invitations.show', [
                'event' => $event,
                'invitation' => $invitation,
                'from' => 'dashboard',
                'page' => 2,
                'status' => 'pending',
            ]));

        $this->actingAs($client)
            ->get(route('admin.events.invitations.show', [
                'event' => $event,
                'invitation' => $invitation,
                'from' => 'dashboard',
                'page' => 2,
                'name' => 'Ana',
                'status' => 'pending',
            ]))
            ->assertOk()
            ->assertSee(route('admin.dashboard', [
                'page' => 2,
                'name' => 'Ana',
                'status' => 'pending',
            ]));
    }

    public function test_going_back_from_an_invitation_keeps_the_invitations_list_page_and_filters(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $guest = Guest::factory()->create([
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => '30111222',
        ]);
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'status' => InvitationStatus::Pending,
            'created_at' => now()->subDay(),
        ]);
        Invitation::factory()->count(15)->create([
            'event_id' => $event->id,
            'status' => InvitationStatus::Pending,
        ]);
        $client = User::factory()->client($event->id)->create();

        $this->actingAs($client)
            ->get(route('admin.events.invitations.index', ['event' => $event, 'page' => 2]))
            ->assertOk()
            ->assertSee(route('admin.events.invitations.show', [
                'event' => $event,
                'invitation' => $invitation,
                'from' => 'invitations',
                'page' => 2,
            ]));

        $this->actingAs($client)
            ->get(route('admin.events.invitations.show', [
                'event' => $event,
                'invitation' => $invitation,
                'from' => 'invitations',
                'page' => 2,
                'name' => 'Ana',
                'document_number' => '30111222',
                'code' => $invitation->code,
                'status' => 'pending',
            ]))
            ->assertOk()
            ->assertSee(route('admin.events.invitations.index', [
                'event' => $event,
                'page' => 2,
                'name' => 'Ana',
                'document_number' => '30111222',
                'code' => $invitation->code,
                'status' => 'pending',
            ]));
    }

    public function test_an_invitation_opened_without_a_list_still_returns_to_the_invitations_index(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $invitation = Invitation::factory()->create(['event_id' => $event->id]);
        $client = User::factory()->client($event->id)->create();

        $this->actingAs($client)
            ->get(route('admin.events.invitations.show', [$event, $invitation]))
            ->assertOk()
            ->assertSee(route('admin.events.invitations.index', $event));
    }
}
