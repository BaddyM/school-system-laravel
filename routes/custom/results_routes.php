<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultsController;

Route::middleware('auth')->prefix('Results')->group(function(){
    Route::get('/olevel_results',[ResultsController::class,'olevel_index'])->name('olevel.index');
    Route::get('/alevel_results',[ResultsController::class,'alevel_index'])->name('alevel.index');
    Route::post('/olevel_show',[ResultsController::class,'select_students'])->name('olevel.show');
    Route::post('/add_results_olevel',[ResultsController::class,'enter_results_olevel'])->name('add.results.olevel');
    Route::post('/add_results_alevel',[ResultsController::class,'enter_results_alevel'])->name('add.results.alevel');
});