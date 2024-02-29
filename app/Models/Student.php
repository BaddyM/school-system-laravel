<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    use HasFactory;

    public $table = 'student';

    protected $fillable = [
        'std_id',
        'fname',
        'lname',
        'mname',
        'dob',
        'class',
        'stream',
        'house',
        'section',
        'image',
        'year_of_entry',
        'status',
        'gender',
        'combination',
        'password',
        'lin',
        'residence',
        'nationality',
    ];

}
