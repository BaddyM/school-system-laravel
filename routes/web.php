<?php
use Illuminate\Support\Facades\Route;

include 'custom/marksheet.php';
include 'custom/staff.php';

Route::get('/', function () {
    return view('common.landing');
});
