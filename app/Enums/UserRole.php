<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case SUBSCRIBER = 'subscriber';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case LIBRARIAN = 'librarian';
    case AUTHOR = 'author';
    case PARENT = 'parent';
    case ACCOUNTANT = 'accountant';
}
