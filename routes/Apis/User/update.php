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
   Route::post('/update/profile/{id}', 'studentUpdateProfile');
});


Route::controller(UpdateController::class)
->prefix('/parent')
->middleware('parent')
->group(
    function () {
   Route::post('/update/photo/{id}', 'parentUpdateProfilePicture');
   Route::post('/update/profile/{id}', 'parentUpdateProfile');

});
