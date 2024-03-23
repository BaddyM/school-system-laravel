<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksheetController;

Route::middleware('auth')->prefix('marksheet')->group(function(){
    Route::get('/alevel', [MarksheetController::class,'alevel'])->name('alevel.marksheet');
    Route::get('/olevel',[MarksheetController::class,'olevel_marksheet'])->name('marksheet.olevel');
    Route::post('/marksheet-select',[MarksheetController::class,'marksheet'])->name('marksheet');
    Route::get('/marksheet-print/{class}/{table}/{level}',[MarksheetController::class,'print_marksheet'])->name('marksheet.print');
});