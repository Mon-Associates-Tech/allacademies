<?php

namespace App\Support;

enum TokenSubscriptionStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case DEPLETED = 'depleted';
    case REPLACED = 'replaced';
}
