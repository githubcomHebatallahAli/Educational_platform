<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::controller(ChatController::class)->group(
    function () {

   Route::post('create/chat','startChat');
   Route::post('create/message','sendMessage');
   Route::get('show/messages/chat/{chatId}','getMessages');

});
