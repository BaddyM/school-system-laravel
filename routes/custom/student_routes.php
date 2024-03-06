<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::middleware('auth')->prefix('Student')->group(function(){
    Route::get('/add-student',[StudentController::class,'addStudentIndex'])->name('student.add');
    Route::post('/add-student-data',[StudentController::class,'addStudentToDB'])->name('student.add.db');
    Route::get('/students-view',[StudentController::class,'viewStudentIndex'])->name('student.view');
    Route::post('/student-data-fetch',[StudentController::class,'fetchStudentData'])->name('data.fetch');
    Route::post('/std_modal-data-fetch',[StudentController::class,'getDataForModal'])->name('data.fetch.modal');
    Route::post('/std_update-data',[StudentController::class,'updateStudentData'])->name('student.update');
    Route::get('/std_import-data',[StudentController::class,'import_index'])->name('student.import');
    Route::post('/std_import_file-data',[StudentController::class,'import_students'])->name('student.import.file');
    Route::get('/std_print-data/{class}/{status}',[StudentController::class,'print_students'])->name('student.print');
    Route::post('/std_fetch_img',[StudentController::class,'fetch_std_records'])->name('student.fetch.image');
    Route::post('/std_update_img',[StudentController::class,'update_std_image'])->name('student.update.image');
    Route::post('/std_disable',[StudentController::class,'disable_student'])->name('student.disable');
    Route::get('/std_status_index',[StudentController::class,'student_status_index'])->name('student.status.index');
    Route::post('/std_status_dt',[StudentController::class,'display_std_status'])->name('student.status.dt');
    Route::post('/std_status_update',[StudentController::class,'update_std_status'])->name('student.status.update');
});