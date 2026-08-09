<?php

namespace App\Enums;

enum EventType: string
{
    case Wedding = 'wedding';
    case Birthday = 'birthday';
    case Graduation = 'graduation';
    case Corporate = 'corporate';
    case Private = 'private';

    public function label(): string
    {
        return __('event.types.'.$this->value);
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
