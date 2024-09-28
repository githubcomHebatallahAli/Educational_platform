<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ParentController;



Route::controller(ParentController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/parent','showAll');
   Route::get('/edit/parent/{id}','edit');
   Route::post('/update/parent/{id}', 'update');
   Route::delete('/delete/parent/{id}', 'destroy');
   Route::get('/showDeleted/parent', 'showDeleted');
Route::get('/restore/parent/{id}','restore');
Route::delete('/forceDelete/parent/{id}','forceDelete');
   });
