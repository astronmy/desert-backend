<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const DEFAULT_MENSAJE_REGISTRO = 'Recibimos tu registro para :event. Queda pendiente de aprobación.';

    public const DEFAULT_MENSAJE_CONFIRMACION = 'Hemos confirmado tu invitación a :event';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'texto_registro',
        'mensaje_registro',
        'texto_confirmacion',
        'mensaje_confirmacion',
        'texto_recordatorio',
        'mensaje_recordatorio',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'texto_registro' => 'boolean',
            'texto_confirmacion' => 'boolean',
            'texto_recordatorio' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'texto_registro' => true,
            'mensaje_registro' => self::DEFAULT_MENSAJE_REGISTRO,
            'texto_confirmacion' => true,
            'mensaje_confirmacion' => self::DEFAULT_MENSAJE_CONFIRMACION,
            'texto_recordatorio' => true,
        ]);
    }

    public function notificationBody(string $messageColumn, string $langKey, string $eventName): string
    {
        $template = $this->{$messageColumn};

        if (! filled($template)) {
            return __($langKey, ['event' => $eventName]);
        }

        return str_replace(':event', $eventName, $template);
    }
}
