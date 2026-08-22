<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Services\Deeplink\DeeplinkTokenService;
use Illuminate\Console\Command;

class DeeplinkGenerateCommand extends Command
{
    protected $signature = 'deeplink:generate
                            {code : Código de la invitación}
                            {--days=30 : Días de vigencia del link}';

    protected $description = 'Genera un deep link firmado para una invitación';

    public function handle(DeeplinkTokenService $tokens): int
    {
        $code = strtoupper(trim((string) $this->argument('code')));
        $days = max(1, (int) $this->option('days'));

        $invitation = Invitation::query()->where('code', $code)->first();

        if (! $invitation) {
            $this->error("Invitación no encontrada: {$code}");

            return self::FAILURE;
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            $this->error('La invitación está cancelada.');

            return self::FAILURE;
        }

        $issued = $tokens->issue($invitation, now()->addDays($days));

        $this->info('Link generado:');
        $this->line($issued['url']);
        $this->newLine();
        $this->line('feature: '.$issued['feature']);
        $this->line('code: '.$issued['invitation_code']);
        $this->line('jti: '.$issued['jti']);
        $this->line('expires_at: '.$issued['expires_at']->toIso8601String());

        return self::SUCCESS;
    }
}
