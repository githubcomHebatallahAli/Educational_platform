<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;



Route::controller(UserController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/user','showAll');
   Route::post('/create/user', 'create');
   Route::get('/edit/user/{id}','edit');
   Route::post('/update/user/{id}', 'update');
   Route::delete('/delete/user/{id}', 'destroy');
   Route::get('/showDeleted/user', 'showDeleted');
Route::get('/restore/user/{id}','restore');
Route::delete('/forceDelete/user/{id}','forceDelete');
   });
