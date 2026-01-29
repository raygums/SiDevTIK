<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case VERIFIKATOR = 'verifikator';
    case EKSEKUTOR = 'eksekutor';
    case ADMIN = 'admin';
}