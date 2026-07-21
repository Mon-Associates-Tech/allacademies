<?php

namespace App\BookShop\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Payment Pending',
            self::PAID => 'Paid',
            self::FAILED => 'Payment Failed',
        };
    }
}
