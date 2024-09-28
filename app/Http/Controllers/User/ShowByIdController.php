<?php

namespace App\Http\Controllers\User;

use Log;
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






    public function showExamResults($examId, $studentId)
{

$student = User::find($studentId);

if (!$student) {
    return response()->json(['message' => 'الطالب غير موجود.'], 404);
}


if (!$this->authorizeStudentOrParent($student)) {
    return response()->json(['message' => 'Unauthorized access.'], 403);
}

    $answers = Answer::with('question.exam')
        ->where('exam_id', $examId)
        ->where('user_id', $studentId)
        ->get();

    if ($answers->isEmpty()) {
        return response()->json([
            'message' => 'لا توجد إجابات لهذا الامتحان.'
        ], 404);
    }

    // حساب عدد الإجابات الصحيحة والدرجة
    $correctAnswers = 0;
    $totalQuestions = $answers->count();
    $exam = null;
    $answersDetail = [];

    foreach ($answers as $answer) {
        $question = $answer->question;
        $is_correct = $question->correct_choice === $answer->selected_choice;

        if ($is_correct) {
            $correctAnswers++;
        }

        // أول مرة، نجلب تفاصيل الامتحان
        if (!$exam) {
            $exam = new ExamResource($question->exam);
        }

        $answersDetail[] = [
            'question_id' => $question->id,
            'question_text' => $question->question,
            'choices' => [
                'choice_1' => $question->choice_1,
                'choice_2' => $question->choice_2,
                'choice_3' => $question->choice_3,
                'choice_4' => $question->choice_4,
            ],
            'correct_choice' => $question->correct_choice,
            'student_choice' => $answer->selected_choice,
            'is_correct' => $is_correct,
        ];
    }

    // حساب النسبة المئوية للدرجة
    $score = ($correctAnswers / $totalQuestions) * 100;

    // استرجاع تفاصيل الطالب
    $studentResource = new StudentRegisterResource($student);

    return response()->json([
        'exam' => $exam,
        'student' => $studentResource,
        'data' => $answersDetail,
        'score' => $score, // عرض الدرجة في الـ API
        'message' => 'تم عرض نتائج الامتحان بنجاح.',
    ]);

}

protected function authorizeStudentOrParent($student)
{
    $user = auth()->guard('api')->user();
    if ($user && $user->id === $student->id) {
        return true;
    }

    $parnt = auth()->guard('parnt')->user();
    if ($parnt && $parnt->id === $student->parnt_id) {
        return true;
    }

    return false;
}

// public function showExamResults1($examId, $studentId, $parntId)
// {
//     $student = User::find($studentId);
//     if (!$student) {
//         return response()->json(['message' => 'الطالب غير موجود.'], 404);
//     }

//     // تحقق من توثيق الطالب
//     $user = auth()->guard('api')->user();
//     if ($user) {
//         if ($user->id !== $student->id) {
//             return response()->json(['message' => 'Unauthorized access.'], 403);
//         }
//     } else {
//         // تحقق من توثيق ولي الأمر
//         $parnt = auth()->guard('parnt')->user();
//         if (!$parnt) {
//             return response()->json(['message' => 'Unauthorized access parent.'], 403);
//         }

//         // تحقق من المطابقة مع ID ولي الأمر المدخل
//         if ($parnt->id !== $parntId) {
//             return response()->json([
//                 'message' => 'Unauthorized access parent.',
//                 'entered_parent_id' => $parntId,
//                 'student_id' => $student->id,
//                 'expected_parent_id' => $parnt->id
//             ], 403);
//         }

//         return response()->json(['message' => 'Parent authenticated.', 'parnt_id' => $parnt->id]);
//     }


//     $answers = Answer::with('question.exam')
//         ->where('exam_id', $examId)
//         ->where('user_id', $studentId)
//         ->get();

//     if ($answers->isEmpty()) {
//         return response()->json([
//             'message' => 'لا توجد إجابات لهذا الامتحان.'
//         ], 404);
//     }

//     // حساب عدد الإجابات الصحيحة والدرجة
//     $correctAnswers = 0;
//     $totalQuestions = $answers->count();
//     $exam = null;
//     $answersDetail = [];

//     foreach ($answers as $answer) {
//         $question = $answer->question;
//         $is_correct = $question->correct_choice === $answer->selected_choice;

//         if ($is_correct) {
//             $correctAnswers++;
//         }

//         // أول مرة، نجلب تفاصيل الامتحان
//         if (!$exam) {
//             $exam = new ExamResource($question->exam);
//         }

//         $answersDetail[] = [
//             'question_id' => $question->id,
//             'question_text' => $question->question,
//             'choices' => [
//                 'choice_1' => $question->choice_1,
//                 'choice_2' => $question->choice_2,
//                 'choice_3' => $question->choice_3,
//                 'choice_4' => $question->choice_4,
//             ],
//             'correct_choice' => $question->correct_choice,
//             'student_choice' => $answer->selected_choice,
//             'is_correct' => $is_correct,
//         ];
//     }

//     // حساب النسبة المئوية للدرجة
//     $score = ($correctAnswers / $totalQuestions) * 100;

//     // استرجاع تفاصيل الطالب
//     $studentResource = new StudentRegisterResource($student);

//     return response()->json([
//         'exam' => $exam,
//         'student' => $studentResource,
//         'data' => $answersDetail,
//         'score' => $score, // عرض الدرجة في الـ API
//         'message' => 'تم عرض نتائج الامتحان بنجاح.',
//     ]);

// }


// public function testAuth()
// {
//     $user = auth()->guard('api')->user();
//     $parnt = auth()->guard('parnt')->user();

//     return response()->json([
//         'user' => $user,
//         'parnt' => $parnt
//     ]);
// }


}
