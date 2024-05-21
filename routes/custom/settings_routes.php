<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

Route::middleware('auth')->prefix('Settings')->group(function(){
    Route::get('/school_settings',[SettingController::class,'school_settings_index'])->name('setting.school');
    Route::post('/school_settings_save',[SettingController::class,'school_settings'])->name('setting.school.save');

    //Term
    Route::get('/school_term',[SettingController::class,'term_index'])->name('setting.term');
    Route::post('/school_term_add',[SettingController::class,'add_term'])->name('setting.term.add');
    Route::post('/school_term_change',[SettingController::class,'change_term'])->name('setting.term.change');
    Route::post('/school_term_delete',[SettingController::class,'delete_term'])->name('setting.term.delete');

    //Subjects
    Route::get('/subjects',[SettingController::class,'subjects_index'])->name('setting.subjects.index');
    Route::post('/add_subjects',[SettingController::class,'add_subject'])->name('setting.subjects.add');
    Route::post('/add_subs',[SettingController::class,'add_subsidiary'])->name('setting.subjects.add.subs');
    Route::post('/delete_subjects',[SettingController::class,'delete_subject'])->name('setting.subjects.delete');
    Route::get('/results_subjects_list',[SettingController::class,'results_table_index'])->name('setting.results.index');

    //Results tables
    Route::post('/results_table_create',[SettingController::class,'create_results_table'])->name('setting.results.table.create');
    Route::post('/results_table_delete',[SettingController::class,'delete_results_table'])->name('setting.results.table.delete');

    //Signatures
    Route::get('/signature_index',[SettingController::class,'signature_index'])->name('setting.signatures');
    Route::post('/signature_upload',[SettingController::class,'upload_signature'])->name('setting.signatures.upload');
    Route::post('/signature_delete',[SettingController::class,'delete_signature'])->name('setting.signatures.delete');

    //Teacher Initials
    Route::get('/teacher_initials',[SettingController::class,'teacher_initials_index'])->name('setting.initials');
    Route::post('/teacher_initials',[SettingController::class,'teacher_initials_save'])->name('setting.initials.save');
    Route::post('/teacher_initials_delete',[SettingController::class,'delete_initials'])->name('setting.initials.delete');
    Route::post('/teacher_initials_edit',[SettingController::class,'show_initials'])->name('setting.initials.edit');
    Route::post('/teacher_initials_update',[SettingController::class,'update_initials'])->name('setting.initials.update');

    //Status List
    Route::get('/status_list',[SettingController::class, 'status_list_index'])->name('status.list.index');
    Route::post('/status_list_add',[SettingController::class, 'status_list_add'])->name('setting.status.add');
    Route::post('/status_list_delete',[SettingController::class, 'delete_status'])->name('setting.status.delete');

    //Classes
    Route::get("/classes",[SettingController::class,'classes_index'])->name('class.index');
    Route::post("/add_class",[SettingController::class,'add_class'])->name('class.add');
    Route::post("/delete_class",[SettingController::class,'delete_class'])->name('class.delete');

    //Streams
    Route::get("streams",[SettingController::class,'stream_index'])->name('stream.index');
    Route::post("/add_streams",[SettingController::class,'add_stream'])->name('stream.add');
    Route::post("/delete_streams",[SettingController::class,'delete_stream'])->name('stream.delete');

    //Users
    Route::get("/users",[SettingController::class,'user_index'])->name('users.index');
    Route::post("/fetch_users",[SettingController::class,'fetch_user'])->name('users.fetch');
    Route::post("/update_users",[SettingController::class,'update_user'])->name('users.update');
    Route::post("/add_users",[SettingController::class,'add_user'])->name('users.add');
    Route::post("/update_users_image",[SettingController::class,'update_image'])->name('users.update.image');
});