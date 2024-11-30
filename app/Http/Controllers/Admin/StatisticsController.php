<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Course;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function showStatistics()
    {
        $this->authorize('manage_users');
        $coursesCount = Course::count();
        $usersCount = User::count();
        $parentsCount = Parent::count();
        $ordersCount = Order::count();
        $statistics = [
            'Courses_count' =>  $coursesCount,
            'Users_count' => $usersCount,
            'Parents_count' => $parentsCount,
            'Orders_count' => $ordersCount,

        ];

        return response()->json($statistics);
    }
}
