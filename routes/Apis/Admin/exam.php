<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;



Route::controller(ExamController::class)->prefix('/admin')->middleware('admin')->group(
    function () {

   Route::get('/showAll/exam','showAll');
   Route::post('/create/exam', 'create');
   Route::post('/addStudents/to/exam','assignStudentsToExam');
   Route::get('/edit/exam/{id}','edit');
   Route::post('/update/exam/{id}', 'update');
   Route::delete('/delete/exam/{id}', 'destroy');
   Route::get('/showDeleted/exam', 'showDeleted');
Route::get('/restore/exam/{id}','restore');
Route::delete('/forceDelete/exam/{id}','forceDelete');
Route::get('/exam/{examId}/questions',  'showExamQuestions');
Route::get('show/exam/{examId}/student/{studentId}/results', 'showExamResults');
   });
