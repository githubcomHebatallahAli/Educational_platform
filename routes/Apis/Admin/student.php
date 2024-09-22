<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;


Route::controller(StudentController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/student','showAll');
   Route::post('/create/student', 'create');
   Route::get('/edit/student/{id}','edit');
   Route::post('/update/student/{id}', 'update');
   Route::patch('/update/isPay/student/{id}', 'updateIsPay');
   Route::delete('/delete/student/{id}', 'destroy');
   Route::get('/showDeleted/student', 'showDeleted');
Route::get('/restore/student/{id}','restore');
Route::delete('/forceDelete/student/{id}','forceDelete');
   });
