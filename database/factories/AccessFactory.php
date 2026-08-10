<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\InvitationStatus;
use App\Models\Access;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Access>
 */
class AccessFactory extends Factory
{
    protected $model = Access::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invitation = Invitation::factory()->confirmed()->create([
            'status' => InvitationStatus::Confirmed,
        ]);

        return [
            'invitation_id' => $invitation->id,
            'event_id' => $invitation->event_id,
            'invitation_code' => $invitation->code,
            'guest_first_name' => $invitation->guest->first_name,
            'guest_last_name' => $invitation->guest->last_name,
            'guest_document_number' => $invitation->guest->document_number,
            'guest_id_type' => $invitation->guest->id_type?->value ?? DocumentType::Dni->value,
            'accessed_at' => now(),
        ];
    }
}
