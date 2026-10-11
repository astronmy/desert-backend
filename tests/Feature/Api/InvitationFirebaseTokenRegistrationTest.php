<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\Invitation;
use App\Models\MicroserviceLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvitationFirebaseTokenRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        config([
            'services.push.base_url' => 'https://push.test',
            'services.push.api_key' => 'pns_test',
        ]);
    }

    public function test_confirm_saves_the_token_and_registers_the_user(): void
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

        $this->post('/api/invitations/CODEAAAA/confirm', $this->confirmPayload($invitation, 'fcm-confirm'), [
            'Accept' => 'application/json',
            'device-platform' => 'ios',
            'device-app-version' => '1.4.0',
        ])->assertOk();

        $this->assertSame('fcm-confirm', $invitation->fresh()->firebase_token);

        Http::assertSent(function ($request) use ($invitation) {
            return $request->method() === 'POST'
                && $request->url() === 'https://push.test/api/v1/users'
                && $request['external_user_id'] === (string) $invitation->id
                && $request['firebase_token'] === 'fcm-confirm'
                && $request['platform'] === 'ios'
                && $request['meta']['app_version'] === '1.4.0';
        });
    }

    public function test_confirm_keeps_the_response_when_the_push_service_fails(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        Http::fake([
            'https://push.test/api/v1/users' => Http::response([
                'success' => false,
                'message' => 'Error',
                'errors' => [],
            ], 500),
        ]);

        $this->post('/api/invitations/CODEAAAA/confirm', $this->confirmPayload($invitation, 'fcm-confirm'), [
            'Accept' => 'application/json',
            'device-platform' => 'android',
        ])->assertOk()
            ->assertJsonPath('message', 'Invitación confirmada correctamente.');

        $this->assertSame('fcm-confirm', $invitation->fresh()->firebase_token);
        $this->assertSame(500, MicroserviceLog::query()->value('response_code'));
    }

    public function test_confirm_without_a_platform_saves_the_token_and_skips_the_push_service(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        Http::fake();

        $this->post('/api/invitations/CODEAAAA/confirm', $this->confirmPayload($invitation, 'fcm-confirm'), [
            'Accept' => 'application/json',
        ])->assertOk();

        Http::assertNothingSent();
        $this->assertSame('fcm-confirm', $invitation->fresh()->firebase_token);
    }

    public function test_confirm_without_a_token_does_not_clear_the_stored_one(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => 'token-previo',
        ]);

        Http::fake();

        $this->post('/api/invitations/CODEAAAA/confirm', $this->confirmPayload($invitation), [
            'Accept' => 'application/json',
            'device-platform' => 'ios',
        ])->assertOk();

        Http::assertNothingSent();
        $this->assertSame('token-previo', $invitation->fresh()->firebase_token);
    }

    public function test_self_register_saves_the_token_and_registers_the_user(): void
    {
        $event = Event::factory()->create([
            'end_date' => '2099-01-01',
        ]);

        Http::fake([
            'https://push.test/api/v1/users' => Http::response([
                'success' => true,
                'message' => 'Usuario registrado.',
                'data' => ['id' => 1],
            ], 201),
        ]);

        $this->post('/api/events/'.$event->id.'/register', [
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => '30111222',
            'id_type' => 'dni',
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            'firebase_token' => 'fcm-register',
        ], [
            'Accept' => 'application/json',
            'device-platform' => 'android',
            'device-app-version' => '2.0.0',
        ])->assertCreated();

        $invitation = Invitation::query()->where('event_id', $event->id)->first();

        $this->assertNotNull($invitation);
        $this->assertSame('fcm-register', $invitation->firebase_token);

        Http::assertSent(function ($request) use ($invitation) {
            return $request->method() === 'POST'
                && $request['external_user_id'] === (string) $invitation->id
                && $request['firebase_token'] === 'fcm-register'
                && $request['platform'] === 'android'
                && $request['name'] === 'Ana Perez'
                && $request['meta']['app_version'] === '2.0.0';
        });
    }

    public function test_self_register_keeps_the_response_when_the_push_service_fails(): void
    {
        $event = Event::factory()->create([
            'end_date' => '2099-01-01',
        ]);

        Http::fake([
            'https://push.test/api/v1/users' => Http::response(['success' => false, 'message' => 'Error', 'errors' => []], 503),
        ]);

        $this->post('/api/events/'.$event->id.'/register', [
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => '30111222',
            'id_type' => 'dni',
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            'firebase_token' => 'fcm-register',
        ], [
            'Accept' => 'application/json',
            'device-platform' => 'ios',
        ])->assertCreated()
            ->assertJsonPath('message', 'Registro recibido. Queda pendiente de aprobación.');

        $this->assertSame('fcm-register', Invitation::query()->where('event_id', $event->id)->value('firebase_token'));
    }

    /**
     * @return array<string, mixed>
     */
    private function confirmPayload(Invitation $invitation, ?string $firebaseToken = null): array
    {
        $invitation->loadMissing('guest');

        $payload = [
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => $invitation->guest->document_number,
            'id_type' => $invitation->guest->id_type->value,
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ];

        if ($firebaseToken !== null) {
            $payload['firebase_token'] = $firebaseToken;
        }

        return $payload;
    }
}
