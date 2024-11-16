<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Models\Course;

use App\Http\Controllers\Controller;

use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\User\OrderShowResource;


class ShowAllController extends Controller
{

        public function showAllCourses($gradeId)
        {
            $Courses = Course::where('status', 'active')
                             ->where('grade_id', $gradeId)
                             ->get();

            return response()->json([
                'data' => CourseResource::collection($Courses),
                'message' => "Show All Active Courses for by grade id Successfully."
            ]);
        }


        public function studentShowAllHisCourses()
        {
            $user = auth()->guard('api')->user();
            $admin = auth()->guard('admin')->user();

            if ($user) {
                $courses = $user->courses()
                ->wherePivot('status', 'paid')
                ->get();
                return response()->json([
                    'data' => CourseResource::collection($courses)
                ]);
            }

            if ($admin && $admin->role_id == 1) {
                $courses = Course::get();
                return response()->json([
                    'data' => CourseResource::collection($courses)
                ]);
            }

            return response()->json([
                'error' => 'Unauthorized access to courses.',
                'message' => "Student Show All His Courses Successfully."
            ]);
        }

        public function studentShowHisOrders()
        {
            $student = auth()->guard('api')->user();
            $admin = auth()->guard('admin')->user();

            if ($student) {
                $orders = Order::where('user_id', $student->id)->get();
            }
            elseif ($admin && $admin->role_id == 1) {
                $orders = Order::all();
            } else {
                return response()->json([
                    'message' => 'Unauthorized access: User not found or not authorized.'
                ]);
            }


            return response()->json([
                'data' => OrderShowResource::collection($orders),
                'message' => 'Orders retrieved successfully.'
            ]);
        }

    }

