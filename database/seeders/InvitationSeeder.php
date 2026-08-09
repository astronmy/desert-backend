<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\Invitations\InvitationCodeGenerator;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::query()->limit(3)->get();

        if ($events->isEmpty()) {
            return;
        }

        $guests = Guest::factory()->count(8)->create();
        $generator = app(InvitationCodeGenerator::class);

        foreach ($events as $event) {
            foreach ($guests->random(min(5, $guests->count())) as $guest) {
                Invitation::query()->firstOrCreate(
                    [
                        'event_id' => $event->id,
                        'guest_id' => $guest->id,
                    ],
                    [
                        'code' => $generator->generate(),
                        'status' => 'pending',
                    ]
                );
            }
        }
    }
}
