<?php

namespace App\Http\Controllers\User;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CourseResource;

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
    }

