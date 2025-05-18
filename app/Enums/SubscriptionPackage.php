<?php

namespace App\Enums;

enum SubscriptionPackage: string
{
    case INDIVIDUAL_FULL = 'individual:full';

    case INSTITUTION_FULL = 'institution:full';

    const INSTITUTION_MOCK_EXAMS = 'institution:mock';
    const INSTITUTION_MID_TERM = 'institution:midterm';

    const ONCE_OFF = 'once:off';
}
