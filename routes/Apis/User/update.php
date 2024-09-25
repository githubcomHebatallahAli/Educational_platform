<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UpdateController;



Route::controller(UpdateController::class)
->prefix('/student')
->middleware('auth:api')
->group(
    function () {

   Route::post('/update/photo/{id}', 'studentUpdateProfilePicture');
   Route::post('/update/code/{id}', 'updateCode');
});

Route::controller(UpdateController::class)
->prefix('/admin')
->middleware('admin')
->group(
    function () {

   Route::post('/update/photo/{id}', 'adminUpdateProfilePicture');

});

Route::controller(UpdateController::class)
->prefix('/parent')
->middleware('auth:parnt')
->group(
    function () {

   Route::post('/update/photo/{id}', 'parentUpdateProfilePicture');

});
