<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ParentAuthController;
use App\Http\Controllers\Auth\StudentAuthController;


    Route::controller(StudentAuthController::class)->prefix('/student')->group(
        function () {
    Route::post('/login', 'login');
    Route::post('/register',  'register');
    Route::post('/logout',  'logout');
    Route::post('/refresh', 'refresh');
    Route::get('/user-profile', 'userProfile');

});

Route::controller(AdminAuthController::class)->prefix('/admin')->group(
    function () {
Route::post('/login', 'login');
Route::post('/register',  'register');
Route::post('/logout',  'logout');
Route::post('/refresh', 'refresh');
Route::get('/user-profile', 'userProfile');

});
Route::controller(ParentAuthController::class)->prefix('/parent')->group(
    function () {
Route::post('/login', 'login');
Route::post('/register',  'register');
Route::post('/logout',  'logout');
Route::post('/refresh', 'refresh');
Route::get('/user-profile', 'userProfile');

});

