<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'dept',
        'gender',
        'image',
        'is_admin',
        'is_super_admin',
        'is_teacher',
        'is_bursar',
        'is_librarian',
        'is_student',
        'email_verified',
        'password',
    ];
}
