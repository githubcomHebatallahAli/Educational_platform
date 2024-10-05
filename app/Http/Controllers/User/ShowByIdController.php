<?php

namespace App\Http\Controllers\User;

use Log;
use App\Models\Exam;
use App\Models\User;
use App\Models\Parnt;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Question;
use App\Models\StudentExam;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnswerRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Resources\StudentResultResource;
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


public function getStudentExamResults($studentId, $courseId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود.'], 404);
    }

    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json(['message' => 'Unauthorized access.'], 403);
    }


$student = User::with(['exams' => function ($query) use ($courseId) {
    $query->where('course_id', $courseId);
}])->findOrFail($studentId);

$fourExams = $student->exams->take(4);

$fourExamResults = $fourExams->map(function ($exam) {
    return [
        'exam_id' => $exam->id,
        'title' => $exam->title,
        'score' => $exam->pivot->has_attempted ? $exam->pivot->score
        : 'absent',
        'has_attempted' => $exam->pivot->has_attempted,
    ];
})->toArray();

$finalExam = $student->exams->last();
$finalExamResult = [
    'exam_id' => $finalExam->id,
    'title' => $finalExam->title,
    'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score :
     'absent',
    'has_attempted' => $finalExam->pivot->has_attempted,
];
$totalScore = 0;
$attemptedCount = 0;

foreach ($fourExams as $exam) {
    if ($exam->pivot->has_attempted) {
        $totalScore += $exam->pivot->score;
        $attemptedCount++;
    }
}


$totalPercentageForFourExams = ($totalScore / (4 * 100)) * 100;
$overallTotalScore = $totalScore + ($finalExam->pivot->has_attempted ?
 $finalExam->pivot->score : 0);
$totalExamsCount = $attemptedCount +
 ($finalExam->pivot->has_attempted ? 1 : 0);

$overallTotalPercentage = ($overallTotalScore / (5 * 100)) * 100;

return response()->json([
    'student' => new StudentResultResource($student),
    'four_exam_results' => $fourExamResults,
    'total_percentage_for_four_exams' => round($totalPercentageForFourExams, 2),
    'final_exam_result' => $finalExamResult,
    'overall_total_percentage' => round($overallTotalPercentage, 2),
]);

}

public function getStudent4ExamsResult($studentId, $courseId)
{
    $student = User::find($studentId);

    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود.'], 404);
    }


    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json(['message' => 'Unauthorized access.'], 403);
    }

    $student = User::with(['grade', 'parent'])->findOrFail($studentId);


    $fourExams = $student->exams()->where('course_id', $courseId)->take(4)->get();

    $fourExamResults = $fourExams->map(function ($exam) {
        $score = $exam->pivot->score;
        $hasAttempted = $exam->pivot->has_attempted;


        $resultScore = ($score === null && $hasAttempted == 0) ? 'absent' : ($hasAttempted ? $score : 'absent');

        return [
            'exam_id' => $exam->id,
            'title' => $exam->title,
            'score' => $resultScore,
            'has_attempted' => $hasAttempted,
        ];
    });

    $totalScore = 0;
    $attemptedCount = 0;

    foreach ($fourExams as $exam) {
        if ($exam->pivot->has_attempted) {
            $totalScore += $exam->pivot->score ?? 0;
            $attemptedCount++;
        }
    }

    $totalPercentageForFourExams = ($totalScore / (4 * 100)) * 100;

    return response()->json([
        'student' => new StudentRegisterResource($student),
        'four_exam_results' => $fourExamResults,
        'total_percentage_for_four_exams' => round($totalPercentageForFourExams, 2),
    ]);

}


public function getStudentOverallResults($studentId)
{

    $student = User::findOrFail($studentId);
    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود.'], 404);
    }


    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json(['message' => 'Unauthorized access.'], 403);
    }

    $totalOverallScore = 0;
    $totalMaxScore = 0;

    $courses = $student->courses()->with('exams')->get();

    foreach ($courses as $course) {
        foreach ($course->exams as $exam) {

            $studentExam = $exam->students()->where('user_id', $studentId)->first();

            if ($studentExam && !is_null($studentExam->pivot->score)) {
                $totalOverallScore += $studentExam->pivot->score;
            }
            $totalMaxScore += 100;
        }
    }


    $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;
    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'img' => $student->img,
            'grade' => new GradeResource($student->grade),
        ],
        'overall_score_percentage' => round($overallScorePercentage, 2),
    ]);
}


public function edit(string $id)
{
    $authenticatedParent = auth()->guard('parnt')->user();

    if ($authenticatedParent->id != $id) {
        return response()->json([
            'message' => "Unauthorized access. You can only view your own data."
        ], 403);
    }


    $Parent = Parnt::with('users')->find($id);

    if (!$Parent) {
        return response()->json([
            'message' => "Parent not found."
        ], 404);
    }

    $sonsData = $Parent->users->map(function ($son) {

        $totalOverallScore = 0;
        $totalMaxScore = 0;

        $courses = $son->courses()->with('exams')->get();

        foreach ($courses as $course) {
            foreach ($course->exams as $exam) {

                $studentExam = $exam->students()->where('user_id', $son->id)->first();

                if ($studentExam && !is_null($studentExam->pivot->score)) {
                    $totalOverallScore += $studentExam->pivot->score;
                }
                $totalMaxScore += 100;
            }
        }

        $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

        return [
            'id' => $son->id,
            'name' => $son->name,
            // 'email' => $son->email,
            'img' => $son->img,
            'grade' => new GradeResource($son->grade),
            'overall_score_percentage' => round($overallScorePercentage, 2),
        ];
    });

    return response()->json([
        'parent' => [
            'id' => $Parent->id,
            'name' => $Parent->name,
            'email' => $Parent->email,
        ],
        'sons' => $sonsData,
        'message' => "Edit Parent By ID Successfully."
    ]);
}
}
