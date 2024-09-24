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
->prefix('/student')
// ->middleware('auth:api')

->group(
    function () {

        Route::get('show/exam/{examId}/student/{studentId}/results', 'showExamResults');
        Route::get('show/exam/{examId}/student/{studentId}/parent/{parentId}/results', 'showExamResults1');
        Route::get('show', 'testAuth');

});
