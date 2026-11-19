<?php

namespace Tests\Feature\Api;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_code_returns_mock_pending(): void
    {
        $this->getJson('/api/invitations/NOEXISTE')
            ->assertOk()
            ->assertJsonPath('code', 'NOEXISTE')
            ->assertJsonPath('status', InvitationStatus::Pending->value);
    }

    public function test_cancelled_invitation_is_exposed_as_pending(): void
    {
        $invitation = Invitation::factory()->cancelled()->create([
            'code' => 'CANCELLED1',
            'confirmed_at' => now(),
        ]);

        $this->getJson('/api/invitations/CANCELLED1')
            ->assertOk()
            ->assertJsonPath('code', 'CANCELLED1')
            ->assertJsonPath('status', InvitationStatus::Pending->value)
            ->assertJsonPath('confirmed_at', null)
            ->assertJsonPath('guest.first_name', $invitation->guest->first_name);
    }

    public function test_confirmed_invitation_keeps_confirmed_status(): void
    {
        Invitation::factory()->confirmed()->create([
            'code' => 'CONFIRMED1',
        ]);

        $this->getJson('/api/invitations/CONFIRMED1')
            ->assertOk()
            ->assertJsonPath('status', InvitationStatus::Confirmed->value);
    }
}
