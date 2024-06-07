<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;

Route::middleware('auth')->prefix('Staff')->group(function(){
    Route::get('/staff-view',[StaffController::class,'view_index'])->name('staff.display')->middleware('allowed-users');
    Route::get('/staff-data-add',[StaffController::class,'add_index'])->name('staff.data.index')->middleware('allowed-users');
    Route::post('/staff-data-add-details',[StaffController::class,'add_staff_details'])->name('staff.data.details')->middleware('allowed-users');
    Route::post('/staff-data-import-details',[StaffController::class,'import_staff'])->name('staff.data.import')->middleware('allowed-users');
    Route::post('/staff-data-to-modal',[StaffController::class,'modal_data'])->name('staff.data.to.modal')->middleware('allowed-users');
});