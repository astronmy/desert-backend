<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSitesBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_activate_page_credits_rxstudio_and_sets_the_author(): void
    {
        $this->get(route('activar'))
            ->assertOk()
            ->assertSee('<meta name="author" content="RXStudio.dev">', false)
            ->assertSee('Diseñado por')
            ->assertSee('https://rxstudio.dev', false)
            ->assertSee('RxStudio.dev');
    }

    public function test_the_confirmed_site_normalizes_names_and_credits_rxstudio(): void
    {
        $event = Event::factory()->create([
            'name' => 'Fiesta de prueba',
            'host' => 'Familia Koepp',
        ]);
        $guest = Guest::factory()->create([
            'first_name' => 'RENZO',
            'last_name' => 'vinci',
        ]);
        Invitation::factory()->create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'status' => InvitationStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        $this->get(route('events.confirmed', $event->confirmedSiteSlug()))
            ->assertOk()
            ->assertSee('<meta name="author" content="RXStudio.dev">', false)
            ->assertSee('Renzo')
            ->assertSee('Vinci')
            ->assertDontSee('RENZO')
            ->assertDontSee('>vinci<', false)
            ->assertSee('Buscar por nombre, apellido o DNI')
            ->assertSee('id="guest-search"', false)
            ->assertSee('Diseñado por')
            ->assertSee('https://rxstudio.dev', false)
            ->assertSee('RxStudio.dev');
    }
}
