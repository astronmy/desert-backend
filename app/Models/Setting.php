<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'texto_registro',
        'texto_confirmacion',
        'texto_recordatorio',
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
            'texto_confirmacion' => true,
            'texto_recordatorio' => true,
        ]);
    }
}
