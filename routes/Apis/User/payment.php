<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PaymentController;




Route::get('/pay', [PaymentController::class, 'pay']) ;



