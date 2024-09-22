<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LessonController;


Route::controller(LessonController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/lesson','showAll');
   Route::post('/create/lesson', 'create');
   Route::post('/addStudents/to/lesson', 'assignStudentsToLesson');
   Route::post('/revokeStudents/from/lesson', 'revokeAllStudentsFromLesson');
   Route::get('/edit/lesson/{id}','edit');
   Route::post('/update/lesson/{id}', 'update');
   Route::delete('/delete/lesson/{id}', 'destroy');
   Route::get('/showDeleted/lesson', 'showDeleted');
Route::get('/restore/lesson/{id}','restore');
Route::delete('/forceDelete/lesson/{id}','forceDelete');
Route::post('/addExam/to/lesson', 'assignExamToLesson');
Route::post('/revokeExam/from/lesson', 'revokeExamFromLesson');
   });
