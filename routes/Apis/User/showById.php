<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShowByIdController;

Route::controller(ShowByIdController::class)
->prefix('/student')
// ->middleware(['checkCourseAccess'])
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
        Route::get('/show/overAllResult/Rank/{id}','getStudentRankOverallResults');
        Route::get('/show/overAllResults/Rank/ForAllStudents/Grade/{gradeId}/course/{courseId}',
        'getRankAndOverAllResultsForAllStudents');
        Route::get('/show/overAllResults/Rank/ForTopThreeStudents/Grade/{gradeId}/course/{courseId}',
        'getRankAndOverAllResultsForTopThreeStudents');
        Route::get('student/show/his/PDF/student/{studentId}', 'getLessonPdf');
        Route::get('show/course/{courseId}', 'showCourse');
        Route::get('student/edit/profile/{id}', 'studentEditProfile');
        Route::get('student/show/his/lesson/{id}', 'showLessonById');
        Route::get('student/show/his/exam/{id}', 'showExamById');


});
