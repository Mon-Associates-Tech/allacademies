<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case UNPAID = 'unpaid';

    case PART_PAID = 'part-paid';

    case PAID = 'paid';
}
