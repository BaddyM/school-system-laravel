<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware('auth')->prefix('attendance')->group(function(){
    Route::get('/staff',[AttendanceController::class,'staff_index'])->name('attendance.staff');
    Route::get('/student',[AttendanceController::class,'student_index'])->name('attendance.student');
    Route::post('/student_list',[AttendanceController::class,'fetch_students'])->name('attendance.student.fetch');
    Route::post('/student_list_save',[AttendanceController::class,'save_student_attendance'])->name('attendance.student.save');
    Route::post('/student_attendance',[AttendanceController::class,'fetch_student_attendance'])->name('attendance.student.get');
    Route::get('/print_student_attendance/{class}/{from}/{to}',[AttendanceController::class,'print_std_attendance'])->name('attendance.student.print');
});