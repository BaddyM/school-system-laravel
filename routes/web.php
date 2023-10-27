<?php
use Illuminate\Support\Facades\Route;

include 'custom/marksheet.php';

Route::get('/', function () {
    return view('common.landing');
});
