<?php

namespace Tests\Feature\Api;

use App\Contracts\GuestNotifier;
use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendEventNotificationJob;
use App\Models\Event;
use App\Models\EventNotification;
use App\Models\Invitation;
use App\Models\MicroserviceLog;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use App\Services\Push\EnqueuePush;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PushMicroserviceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.push.base_url' => 'https://push.test',
            'services.push.api_key' => 'pns_test',
        ]);
    }

    public function test_a_new_token_registers_the_user_in_the_push_service(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        Http::fake([
            'https://push.test/api/v1/users' => Http::response([
                'success' => true,
                'message' => 'Usuario registrado.',
                'data' => ['id' => 1],
            ], 201),
        ]);

        $this->patchJson('/api/invitations/codeaaaa/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ], [
            'device-platform' => 'iOS',
            'device-app-version' => '1.4.0',
        ])->assertOk();

        Http::assertSent(function ($request) use ($invitation) {
            return $request->method() === 'POST'
                && $request->url() === 'https://push.test/api/v1/users'
                && $request->hasHeader('X-API-Key', 'pns_test')
                && $request['external_user_id'] === (string) $invitation->id
                && $request['firebase_token'] === 'nuevo-token'
                && $request['platform'] === 'ios'
                && $request['name'] === $invitation->guest->fullName()
                && $request['meta']['app_version'] === '1.4.0';
        });

        $log = MicroserviceLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame(MicroserviceLog::SERVICE_REGISTER_USER, $log->service);
        $this->assertSame('POST', $log->method);
        $this->assertSame('/api/v1/users', $log->endpoint);
        $this->assertSame(201, $log->response_code);
        $this->assertSame($invitation->event_id, $log->event_id);
        $this->assertNull($log->user_id);
        $this->assertSame('nuevo-token', $invitation->fresh()->firebase_token);
    }

    public function test_an_existing_token_is_updated_and_registers_again_when_missing(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => 'token-viejo',
        ]);

        Http::fake([
            'https://push.test/api/v1/users/'.$invitation->id.'/token' => Http::response([
                'success' => false,
                'message' => 'Recurso no encontrado.',
                'errors' => [],
            ], 404),
            'https://push.test/api/v1/users' => Http::response([
                'success' => true,
                'message' => 'Usuario registrado.',
                'data' => ['id' => 1],
            ], 201),
        ]);

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [
            'firebase_token' => 'token-nuevo',
        ], [
            'device-platform' => 'android',
        ])->assertOk();

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && str_ends_with($request->url(), '/api/v1/users/'.$invitation->id.'/token'));
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'https://push.test/api/v1/users');

        $this->assertSame('token-nuevo', $invitation->fresh()->firebase_token);
    }

    public function test_platform_is_required_and_the_token_is_not_saved_without_it(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        Http::fake();

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('device_platform');

        Http::assertNothingSent();
        $this->assertNull($invitation->fresh()->firebase_token);
    }

    public function test_a_push_service_failure_keeps_the_local_token(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        Http::fake([
            'https://push.test/api/v1/users' => Http::response([
                'success' => false,
                'message' => 'Los datos enviados no son válidos.',
                'errors' => ['platform' => ['El valor de platform no es válido.']],
            ], 422),
        ]);

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ], [
            'device-platform' => 'ios',
        ])->assertOk();

        $this->assertSame('nuevo-token', $invitation->fresh()->firebase_token);
        $this->assertSame(422, MicroserviceLog::query()->value('response_code'));
    }

    public function test_event_notification_is_enqueued_and_marked_sent_after_polling(): void
    {
        $user = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'firebase_token' => 'fcm-token',
        ]);
        $notification = EventNotification::query()->create([
            'event_id' => $invitation->event_id,
            'type' => NotificationType::Instant,
            'scope' => NotificationScope::Specific,
            'status' => NotificationStatus::Pending,
            'title' => 'Pedido listo',
            'message' => 'Tu pedido ya está en camino',
        ]);
        $notification->invitations()->sync([$invitation->id]);

        Http::fake([
            'https://push.test/api/v1/pushes' => Http::response([
                'success' => true,
                'message' => 'Notificación encolada.',
                'data' => ['id' => 10, 'status' => 'pending'],
            ], 202),
            'https://push.test/api/v1/pushes/10' => Http::response([
                'success' => true,
                'message' => 'OK',
                'data' => ['id' => 10, 'status' => 'sent'],
            ], 200),
        ]);

        (new SendEventNotificationJob($notification->id, $user->id))->handle(app(EnqueuePush::class));

        $notification->refresh();

        $this->assertSame('10', $notification->external_id);
        $this->assertSame(NotificationStatus::Sent, $notification->status);

        $enqueue = MicroserviceLog::query()->where('service', MicroserviceLog::SERVICE_ENQUEUE_PUSH)->first();
        $this->assertNotNull($enqueue);
        $this->assertSame($user->id, $enqueue->user_id);
        $this->assertSame($invitation->event_id, $enqueue->event_id);
        $this->assertStringContainsString((string) $invitation->id, (string) $enqueue->request_body);

        $this->assertTrue(
            MicroserviceLog::query()->where('service', MicroserviceLog::SERVICE_GET_PUSH)->where('method', 'GET')->exists()
        );
    }

    public function test_guest_notifier_skips_invitations_without_a_firebase_token(): void
    {
        $invitation = Invitation::factory()->confirmed()->create([
            'firebase_token' => null,
        ]);

        Http::fake();

        app(GuestNotifier::class)->invitationApproved($invitation);

        Http::assertNothingSent();
    }

    public function test_the_confirmation_push_follows_the_setting(): void
    {
        $invitation = Invitation::factory()->confirmed()->create([
            'firebase_token' => 'fcm-token',
        ]);
        $invitation->event->update(['name' => 'Fiesta de prueba']);
        Setting::current()->update(['texto_confirmacion' => false]);

        Http::fake();

        app(GuestNotifier::class)->invitationApproved($invitation);

        Http::assertNothingSent();

        Setting::current()->update(['texto_confirmacion' => true]);

        Http::fake([
            'https://push.test/api/v1/pushes' => Http::response([
                'success' => true,
                'message' => 'Notificación encolada.',
                'data' => ['id' => 11, 'status' => 'pending'],
            ], 202),
        ]);

        app(GuestNotifier::class)->invitationApproved($invitation->fresh(['event']));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://push.test/api/v1/pushes'
                && $request['title'] === 'Invitación confirmada'
                && $request['body'] === 'Hemos confirmado tu invitación a Fiesta de prueba';
        });
    }

    public function test_logs_screen_requires_the_system_logs_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create(['host' => 'Familia Koepp']);
        MicroserviceLog::query()->create([
            'service' => MicroserviceLog::SERVICE_REGISTER_USER,
            'method' => 'POST',
            'endpoint' => '/api/v1/users',
            'request_body' => '{"external_user_id":"1"}',
            'response_body' => '{"success":true}',
            'response_code' => 201,
            'event_id' => $event->id,
        ]);

        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->get(route('admin.microservice-logs.index'))
            ->assertOk()
            ->assertSee('Logs de sistema')
            ->assertSee('Microservicio')
            ->assertSee('Registrar usuario')
            ->assertSee('/api/v1/users')
            ->assertSee('Familia Koepp');

        $this->actingAs($client)
            ->get(route('admin.microservice-logs.index'))
            ->assertForbidden();

        $permissionId = Permission::query()->where('slug', 'logs.ver')->value('id');
        $client->role->permissions()->attach($permissionId);

        $this->actingAs($client->fresh())
            ->get(route('admin.access-control-logs.index'))
            ->assertOk()
            ->assertSee('Control de acceso');
    }
}
