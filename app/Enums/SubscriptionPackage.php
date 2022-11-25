<?php

namespace App\Enums;

enum SubscriptionPackage: string
{
    case INDIVIDUAL_FULL = 'individual:full';

    case INSTITUTION_FULL = 'institution:full';
}
