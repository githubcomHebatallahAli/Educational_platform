<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuestionController;


Route::controller(QuestionController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/question','showAll');
   Route::post('/create/question', 'create');
   Route::get('/edit/question/{id}','edit');
   Route::post('/update/question/{id}', 'update');
   Route::delete('/delete/question/{id}', 'destroy');
   Route::get('/showDeleted/question', 'showDeleted');
Route::get('/restore/question/{id}','restore');
Route::delete('/forceDelete/question/{id}','forceDelete');
   });
