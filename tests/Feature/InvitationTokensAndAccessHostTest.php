<?php

namespace Tests\Feature;

use App\Models\Access;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InvitationTokensAndAccessHostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_invitation_grid_shows_firebase_and_onesignal_as_yes_or_no(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();

        $withFirebase = Invitation::factory()->create([
            'event_id' => $event->id,
            'code' => 'CODEAAAA',
            'firebase_token' => 'firebase-token',
            'uuid_notification' => null,
        ]);
        $withOneSignal = Invitation::factory()->create([
            'event_id' => $event->id,
            'code' => 'CODEBBBB',
            'firebase_token' => null,
            'uuid_notification' => 'onesignal-uuid',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.events.invitations.index', $event))
            ->assertOk()
            ->assertSee('Firebase')
            ->assertSee('OneSignal')
            ->assertDontSee('UUID notificación')
            ->assertSeeInOrder([$withFirebase->guest->fullName(), 'SI', 'NO'])
            ->assertSeeInOrder([$withOneSignal->guest->fullName(), 'NO', 'SI']);
    }

    public function test_accesses_header_shows_the_event_host(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create([
            'name' => 'Cumple de prueba',
            'host' => 'Familia Koepp',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.events.accesses.index', $event))
            ->assertOk()
            ->assertSee('Evento: Cumple de prueba')
            ->assertSee('Familia Koepp');
    }

    public function test_access_times_are_shown_in_argentina_time(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();
        $invitation = Invitation::factory()->confirmed()->create([
            'event_id' => $event->id,
        ]);

        Access::factory()->create([
            'invitation_id' => $invitation->id,
            'event_id' => $event->id,
            'invitation_code' => $invitation->code,
            'entrada_at' => Carbon::parse('2026-10-12 01:30:00', 'UTC'),
            'salon_at' => Carbon::parse('2026-10-12 02:45:10', 'UTC'),
            'accessed_at' => Carbon::parse('2026-10-12 01:30:00', 'UTC'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.events.accesses.index', $event))
            ->assertOk()
            ->assertSee('11/10/2026 22:30:00')
            ->assertSee('11/10/2026 23:45:10');

        $this->actingAs($admin)
            ->get(route('admin.events.accesses.index', [
                'event' => $event,
                'date_from' => '2026-10-11',
                'date_to' => '2026-10-11',
            ]))
            ->assertOk()
            ->assertSee('11/10/2026 22:30:00');

        $this->actingAs($admin)
            ->get(route('admin.events.accesses.index', [
                'event' => $event,
                'date_from' => '2026-10-12',
            ]))
            ->assertOk()
            ->assertDontSee('11/10/2026 22:30:00');
    }
}
