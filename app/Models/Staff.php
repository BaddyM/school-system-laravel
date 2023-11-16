<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    public $table = 'staff'; 

    protected $fillable = [
        'staffid',
        'FName',
        'LName',
        'position',
        'gender',
        'status',
        'location',
        'subjects',
        'Class'
    ];
    
    public static function newFactory(){
        return StaffFactory::new();
    }
}
