<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneralMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (auth()->guard('api')->check() ||
        //     auth()->guard('parnt')->check() ||
        //     auth()->guard('admin')->check()) {
        //     return $next($request);
        // }

        // return response()->json(['error' => 'Unauthorized'], 401);

        $studentId = $request->route('user_id'); // افترض أنك تمرر student_id في الرابط

        // تحقق من أن المستخدم هو الطالب أو ولي أمره
        if (auth()->guard('api')->check() && auth()->guard('api')->id() == $studentId) {
            return $next($request);
        }

        if (auth()->guard('parnt')->check() && auth()->guard('parnt')->user()->hasStudent($studentId)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized pppp'], 401);
    }
    }

