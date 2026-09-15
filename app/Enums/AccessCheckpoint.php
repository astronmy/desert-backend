<?php

namespace App\Enums;

enum AccessCheckpoint: string
{
    case Entrada = 'entrada';
    case Salon = 'salon';

    public function label(): string
    {
        return __('access.checkpoints.'.$this->value);
    }

    public function alreadyReason(): string
    {
        return match ($this) {
            self::Entrada => 'already_entrada',
            self::Salon => 'already_salon',
        };
    }

    public function timestampColumn(): string
    {
        return match ($this) {
            self::Entrada => 'entrada_at',
            self::Salon => 'salon_at',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
