<?php

namespace Tests\Feature\Api;

use App\Models\AccessControlLog;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_registering_an_access_writes_a_log_row(): void
    {
        $user = User::factory()->accessControl()->create();
        $token = $user->createToken('access-control')->plainTextToken;
        $invitation = Invitation::factory()->confirmed()->create();

        $this->withToken($token)
            ->postJson('/api/accesses', [
                'code' => $invitation->code,
                'checkpoint' => 'entrada',
            ])
            ->assertCreated();

        $log = AccessControlLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame(AccessControlLog::SERVICE_REGISTER, $log->service);
        $this->assertSame('POST', $log->method);
        $this->assertSame('/api/accesses', $log->endpoint);
        $this->assertSame(201, $log->response_code);
        $this->assertStringContainsString($invitation->code, (string) $log->request_body);
        $this->assertStringContainsString('entrada', (string) $log->request_body);
        $this->assertStringContainsString('Checkpoint registrado correctamente.', (string) $log->response_body);
    }

    public function test_entry_lookup_writes_a_log_row(): void
    {
        $user = User::factory()->accessControl()->create();
        $token = $user->createToken('access-control')->plainTextToken;
        $invitation = Invitation::factory()->confirmed()->create();

        $this->withToken($token)
            ->get('/api/invitations/'.$invitation->code.'/entry', [
                'Accept' => 'application/json',
            ])
            ->assertOk();

        $log = AccessControlLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame(AccessControlLog::SERVICE_ENTRY, $log->service);
        $this->assertSame('GET', $log->method);
        $this->assertSame('/api/invitations/'.$invitation->code.'/entry', $log->endpoint);
        $this->assertSame(200, $log->response_code);
        $this->assertNull($log->request_body);
    }

    public function test_admin_can_filter_the_log_and_other_roles_cannot(): void
    {
        AccessControlLog::query()->create([
            'service' => AccessControlLog::SERVICE_REGISTER,
            'method' => 'POST',
            'endpoint' => '/api/accesses',
            'request_body' => '{"code":"ABC"}',
            'response_body' => '{"message":"ok"}',
            'response_code' => 201,
        ]);

        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->get(route('admin.access-control-logs.index', ['service' => AccessControlLog::SERVICE_REGISTER, 'response_code' => 201]))
            ->assertOk()
            ->assertSee('Registro de acceso')
            ->assertSee('/api/accesses');

        $this->actingAs($admin)
            ->get(route('admin.access-control-logs.index', ['service' => AccessControlLog::SERVICE_ENTRY]))
            ->assertOk()
            ->assertDontSee('/api/accesses');

        $this->actingAs($client)
            ->get(route('admin.access-control-logs.index'))
            ->assertForbidden();
    }
}
