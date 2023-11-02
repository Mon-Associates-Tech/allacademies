<?php

namespace App\Enums;

enum TeamStatus: string
{
    case PENDING = 'pending';

    case DECLINED = 'declined';

    case APPROVED = 'approved';
}
