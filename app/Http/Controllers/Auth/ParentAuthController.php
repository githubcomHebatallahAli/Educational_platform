<?php

namespace App\Http\Controllers\Auth;

use App\Models\Parnt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\ParentRegisterRequest;
use App\Http\Resources\Auth\ParentRegisterResource;

class ParentAuthController extends Controller
{
        // public function __construct()
    // {
    //     $this->middleware('auth:admin',
    //      ['except' => ['register','login','verify','logout']]);
    // }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $validator = Validator::make($request->all(), $request->rules());


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->guard('parnt')->attempt($validator->validated())) {
            return response()->json(['message' => 'Invalid data'], 422);

            $parent = auth()->guard('parnt')->user();

            // if (is_null($user->email_verified_at)) {
            //     return response()->json([
            //         'message' => 'Email not verified. Please verify it.'
            //     ], 403);
            // }
        }

        return $this->createNewToken($token);
    }

    /**
     * Register an Admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // Register an Admin.
    public function register(ParentRegisterRequest $request)
    {
        // if (!Gate::allows('create', Parnt::class)) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $parent = Parnt::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password), ]

        ));

        $parent->save();
        // $admin->notify(new EmailVerificationNotification());

        return response()->json([
            'message' => 'Admin Registration successful',
            'parent' =>new ParentRegisterResource($parent)
        ]);
    }




    /**
     * Log the admin out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (!Gate::allows('logout', Parnt::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        auth()->guard('parnt')->logout();
        return response()->json([
            'message' => 'Parent successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated Admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(["data" => auth()->user()]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $parent = Parnt::find(auth()->guard('parnt')->id());
        return response()->json([

            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('parnt')->factory()->getTTL() * 60,
            // 'admin' => auth()->guard('admin')->user(),
            //   'admin' => Admin::with('role:id,name')->find(auth()->id()),
            'parent' => $parent,
        ]);
    }
}
