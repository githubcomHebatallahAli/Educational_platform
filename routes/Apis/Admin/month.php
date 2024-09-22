<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MonthController;


Route::controller(MonthController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/month','showAll');
   Route::post('/create/month', 'create');
   Route::get('/edit/month/{id}','edit');
   Route::post('/update/month/{id}', 'update');
   Route::delete('/delete/month/{id}', 'destroy');
   Route::get('/showDeleted/month', 'showDeleted');
Route::get('/restore/month/{id}','restore');
Route::delete('/forceDelete/month/{id}','forceDelete');
   });
