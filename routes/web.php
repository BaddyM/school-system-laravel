<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

include 'custom/marksheet.php';
include 'custom/staff_routes.php';
include 'custom/student_routes.php';
include 'custom/home_routes.php';
include 'custom/login_routes.php';
include 'custom/settings_routes.php';
include 'custom/results_routes.php';
include 'custom/attendance_routes.php';

Route::middleware('auth')->get('/', function () {
    $staff = User::all();
    $students = Student::all();

    //Get the active term
    $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
    $year =  (DB::table('term')->select('year')->where('active',1)->first())->year; 
    $planner = DB::table('planner')->where(['term' => $term, 'year' => $year])->get();

    $subjects = DB::select("
        SELECT 
            DISTINCT name
        FROM
            subjects
    ");

    return view('common.landing', compact('staff', 'students', 'subjects', 'planner'));
})->name('root');
