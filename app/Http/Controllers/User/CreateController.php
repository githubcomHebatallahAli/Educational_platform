<?php

namespace App\Http\Controllers\User;

use App\Models\Exam;
use App\Models\User;
use App\Models\Answer;
use App\Models\Question;
use App\Models\ContactUs;
use App\Models\StudentExam;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Requests\Admin\AnswerRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class CreateController extends Controller
{
    public function create(AnswerRequest $request)
{
    $exam = Exam::find($request->exam_id);

    if (!$exam) {
        return response()->json(['message' => 'Exam not found.'], 404);
    }

    $courseId = $exam->course_id;
    $student = User::find($request->user_id);


    $studentPaid = StudentCourse::where('user_id', $request->user_id)
                          ->where('course_id', $courseId)
                          ->where('status', 'paid')
                          ->first();

    if (!$studentPaid) {
        return response()->json([
            'message' => 'You have not paid for this course and cannot take the exam.'
        ], 403);
    }

        $studentExam = StudentExam::where('user_id', $request->user_id)
                              ->where('exam_id', $request->exam_id)
                              ->where('has_attempted', true)
                              ->first();
    if ($studentExam) {
        return response()->json([
            'message' => 'You have already taken this exam and cannot take it again.'
        ], 403);
    }


    $exam = null;
    $answers = [];
    $student = null;
    $correctAnswers = 0;
    $totalQuestions = count($request->answers);

    foreach ($request->answers as $answer) {
        $createdAnswer = Answer::create([
            'user_id' => $request->user_id,
            'exam_id' => $request->exam_id,
            'question_id' => $answer['question_id'],
            'selected_choice' => $answer['selected_choice'],
        ]);


        $question = Question::with('exam')->find($answer['question_id']);
        $is_correct = $question->correct_choice === $answer['selected_choice'];


        if ($is_correct) {
            $correctAnswers++;
        }

        if (!$exam) {
            $exam = new ExamResource($question->exam);
        }

        if (!$student) {
            $student = new StudentRegisterResource(User::find($request->user_id));
        }

        $answers[] = [
            'question_id' => $question->id,
            'question_text' => $question->question,
            'choices' => [
                'choice_1' => $question->choice_1,
                'choice_2' => $question->choice_2,
                'choice_3' => $question->choice_3,
                'choice_4' => $question->choice_4,
            ],
            'correct_choice' => $question->correct_choice,
            'student_choice' => $answer['selected_choice'],
            'is_correct' => $is_correct,
        ];
    }


    $score = ($correctAnswers / $totalQuestions) * 100;

    StudentExam::updateOrCreate(
        ['user_id' => $request->user_id, 'exam_id' => $request->exam_id],
        ['score' => $score, 'has_attempted' => true]
    );

    return response()->json([
        'exam' => $exam,
        'student' => $student,
        'data' => $answers,
        'score' => $score,
        'message' => 'Answers submitted and scored successfully.',
    ]);


}

public function createContactUs(ContactRequest $request)
{
       $Contact =ContactUs::create ([
            "name" => $request->name,
            "phoneNumber" => $request->phoneNumber,
            "message" => $request->message,
        ]);
       $Contact->save();
       return response()->json([
        'data' =>new ContactResource($Contact),
        'message' => "Contact Created Successfully."
    ]);

    }
}
