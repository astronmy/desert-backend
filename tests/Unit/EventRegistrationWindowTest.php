<?php

namespace Tests\Unit;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventRegistrationWindowTest extends TestCase
{
    public function test_registration_stays_open_until_3am_the_day_after_end_date(): void
    {
        $event = new Event(['end_date' => '2026-10-03']);

        $closes = $event->registrationClosesAt();

        $this->assertSame('2026-10-04 03:00:00', $closes->format('Y-m-d H:i:s'));
        $this->assertSame(Event::REGISTRATION_TIMEZONE, $closes->getTimezone()->getName());

        $this->assertTrue($event->isRegistrationOpen(
            Carbon::parse('2026-10-04 02:59:59', Event::REGISTRATION_TIMEZONE)
        ));
        $this->assertFalse($event->isRegistrationOpen(
            Carbon::parse('2026-10-04 03:00:00', Event::REGISTRATION_TIMEZONE)
        ));
    }
}
