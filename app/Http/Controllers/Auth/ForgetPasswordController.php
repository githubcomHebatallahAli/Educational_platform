<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Models\Parnt;
use App\Http\Controllers\Controller;
use App\Notifications\ResetPasswordNotification;
use App\Http\Requests\Auth\ForgetPasswordRequest;

class ForgetPasswordController extends Controller
{
    public function forgotPassword(ForgetPasswordRequest $request){
        $input = $request->only('email');
        $user = User::where('email',$input)->first();
        $admin = Admin::where('email',$input)->first();
        $parent = Parnt::where('email',$input)->first();
        if (!$user && !$admin && !$parent) {
            return response()->json([
                'message' => "User or admin or parent not found."
            ], 404);
        }

        if ($user) {
            $user->notify(new ResetPasswordNotification());
        }


        if ($admin) {
             $admin->notify(new ResetPasswordNotification());
        }
        
        if ($parent) {
             $parent->notify(new ResetPasswordNotification());
        }

        return response()->json([
            'message' => "Please check your email."
        ]);

    }
}
