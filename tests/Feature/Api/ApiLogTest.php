<?php

namespace Tests\Feature\Api;

use App\Models\ApiLog;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_api_endpoints_write_an_api_log_row(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        $this->getJson('/api/events')
            ->assertOk();

        $eventsLog = ApiLog::query()->where('service', ApiLog::SERVICE_EVENTS)->first();

        $this->assertNotNull($eventsLog);
        $this->assertSame('GET', $eventsLog->method);
        $this->assertSame('/api/events', $eventsLog->endpoint);
        $this->assertNotNull($eventsLog->ip);
        $this->assertSame(200, $eventsLog->response_code);
        $this->assertNotNull($eventsLog->response_body);

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [
            'firebase_token' => 'fcm-token',
            'device_platform' => 'ios',
        ], [
            'device-platform' => 'ios',
        ])->assertOk();

        $tokenLog = ApiLog::query()->where('service', ApiLog::SERVICE_FIREBASE_TOKEN)->first();

        $this->assertNotNull($tokenLog);
        $this->assertSame('PATCH', $tokenLog->method);
        $this->assertSame('/api/invitations/CODEAAAA/firebase-token', $tokenLog->endpoint);
        $this->assertStringContainsString('fcm-token', (string) $tokenLog->request_body);
        $this->assertSame(200, $tokenLog->response_code);

        $this->postJson('/api/invitations/CODEAAAA/confirm', [
            'first_name' => 'Ana',
        ])->assertForbidden();

        $confirmLog = ApiLog::query()->where('service', ApiLog::SERVICE_CONFIRM)->first();

        $this->assertNotNull($confirmLog);
        $this->assertSame('POST', $confirmLog->method);
        $this->assertSame('/api/invitations/CODEAAAA/confirm', $confirmLog->endpoint);
        $this->assertSame(403, $confirmLog->response_code);
        $this->assertStringContainsString('Forbidden.', (string) $confirmLog->response_body);
        $this->assertSame('CODEAAAA', $invitation->fresh()->code);
    }
}
