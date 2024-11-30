<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;


Route::controller(RoleController::class)->prefix('/admin')->middleware('admin')->group(
    function () {
