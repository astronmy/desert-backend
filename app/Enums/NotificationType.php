<?php

namespace App\Enums;

enum NotificationType: string
{
    case Instant = 'instant';
    case Scheduled = 'scheduled';
}
