<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseController;


Route::controller(CourseController::class)
->prefix('/admin')
->middleware(['admin'])
->group(
    function () {

   Route::get('/showAll/course','showAll');
   Route::post('/create/course', 'create');
   Route::get('/edit/course/{id}','edit');
   Route::post('/update/course/{id}', 'update');
   Route::delete('/delete/course/{id}', 'destroy');
   Route::get('/showDeleted/course', 'showDeleted');
Route::get('/restore/course/{id}','restore');
Route::delete('/forceDelete/course/{id}','forceDelete');
Route::get('/show/course/{id}/with/all/lessonsAndExams','show');

Route::post('/add/student/to/course',  'attachStudentToCourse');
Route::get('/show/course/with/students/{id}',  'showCourseWithStudent');


   });


