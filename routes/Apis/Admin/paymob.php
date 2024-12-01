<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymobController;



Route::controller(PaymobController::class)->group(
    function () {


Route::post('/initiate-payment/{paymentType}', 'initiatePayment');

Route::post('/api/generate-token', 'generateToken');
Route::post('/api/create-intention', 'createIntention');
Route::post('/api/post-payment', 'postPayment');
Route::post('/api/checkout-url', 'generateCheckoutUrl');


});
