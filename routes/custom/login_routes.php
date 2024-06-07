<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::prefix('Login')->group(function(){
    Route::get('/login-pg',[LoginController::class,'login'])->name('login');
    Route::post('/login-validate',[LoginController::class,'validate_login'])->name('login.validate');
    Route::get('/logout',[LoginController::class,'logout'])->name('logout');
});

//Signup
Route::prefix('Signup')->group(function(){
    Route::get('/signup-pg',[LoginController::class,'signup_index'])->name('signup.index');
    Route::post('/register',[LoginController::class,'register_user'])->name('register.user');
});

//Forgot password
Route::prefix('Forgot-Password')->group(function(){
    Route::get('/forgotpass-pg',[LoginController::class,'forgot_pass_index'])->name('forgotpass.index');
    Route::post('/forgotpass-send-email',[LoginController::class,'send_email'])->name('forgotpass.send.email');
});