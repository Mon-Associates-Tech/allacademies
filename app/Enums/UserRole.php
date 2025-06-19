<?php

namespace App\Enums;

enum UserRole: string
{
    public const OWNER = 'owner';

    public const ADMIN = 'admin';

    public const MODERATOR = 'moderator';

    public const SUBSCRIBER = 'subscriber';
    public const TEACHER = 'teacher';
    public const STUDENT = 'student';
    public const LIBRARIAN = 'librarian';
    public const AUTHOR = 'author';
    public const PARENT = 'parent';
}
