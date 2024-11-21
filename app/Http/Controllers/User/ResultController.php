<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Http\Resources\StudentResultResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class ResultController extends Controller
{

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

    $admin = auth()->guard('admin')->user();
    if ($admin && $admin->role_id == 1) {
        return true;
    }

    return false;
}

    public function studentShowResultOf5Exams($studentId, $courseId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }


    $studentWithExams = User::with(['exams' => function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    }])->findOrFail($studentId);




    $fourExams = $studentWithExams->exams->take(4);


    $fourExamResults = $fourExams->map(function ($exam) {
        return [
            'exam_id' => $exam->id,
            'test_id' => $exam->test->id ?? null,
            'test_name' => $exam->test->name ?? null,

            'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
            'has_attempted' => $exam->pivot->has_attempted,
        ];
    })->toArray();


    $finalExam = $studentWithExams->exams->last();


    $finalExamResult = $finalExam ? [
        'exam_id' => $finalExam->id,
        'test_id' => $finalExam->test->id ?? null,
        'test_name' => $finalExam->test->name ?? null,
        'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
        'has_attempted' => $finalExam->pivot->has_attempted,
    ] : null;

    return response()->json([

        'four_exam_results' => $fourExamResults,
        'final_exam_result' => $finalExamResult,
    ], 200, [], JSON_PRETTY_PRINT);
}


    public function parentOrAdminShowResultOf5Exams($studentId, $courseId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }


    $studentWithExams = User::with(['exams' => function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    }])->findOrFail($studentId);




    $fourExams = $studentWithExams->exams->take(4);


    $fourExamResults = $fourExams->map(function ($exam) {
        return [
            'exam_id' => $exam->id,
            'test_id' => $exam->test->id ?? null,
            'test_name' => $exam->test->name ?? null,
            'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
            'has_attempted' => $exam->pivot->has_attempted,
        ];
    })->toArray();


    $finalExam = $studentWithExams->exams->last();
    $finalExamResult = $finalExam ? [
        'exam_id' => $finalExam->id,
        'test_id' => $finalExam->test->id ?? null,
        'test_name' => $finalExam->test->name ?? null,
        'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
        'has_attempted' => $finalExam->pivot->has_attempted,
    ] : null;

    return response()->json([
        'student' => new StudentRegisterResource($student),
        'four_exam_results' => $fourExamResults,
        'final_exam_result' => $finalExamResult,
    ]);
}

public function parentOrAdminShowExamResults($studentId, $courseId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }


$student = User::with(['exams' => function ($query) use ($courseId) {
    $query->where('course_id', $courseId);
}])->findOrFail($studentId);

$fourExams = $student->exams
->take(4);

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
    'student' => new ResultResource($student),
    'four_exam_results' => $fourExamResults,
    // 'total_percentage_for_four_exams' => round($totalPercentageForFourExams, 2),
    'final_exam_result' => $finalExamResult,
    'overall_total_percentage' => round($overallTotalPercentage, 2),
]);

}

public function studentShowAll5ExamResultsOfAllCourses($studentId)
{
    // استرجاع الطالب مع الاختبارات والكورسات المرتبطة به
    $studentWithExams = User::with(['exams.course.month' => function ($query) {
        // لا نحدد course_id هنا لأننا نريد استرجاع جميع الكورسات
    }])->findOrFail($studentId);

    // معالجة نتائج الاختبارات لكل كورس
    $coursesResults = $studentWithExams->exams->groupBy('course_id')->map(function ($exams, $courseId) {
        // استرجاع بيانات الدورة الشهرية (month_id و month_name)
        $course = $exams->first()->course;
        $monthId = $course->month_id;
        $monthName = $course->month->name ?? 'غير معروف'; // تأكد من أن لديك علاقة مع الـ Month في موديل Course

        // معالجة نتائج الاختبارات (4 اختبارات فقط)
        $fourExamResults = $exams->take(4)->map(function ($exam) {
            return [
                'exam_id' => $exam->id,
                'test_id' => optional($exam->test)->id,
                'test_name' => optional($exam->test)->name,
                'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
                'has_attempted' => $exam->pivot->has_attempted ?? false,
            ];
        })->toArray();

        // الحصول على نتيجة الاختبار النهائي (الاختبار الأخير)
        $finalExam = $exams->last();
        $finalExamResult = $finalExam ? [
            'exam_id' => $finalExam->id,
            'test_id' => optional($finalExam->test)->id,
            'test_name' => optional($finalExam->test)->name,
            'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
            'has_attempted' => $finalExam->pivot->has_attempted ?? false,
        ] : null;

        return [
            'course_id' => $courseId,
            'month_id' => $monthId,        // إضافة month_id
            'month_name' => $monthName,    // إضافة month_name
            'four_exam_results' => $fourExamResults,
            'final_exam_result' => $finalExamResult,
        ];
    })->values()->toArray();

    // إرجاع الريسبونس مع جميع نتائج الامتحانات
    return response()->json([
        'data' => $coursesResults,
    ], 200, [], JSON_PRETTY_PRINT);
}







}
