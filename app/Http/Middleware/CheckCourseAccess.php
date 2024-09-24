<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = auth()->guard('api')->user();

        $courseId = $request->route('id');

         

        if ($user && $user->courses()->where('course_id', $courseId)->wherePivot('status', 'paid')->exists()) {
            return $next($request);
        }


        return response()->json(['error' => 'Unauthorized access to this course.'], 403);
    }
    }


