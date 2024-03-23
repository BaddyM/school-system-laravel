<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

include 'custom/marksheet.php';
include 'custom/staff.php';
include 'custom/student_routes.php';
include 'custom/home_routes.php';
include 'custom/login_routes.php';
include 'custom/settings_routes.php';
include 'custom/results_routes.php';

Route::middleware('auth')->get('/', function () {
    $staff = User::all();
    $students = Student::all();

    $subjects = DB::select("
            SELECT 
                DISTINCT name
            FROM
                subjects
        ");

    return view('common.landing', compact('staff', 'students', 'subjects'));
})->name('root');
