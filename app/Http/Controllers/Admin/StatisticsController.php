<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Grade;
use App\Models\Order;
use App\Models\Parnt;
use App\Models\Course;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function showStatistics()
    {
        $this->authorize('manage_users');
        $coursesCount = Course::count();
        $usersCount = User::count();
        $parentsCount = Parnt::count();
        // $ordersCount = Order::count();
        $paidOrdersCount = Order::where('status', 'paid')->count();

        $gradesStatistics = Grade::withCount([
            'users as students_count', // عدد الطلاب في المرحلة
            'courses as courses_count', // عدد الكورسات في المرحلة
            'courses as lessons_count' => function ($query) {
                $query->withCount('lessons'); // عدد الدروس في كل الكورسات
            },
            'courses as exams_count' => function ($query) {
                $query->withCount('exams'); // عدد الامتحانات في كل الكورسات
            },
        ])->get()->map(function ($grade) {
            return [
                'grade_id' => $grade->id,
                'grade_name' => $grade->grade,
                'students_count' => $grade->students_count,
                'courses_count' => $grade->courses_count,
                'lessons_count' => $grade->lessons_count,
                'exams_count' => $grade->exams_count,
            ];
        });


        $statistics = [
            'General_statistics' => [
                'Courses_count' => $coursesCount,
                'Users_count' => $usersCount,
                'Parents_count' => $parentsCount,
                'Paid_orders_count' => $paidOrdersCount,
            ],
            'Grades_statistics' => $gradesStatistics,
        ];

        return response()->json($statistics);
    }
}
