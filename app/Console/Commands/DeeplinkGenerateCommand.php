<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\Deeplink\DeeplinkTokenService;
use Illuminate\Console\Command;

class DeeplinkGenerateCommand extends Command
{
    protected $signature = 'deeplink:generate
                            {event : ID del evento}';

    protected $description = 'Genera el deep link de registro de un evento (vence con end_date)';

    public function handle(DeeplinkTokenService $tokens): int
    {
        $eventId = (int) $this->argument('event');
        $event = Event::query()->find($eventId);

        if (! $event) {
            $this->error("Evento no encontrado: {$eventId}");

            return self::FAILURE;
        }

        $issued = $tokens->issue($event);

        $this->info('Link generado:');
        $this->line($issued['url']);
        $this->newLine();
        $this->line('feature: '.$issued['feature']);
        $this->line('event_id: '.$issued['event_id']);
        $this->line('jti: '.$issued['jti']);
        $this->line('expires_at: '.$issued['expires_at']->toIso8601String());

        return self::SUCCESS;
    }
}
