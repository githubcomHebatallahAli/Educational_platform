<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\StudentRegisterRequest;
use App\Http\Resources\Auth\StudentRegisterResource;


class StudentAuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','verify']]);
    }

    public function login(LoginRequest $request){
    	$validator = Validator::make($request->all(),$request->rules(),$request->messages()

        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->guard('api')->attempt($validator->validated())) {
            return response()->json(['message' => 'Invalid Data'], 422);
        }
        $user = auth()->guard('api')->user();

        // if (is_null($user->email_verified_at)) {
        //     return response()->json([
        //         'message' => 'Email not verified. Please verify it.'
        //     ], 403);
        // }
        return $this->createNewToken($token , $user);
    }


    public function register(StudentRegisterRequest $request) {
        $validator = Validator::make($request->all(), $request->rules()

        );
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
                // $user->notify(new EmailVerificationNotification());
        return response()->json([
            'message' => 'Registration successful!',
            'student' =>new StudentRegisterResource($user)
        ], 201);
    }


    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'Student successfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken(["data"=>auth()->refresh()
    ]);
    }

    public function userProfile() {
        return response()->json(["data"=>auth()->user()
    ]);
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'Student' => auth()->guard('api')->user(),
            // 'user' => User::with('role:id,name')->find(auth()->id()),
            // 'permissions' => User::find(auth()->id())->permissions(),
        ]);
    }
}
