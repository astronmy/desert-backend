<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\Deeplink\EventRegistrationLinkService;
use Illuminate\Console\Command;

class DeeplinkGenerateCommand extends Command
{
    protected $signature = 'deeplink:generate
                            {event : ID del evento}';

    protected $description = 'Genera (o regenera) el short link de registro de un evento';

    public function handle(EventRegistrationLinkService $links): int
    {
        $eventId = (int) $this->argument('event');
        $event = Event::query()->find($eventId);

        if (! $event) {
            $this->error("Evento no encontrado: {$eventId}");

            return self::FAILURE;
        }

        $link = $links->issueOrRegenerate($event);

        $this->info('Short link generado:');
        $this->line($link->shortUrl());
        $this->newLine();
        $this->line('long (activar): '.$link->longActivateUrl());
        $this->line('short_code: '.$link->short_code);
        $this->line('jti: '.$link->jti);
        $this->line('expires_at: '.$link->expires_at->toIso8601String());

        return self::SUCCESS;
    }
}
