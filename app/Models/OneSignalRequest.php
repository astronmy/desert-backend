<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'method',
    'uri',
    'body',
    'response',
    'status',
])]
class OneSignalRequest extends Model
{
    use SoftDeletes;

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'body' => 'array',
            'response' => 'array',
            'status' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
