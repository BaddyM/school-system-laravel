<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;

Route::middleware('auth')->prefix('Staff')->group(function(){
    Route::get('/Staff-View',[StaffController::class,'index'])->name('staff.display');
    Route::post('/Staff-Data',[StaffController::class,'staff_details'])->name('staff.data');
});