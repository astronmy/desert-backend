<?php

namespace Tests\Feature;

use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Models\Event;
use App\Models\EventNotification;
use App\Models\Invitation;
use App\Models\Setting;
use App\Models\User;
use App\Services\Notifications\EventNotificationService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationCreateSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_notification_enqueues_the_push_before_returning(): void
    {
        config([
            'queue.default' => 'database',
            'services.push.base_url' => 'https://push.test',
            'services.push.api_key' => 'pns_test',
        ]);

        $invitation = Invitation::factory()->create([
            'firebase_token' => 'fcm-token',
        ]);

        Http::fake([
            'https://push.test/api/v1/pushes' => Http::response([
                'success' => true,
                'message' => 'Notificación encolada.',
                'data' => ['id' => 10, 'status' => 'pending'],
            ], 202),
        ]);

        $notification = app(EventNotificationService::class)->create([
            'event_id' => $invitation->event_id,
            'type' => NotificationType::Instant->value,
            'scope' => NotificationScope::Specific->value,
            'title' => 'Pedido listo',
            'message' => 'Tu pedido ya está en camino',
            'invitation_ids' => [$invitation->id],
        ]);

        $this->assertSame('10', $notification->fresh()->external_id);
        $this->assertSame(NotificationStatus::Pending, $notification->fresh()->status);
    }

    public function test_admin_can_toggle_notification_texts_and_other_roles_cannot(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Texto Notificaciones')
            ->assertSee('Texto Registro')
            ->assertSee('Texto Confirmación')
            ->assertSee('Texto Recordatorio')
            ->assertSee('Recibimos tu registro para :event. Queda pendiente de aprobación.')
            ->assertSee('Hemos confirmado tu invitación a :event');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'texto_confirmacion' => '1',
                'mensaje_registro' => 'Tu registro en :event',
                'mensaje_confirmacion' => 'Confirmado para :event',
                'mensaje_recordatorio' => 'Te esperamos en :event',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $setting = Setting::current();
        $this->assertFalse($setting->texto_registro);
        $this->assertTrue($setting->texto_confirmacion);
        $this->assertFalse($setting->texto_recordatorio);
        $this->assertSame('Tu registro en :event', $setting->mensaje_registro);
        $this->assertSame('Confirmado para :event', $setting->mensaje_confirmacion);
        $this->assertSame('Te esperamos en :event', $setting->mensaje_recordatorio);

        $this->actingAs($client)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_event_selector_includes_the_host_and_specific_guests_include_the_name(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create([
            'name' => 'Fiesta de prueba',
            'host' => 'Familia Koepp',
        ]);
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'firebase_token' => 'fcm-token',
        ]);
        $invitation->guest->update([
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => '30111222',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.notifications.create'))
            ->assertOk()
            ->assertSee('Fiesta de prueba')
            ->assertSee('Familia Koepp');

        $this->actingAs($admin)
            ->getJson(route('admin.events.notifiable-invitations', $event))
            ->assertOk()
            ->assertJsonPath('data.0.first_name', 'Ana')
            ->assertJsonPath('data.0.last_name', 'Perez')
            ->assertJsonPath('data.0.document', 'DNI 30111222');
    }

    public function test_a_scheduled_time_in_argentina_is_accepted_when_it_is_still_ahead(): void
    {
        $this->seed(RolePermissionSeeder::class);
        config([
            'services.push.base_url' => 'https://push.test',
            'services.push.api_key' => 'pns_test',
        ]);
        Http::fake([
            'https://push.test/api/v1/pushes' => Http::response([
                'success' => true,
                'message' => 'Notificación encolada.',
                'data' => ['id' => 10, 'status' => 'pending'],
            ], 202),
        ]);

        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();
        Invitation::factory()->create([
            'event_id' => $event->id,
            'firebase_token' => 'fcm-token',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-10-12 16:31:00', Event::REGISTRATION_TIMEZONE));

        try {
            $payload = [
                'event_id' => $event->id,
                'type' => NotificationType::Scheduled->value,
                'scope' => NotificationScope::General->value,
                'title' => 'Recordatorio',
                'message' => 'Nos vemos',
            ];

            $this->actingAs($admin)
                ->from(route('admin.notifications.create'))
                ->post(route('admin.notifications.store'), [
                    ...$payload,
                    'send_at' => '2026-10-12T16:30',
                ])
                ->assertRedirect(route('admin.notifications.create'))
                ->assertSessionHasErrors([
                    'send_at' => 'El campo enviar el debe ser una fecha posterior a ahora.',
                ]);

            $this->actingAs($admin)
                ->post(route('admin.notifications.store'), [
                    ...$payload,
                    'send_at' => '2026-10-12T16:34',
                ])
                ->assertRedirect();
        } finally {
            Carbon::setTestNow();
        }

        $notification = EventNotification::query()->where('title', 'Recordatorio')->first();

        $this->assertNotNull($notification);
        $this->assertSame(
            '2026-10-12T19:34:00+00:00',
            $notification->send_at->utc()->toIso8601String()
        );
    }
}
