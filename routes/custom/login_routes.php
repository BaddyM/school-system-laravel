<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::prefix('Login')->group(function(){
    Route::get('/login-pg',[LoginController::class,'login'])->name('login');
    Route::post('/login-validate',[LoginController::class,'validate_login'])->name('login.validate');
    Route::get('/logout',[LoginController::class,'logout'])->name('logout');
});