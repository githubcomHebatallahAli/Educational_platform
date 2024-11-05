<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController;



Route::controller(OrderController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/order','showAll');
   Route::get('/edit/order/{id}','edit');
   Route::post('/update/order/{id}', 'update');
   Route::delete('/delete/order/{id}', 'destroy');
   Route::get('/showDeleted/order', 'showDeleted');
Route::get('/restore/order/{id}','restore');
Route::delete('/forceDelete/order/{id}','forceDelete');

Route::patch('paid/order/{id}', 'paid');
Route::patch('pending/order/{id}', 'pending');
Route::patch('canceled/order/{id}', 'canceled');
   });
