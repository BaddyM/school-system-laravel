<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

Route::middleware('auth')->prefix('Settings')->group(function(){
    Route::get('/school_settings',[SettingController::class,'school_settings_index'])->name('setting.school')->middleware('allowed-users');
    Route::post('/school_settings_save',[SettingController::class,'school_settings'])->name('setting.school.save')->middleware('allowed-users');

    //Term
    Route::get('/school_term',[SettingController::class,'term_index'])->name('setting.term')->middleware('allowed-users');
    Route::post('/school_term_add',[SettingController::class,'add_term'])->name('setting.term.add')->middleware('allowed-users');
    Route::post('/school_term_change',[SettingController::class,'change_term'])->name('setting.term.change')->middleware('allowed-users');
    Route::post('/school_term_delete',[SettingController::class,'delete_term'])->name('setting.term.delete')->middleware('allowed-users');

    //Subjects
    Route::get('/subjects',[SettingController::class,'subjects_index'])->name('setting.subjects.index')->middleware('allowed-users');
    Route::post('/add_subjects',[SettingController::class,'add_subject'])->name('setting.subjects.add')->middleware('allowed-users');
    Route::post('/add_subs',[SettingController::class,'add_subsidiary'])->name('setting.subjects.add.subs')->middleware('allowed-users');
    Route::post('/delete_subjects',[SettingController::class,'delete_subject'])->name('setting.subjects.delete')->middleware('allowed-users');
    Route::get('/results_subjects_list',[SettingController::class,'results_table_index'])->name('setting.results.index')->middleware('allowed-users');

    //Results tables
    Route::post('/results_table_create',[SettingController::class,'create_results_table'])->name('setting.results.table.create')->middleware('allowed-users');
    Route::post('/results_table_delete',[SettingController::class,'delete_results_table'])->name('setting.results.table.delete')->middleware('allowed-users');

    //Signatures
    Route::get('/signature_index',[SettingController::class,'signature_index'])->name('setting.signatures')->middleware('allowed-users');
    Route::post('/signature_upload',[SettingController::class,'upload_signature'])->name('setting.signatures.upload')->middleware('allowed-users');
    Route::post('/signature_delete',[SettingController::class,'delete_signature'])->name('setting.signatures.delete')->middleware('allowed-users');

    //Teacher Initials
    Route::get('/teacher_initials',[SettingController::class,'teacher_initials_index'])->name('setting.initials')->middleware('allowed-users');
    Route::post('/teacher_initials',[SettingController::class,'teacher_initials_save'])->name('setting.initials.save')->middleware('allowed-users');
    Route::post('/teacher_initials_delete',[SettingController::class,'delete_initials'])->name('setting.initials.delete')->middleware('allowed-users');
    Route::post('/teacher_initials_edit',[SettingController::class,'show_initials'])->name('setting.initials.edit')->middleware('allowed-users');
    Route::post('/teacher_initials_update',[SettingController::class,'update_initials'])->name('setting.initials.update')->middleware('allowed-users');

    //Status List
    Route::get('/status_list',[SettingController::class, 'status_list_index'])->name('status.list.index')->middleware('allowed-users');
    Route::post('/status_list_add',[SettingController::class, 'status_list_add'])->name('setting.status.add')->middleware('allowed-users');
    Route::post('/status_list_delete',[SettingController::class, 'delete_status'])->name('setting.status.delete')->middleware('allowed-users');

    //Classes
    Route::get("/classes",[SettingController::class,'classes_index'])->name('class.index')->middleware('allowed-users');
    Route::post("/add_class",[SettingController::class,'add_class'])->name('class.add')->middleware('allowed-users');
    Route::post("/delete_class",[SettingController::class,'delete_class'])->name('class.delete')->middleware('allowed-users');

    //Streams
    Route::get("streams",[SettingController::class,'stream_index'])->name('stream.index')->middleware('allowed-users');
    Route::post("/add_streams",[SettingController::class,'add_stream'])->name('stream.add')->middleware('allowed-users');
    Route::post("/delete_streams",[SettingController::class,'delete_stream'])->name('stream.delete')->middleware('allowed-users');

    //Users
    Route::get("/users",[SettingController::class,'user_index'])->name('users.index');
    Route::post("/fetch_users",[SettingController::class,'fetch_user'])->name('users.fetch');
    Route::post("/update_users",[SettingController::class,'update_user'])->name('users.update');
    Route::post("/add_users",[SettingController::class,'add_user'])->name('users.add');
    Route::post("/delete_users",[SettingController::class,'delete_user'])->name('users.delete');
    Route::post("/update_users_image",[SettingController::class,'update_image'])->name('users.update.image');

    //Positions
    Route::get("/positions",[SettingController::class,'position_index'])->name('positions.index')->middleware('allowed-users');
    Route::post("/add_positions",[SettingController::class,'add_position'])->name('positions.add')->middleware('allowed-users');
    Route::post("/add_dept",[SettingController::class,'add_department'])->name('dept.add')->middleware('allowed-users');
    Route::post("/delete_dept",[SettingController::class,'delete_department'])->name('dept.delete')->middleware('allowed-users');

    //Topics
    Route::get("/topics",[SettingController::class,'topics_index'])->name('topics.index');
    Route::post("/add_topics",[SettingController::class,'add_topic'])->name('topics.add');
    Route::post("/delete_topics",[SettingController::class,'delete_topic'])->name('topics.delete');
    Route::post("/fetch_topics",[SettingController::class,'fetch_topics'])->name('topics.fetch');
    Route::post("/fetch_edit_topics",[SettingController::class,'fetch_edit_data'])->name('topics.edit');
    Route::post("/update_topics",[SettingController::class,'update_topic'])->name('topics.update');
    Route::post("/select_topics",[SettingController::class,'fetch_topics_ajax'])->name('fetch.topics.olevel');

    //Term Planner
    Route::get("/term_planner",[SettingController::class,'planner_index'])->name('planner.index');
    Route::post("/add_planner",[SettingController::class,'add_planner'])->name('planner.add');
    Route::post("/delete_planner",[SettingController::class,'delete_planner'])->name('planner.delete');
});