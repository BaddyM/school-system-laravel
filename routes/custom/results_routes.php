<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultsController;

Route::middleware('auth')->prefix('Results')->group(function(){
    Route::get('/olevel_results',[ResultsController::class,'olevel_index'])->name('olevel.index');
    Route::get('/alevel_results',[ResultsController::class,'alevel_index'])->name('alevel.index');
    Route::post('/olevel_show',[ResultsController::class,'select_students'])->name('olevel.show');
    Route::post('/add_results_olevel',[ResultsController::class,'enter_results_olevel'])->name('add.results.olevel');
    Route::post('/add_results_alevel',[ResultsController::class,'enter_results_alevel'])->name('add.results.alevel');
    Route::get('/report_olevel',[ResultsController::class,'oreports_index'])->name('reports.olevel');
    Route::get('/report_alevel',[ResultsController::class,'areports_index'])->name('reports.alevel');
    Route::post('/report_olevel_class',[ResultsController::class,'select_class'])->name('reports.olevel.class');

    //Print PDF
    Route::get('/report_olevel_print/{table}/{term}/{year}/{ids}',[ResultsController::class,'oreports_print_fpdf'])->name('reports.olevel.print');
    //Route::get('/report_olevel_print/{table}/{term}/{year}/{ids}',[ResultsController::class,'oreports_print'])->name('reports.olevel.print');


    Route::get('/report_alevel_print/{table}/{term}/{year}/{ids}',[ResultsController::class,'areports_print'])->name('reports.alevel.print');
    Route::get('/marklist_print/{class}/{paper}/{subject}/{level}',[ResultsController::class,'print_marklist'])->name('marklist.print');

    //Marklist
    Route::get('/olevel_marklist',[ResultsController::class,'olevel_marklist_index'])->name('olevel.marklist.index');
    Route::post('/olevel_fetch_marklist',[ResultsController::class,'fetch_olevel_marklist_result'])->name('olevel.marklist.fetch');
    Route::get('/olevel_print_marklist/{table}/{class}/{subject}/{topic}',[ResultsController::class,'print_marklist_olevel'])->name('olevel.marklist.print');
});