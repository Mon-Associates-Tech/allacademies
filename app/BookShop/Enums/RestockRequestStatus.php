<?php

namespace App\BookShop\Enums;

enum RestockRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FULFILLED = 'fulfilled';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
