<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CreateController;



Route::controller(CreateController::class)
->prefix('/student')
->group(
    function () {
   Route::post('/create/answer', 'create');
});

Route::controller(CreateController::class)
->group(
    function () {
   Route::post('/create/contact', 'createContactUs');
});
