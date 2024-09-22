<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GradeController;


Route::controller(GradeController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/grade','showAll');
   Route::post('/create/grade', 'create');
   Route::get('/edit/grade/{id}','edit');
   Route::post('/update/grade/{id}', 'update');
   Route::delete('/delete/grade/{id}', 'destroy');
   Route::get('/showDeleted/grade', 'showDeleted');
Route::get('/restore/grade/{id}','restore');
Route::delete('/forceDelete/grade/{id}','forceDelete');
   });
