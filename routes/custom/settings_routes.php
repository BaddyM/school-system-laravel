<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

Route::middleware('auth')->prefix('Settings')->group(function(){
    Route::get('/school_settings',[SettingController::class,'school_settings_index'])->name('setting.school');
    Route::post('/school_settings_save',[SettingController::class,'school_settings'])->name('setting.school.save');
    Route::get('/school_term',[SettingController::class,'term_index'])->name('setting.term');
    Route::get('/subjects',[SettingController::class,'subjects_index'])->name('setting.subjects.index');
    Route::post('/add_subjects',[SettingController::class,'add_subject'])->name('setting.subjects.add');
    Route::post('/delete_subjects',[SettingController::class,'delete_subject'])->name('setting.subjects.delete');
    Route::get('/results_subjects_list',[SettingController::class,'results_table_index'])->name('setting.results.index');
    Route::post('/results_table_create',[SettingController::class,'create_results_table'])->name('setting.results.table.create');
    Route::post('/results_table_delete',[SettingController::class,'delete_results_table'])->name('setting.results.table.delete');
});