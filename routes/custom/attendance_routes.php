<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware('auth')->prefix('attendance')->group(function(){
    Route::get('/staff',[AttendanceController::class,'staff_index'])->middleware('allowed-users')->name('attendance.staff');
    Route::get('/student',[AttendanceController::class,'student_index'])->name('attendance.student');
    Route::post('/student_list',[AttendanceController::class,'fetch_students'])->name('attendance.student.fetch');
    Route::post('/staff_list',[AttendanceController::class,'fetch_staff'])->middleware('allowed-users')->name('attendance.staff.fetch');
    Route::get('/attendance_table_index',[AttendanceController::class,'create_attendance_table_index'])->middleware('allowed-users')->name('attendance.table.create.index');
    Route::post('/attendance_table',[AttendanceController::class,'create_attendance_table'])->middleware('allowed-users')->name('attendance.table.create');
    Route::post('/student_list_save',[AttendanceController::class,'save_student_attendance'])->name('attendance.student.save');
    Route::post('/staff_list_save',[AttendanceController::class,'save_staff_attendance'])->middleware('allowed-users')->name('attendance.staff.save');
    Route::post('/student_attendance',[AttendanceController::class,'fetch_student_attendance'])->name('attendance.student.get');
    Route::post('/staff_attendance',[AttendanceController::class,'fetch_staff_attendance'])->name('attendance.staff.get');
    Route::get('/print_student_attendance/{class}/{from}/{to}',[AttendanceController::class,'print_std_attendance'])->middleware('allowed-users')->name('attendance.student.print');
    Route::get('/print_staff_attendance/{from}/{to}',[AttendanceController::class,'print_staff_attendance'])->middleware('allowed-users')->name('attendance.student.print');
});