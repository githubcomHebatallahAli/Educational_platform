<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AnswerController;


Route::controller(AnswerController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/answer','showAll');
   Route::post('/create/answer', 'create');
   Route::get('/edit/answer/{id}','edit');
   Route::post('/update/answer/{id}', 'update');
   Route::delete('/delete/answer/{id}', 'destroy');
   Route::get('/showDeleted/answer', 'showDeleted');
Route::get('/restore/answer/{id}','restore');
Route::delete('/forceDelete/answer/{id}','forceDelete');
Route::post('/answer/submit', 'storeStudentAnswers');
   });
