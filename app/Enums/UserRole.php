<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';

    case ADMIN = 'admin';

    case MODERATOR = 'moderator';

    case SUBSCRIBER = 'subscriber';

    case STUDENT = 'student';

    case TEACHER = 'teacher';

    case LIBRARIAN = 'librarian';

    case AUTHOR = 'author';
}
