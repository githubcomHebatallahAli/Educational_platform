<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\mainCourseController;


Route::controller(mainCourseController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/mainCourse','showAll');
   Route::post('/create/mainCourse', 'create');
   Route::get('/edit/mainCourse/{id}','edit');
   Route::post('/update/mainCourse/{id}', 'update');
   Route::delete('/delete/mainCourse/{id}', 'destroy');
   Route::get('/showDeleted/mainCourse', 'showDeleted');
Route::get('/restore/mainCourse/{id}','restore');
Route::delete('/forceDelete/mainCourse/{id}','forceDelete');

   });
