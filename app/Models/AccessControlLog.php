<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'service',
    'method',
    'endpoint',
    'request_body',
    'response_body',
    'response_code',
])]
class AccessControlLog extends Model
{
    public const SERVICE_ENTRY = 'consulta_entrada';

    public const SERVICE_REGISTER = 'registro_acceso';

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
        return __('access_control_log.services.'.$this->service);
    }
}
