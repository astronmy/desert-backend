<?php

namespace App\Enums;

enum NotificationScope: string
{
    case General = 'general';
    case Specific = 'specific';
}
