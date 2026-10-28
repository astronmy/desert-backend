<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'service',
    'method',
    'endpoint',
    'ip',
    'request_body',
    'response_body',
    'response_code',
])]
class ApiLog extends Model
{
    public const SERVICE_EVENTS = 'listar_eventos';

    public const SERVICE_FIREBASE_TOKEN = 'actualizar_firebase_token';

    public const SERVICE_CONFIRM = 'confirmar_invitacion';

    public const SERVICE_REGISTER = 'registro_evento';

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
        return __('api_log.services.'.$this->service);
    }

    /**
     * @return list<string>
     */
    public static function services(): array
    {
        return [
            self::SERVICE_EVENTS,
            self::SERVICE_FIREBASE_TOKEN,
            self::SERVICE_CONFIRM,
            self::SERVICE_REGISTER,
        ];
    }
}
