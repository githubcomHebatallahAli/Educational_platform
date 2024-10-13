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
public function pay()
{
    $checkout = User::first()->checkout(['course']);

    return view('pay', ['checkout' => $checkout]);


    }


}
