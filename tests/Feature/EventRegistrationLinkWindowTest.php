<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistrationLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventRegistrationLinkWindowTest extends TestCase
{
    use RefreshDatabase;

    public function test_link_issued_before_the_change_stays_usable_until_3am(): void
    {
        $event = Event::factory()->create([
            'end_date' => '2026-10-03',
        ]);

        $link = EventRegistrationLink::query()->create([
            'event_id' => $event->id,
            'short_code' => 'ABC12345',
            'token' => 'v1.old.token',
            'jti' => '11111111-1111-1111-1111-111111111111',
            'expires_at' => Carbon::parse('2026-10-03 23:59:59', 'UTC'),
        ]);

        try {
            Carbon::setTestNow(Carbon::parse('2026-10-04 02:30:00', Event::REGISTRATION_TIMEZONE));

            $this->assertTrue($link->fresh()->isUsable());
            $this->assertNotNull(
                EventRegistrationLink::query()->active()->whereKey($link->id)->first()
            );

            Carbon::setTestNow(Carbon::parse('2026-10-04 03:00:00', Event::REGISTRATION_TIMEZONE));

            $this->assertFalse($link->fresh()->isUsable());
            $this->assertNull(
                EventRegistrationLink::query()->active()->whereKey($link->id)->first()
            );
        } finally {
            Carbon::setTestNow();
        }
    }
}
