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

        // إجمالي مبيعات الكلية
        $totalSales = Order::join('courses', 'orders.course_id', '=', 'courses.id')
            ->where('orders.status', 'paid')
            ->sum('courses.price');

        // مبيعات كل كورس على حدة
        $salesPerCourse = Course::withCount(['orders as paid_orders_count' => function ($query) {
            $query->where('status', 'paid');
        }])->get(['id', 'nameOfCourse', 'price'])->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->nameOfCourse,
                'price' => $course->price,
                'paid_orders_count' => $course->paid_orders_count,
                'total_sales' => $course->paid_orders_count * $course->price,
            ];
        });

        $gradesStatistics = Grade::withCount([
            'users as students_count', 
            'courses as courses_count',
            'courses as lessons_count' => function ($query) {
                $query->withCount('lessons');
            },
            'courses as exams_count' => function ($query) {
                $query->withCount('exams');
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
                'Total_sales' => $totalSales,
            ],
            'Grades_statistics' => $gradesStatistics,
            'Courses_sales' => $salesPerCourse,
        ];

        return response()->json($statistics);
    }
}
