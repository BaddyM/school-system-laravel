<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksheetController;

Route::get('/alevel', [MarksheetController::class,'alevel'])->name('alevel.marksheet');
