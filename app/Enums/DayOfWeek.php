<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';
    case Sunday = 'sunday';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function isWeekend(): bool
    {
        return $this === self::Saturday || $this === self::Sunday;
    }
}
