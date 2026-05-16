<?php

namespace App\Enums;

enum MonitorStatus: string
{
    case PENDING = 'pending';
    case UP = 'up';
    case DOWN = 'down';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
