<?php
use Illuminate\Support\Facades\Route;

include 'custom/marksheet.php';
include 'custom/staff.php';
include 'custom/student_routes.php';
include 'custom/home_routes.php';

Route::get('/', function () {
    return view('common.landing');
});
