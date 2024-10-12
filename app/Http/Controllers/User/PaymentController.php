<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // public function pay()
    // {
    //     return view('store',[
    //         'payLink' =>auth()->guard('api')->user()->charge(12.99,'Action Figure')
    //     ]);
    // }

    public function pay()
{
    $user = auth()->guard('api')->user();

    $payLink = $user->charge(12, 'Action Figure');

    return response()->json([
        'payLink' => $payLink,
        'message' => 'Payment link generated successfully.',
    ], 200);
}

}
