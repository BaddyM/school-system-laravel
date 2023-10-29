<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksheetController;

Route::prefix('marksheet')->group(function(){
    Route::get('/alevel', [MarksheetController::class,'alevel'])->name('alevel.marksheet');
    Route::post('/fetchalevel', [MarksheetController::class,'fetchdata'])->name('marksheet.display');
    Route::post('/amarksheet', [MarksheetController::class,'marksheet'])->name('marksheet.a.fetch');
    Route::get('/marksheet-pdf/{result}/{classname}', [MarksheetController::class,'marksheetpdf'])->name('marksheet.pdf');
    Route::get('/olevel',[MarksheetController::class,'olevel_marksheet'])->name('marksheet.olevel.display');
});