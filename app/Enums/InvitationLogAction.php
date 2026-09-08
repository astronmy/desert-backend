<?php

namespace App\Enums;

enum InvitationLogAction: string
{
    case Approve = 'approve';
    case Reject = 'reject';
    case Confirm = 'confirm';
    case Edit = 'edit';

    public function label(): string
    {
        return __('invitation.logs.actions.'.$this->value);
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
