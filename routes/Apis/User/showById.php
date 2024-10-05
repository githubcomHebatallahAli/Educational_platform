<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShowByIdController;

Route::controller(ShowByIdController::class)
->prefix('/student')
->middleware(['auth:api', 'checkCourseAccess'])

->group(
    function () {

   Route::get('/show/course/{id}/with/all/lessonsAndExams','studentShowCourse');

});

Route::controller(ShowByIdController::class)


->group(
    function () {

        Route::get('show/exam/{examId}/student/{studentId}/results', 'showExamResults');
        Route::get('show/students/{studentId}/courses/{courseId}/exam-results','getStudentExamResults');
        Route::get('show/students/{studentId}/courses/{courseId}/4Exam-results','getStudent4ExamsResult');
        Route::get('/show/overAllResults/student/{id}','getStudentOverallResults');
        Route::get('/show/his/sons/withOverAllResult/{id}','edit');


});
