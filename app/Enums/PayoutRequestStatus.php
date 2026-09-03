<?php

namespace App\Enums;

enum PayoutRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
