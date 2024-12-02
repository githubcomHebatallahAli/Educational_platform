<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymobController;



Route::controller(PaymobController::class)->group(
    function () {


Route::post('/initiate-payment/{paymentType}', 'initiatePayment');

Route::post('generate-token', 'generateToken');
Route::post('create-intention', 'createIntention');
Route::post('post-payment', 'postPayment');
Route::post('checkout-url', 'generateCheckoutUrl');
Route::get('secret-key', 'getPaymobSecretKey')->middleware('admin');

});
