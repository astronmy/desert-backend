<?php

namespace Tests\Feature;

use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Models\Invitation;
use App\Models\Setting;
use App\Models\User;
use App\Services\Notifications\EventNotificationService;
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
            ->assertSee('Texto Recordatorio');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'texto_confirmacion' => '1',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $setting = Setting::current();
        $this->assertFalse($setting->texto_registro);
        $this->assertTrue($setting->texto_confirmacion);
        $this->assertFalse($setting->texto_recordatorio);

        $this->actingAs($client)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }
}
