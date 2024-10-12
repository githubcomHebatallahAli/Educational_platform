<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PaymentController;



Route::post('/pay', [PaymentController::class, 'pay']) ;
