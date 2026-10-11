<?php

namespace Tests\Feature\Api;

use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateInvitationFirebaseTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_patch_updates_the_firebase_token_by_invitation_code(): void
    {
        $invitation = Invitation::factory()->create([
            'code' => 'CODEAAAA',
            'firebase_token' => null,
        ]);

        $this->patchJson('/api/invitations/codeaaaa/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ])
            ->assertOk()
            ->assertJsonPath('code', 'CODEAAAA')
            ->assertJsonPath('message', 'Token de Firebase actualizado.');

        $this->assertSame('nuevo-token', $invitation->fresh()->firebase_token);
    }

    public function test_unknown_code_returns_not_found(): void
    {
        $this->patchJson('/api/invitations/NOEXISTE/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ])->assertNotFound();
    }

    public function test_cancelled_invitation_cannot_update_the_token(): void
    {
        Invitation::factory()->cancelled()->create([
            'code' => 'CODEAAAA',
        ]);

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [
            'firebase_token' => 'nuevo-token',
        ])->assertStatus(410);
    }

    public function test_firebase_token_is_required(): void
    {
        Invitation::factory()->create([
            'code' => 'CODEAAAA',
        ]);

        $this->patchJson('/api/invitations/CODEAAAA/firebase-token', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('firebase_token');
    }
}
