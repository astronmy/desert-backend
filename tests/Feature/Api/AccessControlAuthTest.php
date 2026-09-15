<?php

namespace Tests\Feature\Api;

use App\Models\Access;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_access_control_can_login_via_api(): void
    {
        $user = User::factory()->accessControl()->create([
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role.slug', Role::SLUG_ACCESS_CONTROL)
            ->assertJsonStructure(['token', 'token_type', 'user']);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_admin_cannot_login_via_api(): void
    {
        $user = User::factory()->admin()->create([
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertForbidden()
            ->assertJsonPath('message', __('role.messages.api_forbidden'));
    }

    public function test_client_cannot_login_via_api(): void
    {
        $user = User::factory()->client()->create([
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertForbidden()
            ->assertJsonPath('message', __('role.messages.api_forbidden'));
    }

    public function test_invalid_credentials_return_validation_error(): void
    {
        $user = User::factory()->accessControl()->create([
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_access_control_cannot_login_on_the_web(): void
    {
        $user = User::factory()->accessControl()->create([
            'password' => 'password',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => __('role.messages.api_only')]);

        $this->assertGuest();
    }

    public function test_me_and_logout_require_and_revoke_token(): void
    {
        $user = User::factory()->accessControl()->create();
        $token = $user->createToken('access-control')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role.slug', Role::SLUG_ACCESS_CONTROL);

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }

    public function test_entry_and_accesses_require_token(): void
    {
        $invitation = Invitation::factory()->confirmed()->create();

        $this->getJson('/api/invitations/'.$invitation->code.'/entry')
            ->assertUnauthorized();

        $this->postJson('/api/accesses', [
            'code' => $invitation->code,
            'checkpoint' => 'entrada',
        ])->assertUnauthorized();
    }

    public function test_access_control_can_query_entry_and_register_dual_checkpoints(): void
    {
        $user = User::factory()->accessControl()->create();
        $token = $user->createToken('access-control')->plainTextToken;
        $invitation = Invitation::factory()->confirmed()->create();

        $this->withToken($token)
            ->getJson('/api/invitations/'.$invitation->code.'/entry')
            ->assertOk()
            ->assertJsonPath('code', $invitation->code)
            ->assertJsonPath('access.has_entered', false)
            ->assertJsonPath('access.has_entrada', false)
            ->assertJsonPath('access.has_salon', false)
            ->assertJsonPath('access.is_complete', false);

        $this->withToken($token)
            ->postJson('/api/accesses', [
                'code' => $invitation->code,
                'checkpoint' => 'entrada',
            ])
            ->assertCreated()
            ->assertJsonPath('access.invitation_code', $invitation->code)
            ->assertJsonPath('access.checkpoint', 'entrada')
            ->assertJsonPath('access.is_complete', false);

        $this->assertDatabaseHas('accesses', [
            'invitation_id' => $invitation->id,
        ]);

        $access = Access::query()->where('invitation_id', $invitation->id)->first();
        $this->assertNotNull($access->entrada_at);
        $this->assertNull($access->salon_at);
        $this->assertFalse($access->isComplete());

        $this->withToken($token)
            ->getJson('/api/invitations/'.$invitation->code.'/entry')
            ->assertOk()
            ->assertJsonPath('access.has_entrada', true)
            ->assertJsonPath('access.has_salon', false)
            ->assertJsonPath('access.is_complete', false)
            ->assertJsonPath('access.has_entered', false);

        $this->withToken($token)
            ->postJson('/api/accesses', [
                'code' => $invitation->code,
                'checkpoint' => 'entrada',
            ])
            ->assertStatus(409)
            ->assertJsonPath('checkpoint', 'entrada');

        $this->withToken($token)
            ->postJson('/api/accesses', [
                'code' => $invitation->code,
                'checkpoint' => 'salon',
            ])
            ->assertCreated()
            ->assertJsonPath('access.checkpoint', 'salon')
            ->assertJsonPath('access.is_complete', true);

        $this->withToken($token)
            ->getJson('/api/invitations/'.$invitation->code.'/entry')
            ->assertOk()
            ->assertJsonPath('access.has_entrada', true)
            ->assertJsonPath('access.has_salon', true)
            ->assertJsonPath('access.is_complete', true)
            ->assertJsonPath('access.has_entered', true);
    }

    public function test_checkpoint_is_required_when_registering_access(): void
    {
        $user = User::factory()->accessControl()->create();
        $token = $user->createToken('access-control')->plainTextToken;
        $invitation = Invitation::factory()->confirmed()->create();

        $this->withToken($token)
            ->postJson('/api/accesses', ['code' => $invitation->code])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['checkpoint']);
    }

    public function test_invitation_show_remains_public(): void
    {
        $invitation = Invitation::factory()->create();

        $this->getJson('/api/invitations/'.$invitation->code)
            ->assertOk()
            ->assertJsonPath('code', $invitation->code);
    }
}
