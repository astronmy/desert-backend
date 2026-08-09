<?php

namespace App\Enums;

enum DocumentType: string
{
    case Dni = 'dni';
    case Passport = 'passport';

    public function label(): string
    {
        return __('guest.document_types.'.$this->value);
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
