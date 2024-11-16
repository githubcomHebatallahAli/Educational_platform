<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ResultController;

Route::controller(ResultController::class)
->group(
    function () {
        Route::get('student/{studentId}/show/courses/{courseId}/5Exam-results','studentShowResultOf5Exams');
        Route::get('parent/student/{studentId}/show/courses/{courseId}/5Exam-results','parentOrAdminShowResultOf5Exams');
        Route::get('students/{studentId}/courses/{courseId}/exam-results','parentOrAdminShowExamResults');
    });
