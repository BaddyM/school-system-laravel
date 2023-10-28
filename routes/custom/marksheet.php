<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksheetController;

Route::get('/alevel', [MarksheetController::class,'alevel'])->name('alevel.marksheet');
Route::post('/fetchalevel', [MarksheetController::class,'fetchdata'])->name('marksheet.display');
Route::post('/amarkcheet', [MarksheetController::class,'marksheet'])->name('marksheet.a.fetch');