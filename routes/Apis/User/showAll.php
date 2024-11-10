<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShowAllController;





Route::controller(ShowAllController::class)
->group(
    function () {
   Route::get('/showAll/courses/grade/{gradeId}', 'showAllCourses');
   Route::get('student/showAll/his/courses', 'studentShowAllHisCourses');
   Route::get('student/showAll/his/orders', 'studentShowHisOrders');
});
