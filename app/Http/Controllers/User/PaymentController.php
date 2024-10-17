<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use ReflectionClass;
use Illuminate\Http\Request;
use Laravel\Paddle\Checkout;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
// public function pay()
// {
//     $checkout = User::first()->checkout(['course']);

//     return view('pay', ['checkout' => $checkout]);


//     }

public function pay()
{
    $user = User::first();

    if (!$user) {
        // Handle the case where there are no users
        return redirect()->route('home')->with('error', 'No user found.');
    }

    $checkout = $user->checkout(['course']);

    if (!$checkout) {
        // Handle the case where checkout fails
        return redirect()->route('home')->with('error', 'Checkout process failed.');
    }

    return view('pay', ['checkout' => $checkout]);
}



}
