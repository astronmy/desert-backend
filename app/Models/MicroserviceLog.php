<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service',
    'method',
    'endpoint',
    'request_body',
    'response_body',
    'response_code',
    'user_id',
    'event_id',
])]
class MicroserviceLog extends Model
{
    public const SERVICE_REGISTER_USER = 'registrar_usuario';

    public const SERVICE_UPDATE_TOKEN = 'actualizar_token';

    public const SERVICE_ENQUEUE_PUSH = 'encolar_push';

    public const SERVICE_GET_PUSH = 'consultar_push';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response_code' => 'integer',
        ];
    }

    public function serviceLabel(): string
    {
        return __('microservice_log.services.'.$this->service);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
