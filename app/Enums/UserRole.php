<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case GUEST = 'guest';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case LIBRARIAN = 'librarian';
    case AUTHOR = 'author';
    case PARENT = 'parent';
    case ACCOUNTANT = 'accountant';
    case BENEFACTOR = 'benefactor';
    case SPONSOR = 'sponsor';
    case REVIEWER = 'reviewer';

    case SUPER_ADMIN = 'superadmin';

    public static function getAll(): array
    {
        return [
            self::OWNER,
            self::ADMIN,
            self::MODERATOR,
            self::GUEST,
            self::TEACHER,
            self::STUDENT,
            self::LIBRARIAN,
            self::AUTHOR,
            self::PARENT,
            self::ACCOUNTANT,
            self::BENEFACTOR,
            self::SPONSOR,
            self::REVIEWER,
        ];
    }
}
