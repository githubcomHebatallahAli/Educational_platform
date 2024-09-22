<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TestController;


Route::controller(TestController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/test','showAll');
   Route::post('/create/test', 'create');
   Route::get('/edit/test/{id}','edit');
   Route::post('/update/test/{id}', 'update');
   Route::delete('/delete/test/{id}', 'destroy');
   Route::get('/showDeleted/test', 'showDeleted');
Route::get('/restore/test/{id}','restore');
Route::delete('/forceDelete/test/{id}','forceDelete');
   });
