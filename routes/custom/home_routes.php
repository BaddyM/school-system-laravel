<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::prefix('Home')->group(function(){
    Route::get('/Home',[HomeController::class,'index'])->name('home');
    Route::get('/term',[HomeController::class,'fetch_term'])->name('home.term');
});