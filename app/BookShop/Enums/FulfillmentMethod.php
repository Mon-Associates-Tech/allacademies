<?php

namespace App\BookShop\Enums;

enum FulfillmentMethod: string
{
    case PICKUP = 'pickup';
    case DELIVERY = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Pickup',
            self::DELIVERY => 'Delivery',
        };
    }
}
