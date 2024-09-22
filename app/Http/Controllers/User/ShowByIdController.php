<?php

namespace App\Http\Controllers\User;

use App\Models\Exam;
use App\Models\User;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Question;
use App\Models\StudentExam;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnswerRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Auth\StudentRegisterResource;
use App\Http\Resources\Admin\CourseWithLessonsExamsResource;

class ShowByIdController extends Controller
{
    public function studentShowCourse($id)
    {

        $course = Course::with(['lessons.exam.questions'])->findOrFail($id);
        return response()->json([
       'data' =>new CourseWithLessonsExamsResource($course)
        ]);
    }

    public function create(AnswerRequest $request)
{
    $exam = Exam::find($request->exam_id);

    if (!$exam) {
        return response()->json(['message' => 'Exam not found.'], 404);
    }

    $courseId = $exam->course_id;

    // التحقق من الدفع
    $studentPaid = StudentCourse::where('user_id', $request->user_id)
                               ->where('course_id', $courseId) // استخدم $courseId المستخرج من الامتحان
                               ->where('status', 'paid') // تأكيد حالة الدفع
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
    $correctAnswers = 0; // عدد الإجابات الصحيحة
    $totalQuestions = count($request->answers); // العدد الإجمالي للأسئلة

    foreach ($request->answers as $answer) {
        $createdAnswer = Answer::create([
            'user_id' => $request->user_id,
            'exam_id' => $request->exam_id,
            'question_id' => $answer['question_id'],
            'selected_choice' => $answer['selected_choice'],
        ]);

        // الحصول على السؤال ومقارنته بالإجابة الصحيحة
        $question = Question::with('exam')->find($answer['question_id']);
        $is_correct = $question->correct_choice === $answer['selected_choice'];

        // إذا كانت الإجابة صحيحة، يتم زيادة عدد الإجابات الصحيحة
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

    // حساب النسبة المئوية للدرجة
    $score = ($correctAnswers / $totalQuestions) * 100;

    // تحديث جدول student_exams لتسجيل درجة الطالب وحالة المحاولة
    StudentExam::updateOrCreate(
        ['user_id' => $request->user_id, 'exam_id' => $request->exam_id],
        ['score' => $score, 'has_attempted' => true]
    );

    return response()->json([
        'exam' => $exam,
        'student' => $student,
        'data' => $answers,
        'score' => $score, // عرض الدرجة في الـ API
        'message' => 'Answers submitted and scored successfully.',
    ]);
}

}
