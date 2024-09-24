<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Answer;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentExam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnswerRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\StudentResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class AnswerController extends Controller
{
//     public function create(AnswerRequest $request)
//     {
//         $exam = null;
//         $answers = [];
//         $student = null;

//         foreach ($request->answers as $answer) {
//             $createdAnswer = Answer::create([
//                 'student_id' => $request->student_id,
//                 'exam_id' => $request->exam_id,
//                 'question_id' => $answer['question_id'],
//                 'selected_choice' => $answer['selected_choice'],
//             ]);

//             $question = Question::with('exam')->find($answer['question_id']);
//             $is_correct = $question->correct_choice === $answer['selected_choice'];


//             if (!$exam) {
//                 $exam = new ExamResource($question->exam);
//             }

//             if (!$student) {
//                 $student = new StudentResource(Student::find($request->student_id));
//             }

//             $answers[] = [
//                 'question_id' => $question->id,
//                 'question_text' => $question->question,
//                 'choices' => [
//                     'choice_1' => $question->choice_1,
//                     'choice_2' => $question->choice_2,
//                     'choice_3' => $question->choice_3,
//                     'choice_4' => $question->choice_4,
//                 ],
//                 'correct_choice' => $question->correct_choice,
//                 'student_choice' => $answer['selected_choice'],
//                 'is_correct' => $is_correct,
//             ];
//         }

//         return response()->json([
//             'exam' => $exam,
//             'student' => $student,
//             'data' => $answers,
//             'message' => 'Answers submitted successfully.',
//         ]);
// }




}
