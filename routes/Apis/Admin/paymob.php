<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymobController;



Route::controller(PaymobController::class)->group(
    function () {
Route::post('/paymob/initiate',  'initiatePayment')->name('paymob.initiate');
Route::post('/paymob/callback', 'handlePaymentCallback')->name('paymob.callback');
Route::post('/paymob/webhook','handleWebhook')->name('paymob.webhook');
Route::post('/payments/create-intent', 'createPaymentIntent');

Route::post('/initiate-payment/{paymentType}', 'initiatePayment');

});
