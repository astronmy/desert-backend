<?php

namespace App\Enums;

enum InvitationOrigin: string
{
    case Api = 'api';
    case Manual = 'manual';
    case Import = 'import';

    public function label(): string
    {
        return __('invitation.origins.'.$this->value);
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
