<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UpdateController;



Route::controller(UpdateController::class)
->prefix('/student')
->middleware('auth:api')
->group(
    function () {

   Route::post('/update/photo/{id}', 'updateProfilePicture');
   Route::post('/update/code/{id}', 'updateCode');
});
