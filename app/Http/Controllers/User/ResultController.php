<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
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

protected function authorizeParentOrAdmin($student)
{
    $parent = auth()->guard('parnt')->user();

    if ($parent && $parent->id === $student->parent_id) {
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

    $fourExams = $studentWithExams->exams
    ->whereIn('test_id', [1, 2, 3, 4])
    ->values();

    $fourExamResults = $fourExams->map(function ($exam) {
        return [
            'exam_id' => $exam->id,
            'test_id' => $exam->test->id ?? null,
            'test_name' => $exam->test->name ?? null,

            'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
            'has_attempted' => $exam->pivot->has_attempted,
        ];
    })->toArray();


    $finalExam = $studentWithExams->exams->firstWhere('test_id', 5);


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

    if (!$this->authorizeParentOrAdmin($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }


    $studentWithExams = User::with(['exams' => function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    }])->findOrFail($studentId);

    $fourExams = $studentWithExams->exams
    ->whereIn('test_id', [1, 2, 3, 4])
    ->values();


    $fourExamResults = $fourExams->map(function ($exam) {
        return [
            'exam_id' => $exam->id,
            'test_id' => $exam->test->id ?? null,
            'test_name' => $exam->test->name ?? null,
            'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
            'has_attempted' => $exam->pivot->has_attempted,
        ];
    })->toArray();


    $finalExam = $studentWithExams->exams->firstWhere('test_id', 5);
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

// public function parentOrAdminShowExamResults($studentId, $courseId)
// {
//     $student = User::find($studentId);
//     if (!$student) {
//         return response()->json([
//             'message' => 'الطالب غير موجود.'
//         ]);
//     }

//     if (!$this->authorizeStudentOrParent($student)) {
//         return response()->json([
//             'message' => 'Unauthorized access.'
//         ]);
//     }


// $student = User::with(['exams' => function ($query) use ($courseId) {
//     $query->where('course_id', $courseId);
// }])->findOrFail($studentId);

// $fourExams = $student->exams
// ->take(4);

// $fourExamResults = $fourExams->map(function ($exam) {
//     return [
//         'exam_id' => $exam->id,
//         'title' => $exam->title,
//         'score' => $exam->pivot->has_attempted ? $exam->pivot->score
//         : 'absent',
//         'has_attempted' => $exam->pivot->has_attempted,
//     ];
// })->toArray();

// $finalExam = $student->exams->last();
// $finalExamResult = [
//     'exam_id' => $finalExam->id,
//     'title' => $finalExam->title,
//     'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score :
//      'absent',
//     'has_attempted' => $finalExam->pivot->has_attempted,
// ];
// $totalScore = 0;
// $attemptedCount = 0;

// foreach ($fourExams as $exam) {
//     if ($exam->pivot->has_attempted) {
//         $totalScore += $exam->pivot->score;
//         $attemptedCount++;
//     }
// }


// $totalPercentageForFourExams = ($totalScore / (4 * 100)) * 100;
// $overallTotalScore = $totalScore + ($finalExam->pivot->has_attempted ?
//  $finalExam->pivot->score : 0);
// $totalExamsCount = $attemptedCount +
//  ($finalExam->pivot->has_attempted ? 1 : 0);

// $overallTotalPercentage = ($overallTotalScore / (5 * 100)) * 100;

// return response()->json([
//     'student' => new ResultResource($student),
//     'four_exam_results' => $fourExamResults,
//     // 'total_percentage_for_four_exams' => round($totalPercentageForFourExams, 2),
//     'final_exam_result' => $finalExamResult,
//     'overall_total_percentage' => round($overallTotalPercentage, 2),
// ]);

// }


public function parentOrAdminShowExamResults($studentId, $courseId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeParentOrAdmin($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }
    $course = Course::find($courseId);
    if (!$course) {
        return response()->json([
            'message' => 'الكورس غير موجود.'
        ]);
    }
    $course->loadCount('students');

    // جلب درجات الطالب
    $student = User::with(['exams' => function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    }])->findOrFail($studentId);



    $fourExams = $student->exams
    ->whereIn('test_id', [1, 2, 3, 4])
    ->values();
    $finalExam = $student->exams->firstWhere('test_id', 5);

    // حساب درجات الطالب (اعتبار الامتحانات الغير محضورة كـ 0)
    $totalScore = $fourExams->sum(function ($exam) {
        return $exam->pivot->has_attempted ? $exam->pivot->score : 0; // إذا لم يتم الحضور يتم استخدام 0
    });
    $finalScore = $finalExam && $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 0; // نفس الشيء هنا

    // حساب النتيجة النهائية
    $overallTotalScore = $totalScore + $finalScore;
    $overallTotalPercentage = ($overallTotalScore / (5 * 100)) * 100; // 5 اختبارات وكل اختبار درجته 100

    // جلب جميع الطلاب في الكورس وحساب درجاتهم
    $studentsInCourse = User::whereHas('exams', function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    })->with(['exams' => function ($query) use ($courseId) {
        $query->where('course_id', $courseId);
    }])->get();

    $studentsScores = $studentsInCourse->map(function ($student) {
        // حساب الدرجات لجميع الطلاب في الكورس
        $totalScore = $student->exams->sum(function ($exam) {
            return $exam->pivot->has_attempted ? $exam->pivot->score : 0;
        });
        return [
            'user_id' => $student->id,
            'total_score' => $totalScore,
        ];
    });

    // ترتيب الطلاب بناءً على الدرجات
    $studentsScores = $studentsScores
    ->sortByDesc('total_score')->values();

    // إيجاد ترتيب الطالب الحالي
    $studentRank = $studentsScores->
    search(fn($score) => $score['user_id'] === $studentId) + 1;

    return response()->json([
        'data' => new ResultResource($student),
        'four_exam_results' => $fourExams->map(fn($exam) => [
            'exam_id' => $exam->id,
            'title' => $exam->title,
            'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
            'has_attempted' => $exam->pivot->has_attempted,
        ]),
        'final_exam_result' => $finalExam ? [
            'exam_id' => $finalExam->id,
            'title' => $finalExam->title,
            'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
            'has_attempted' => $finalExam->pivot->has_attempted,
        ] : null,
        'overall_total_percentage' => round($overallTotalPercentage, 2),
        'students_count' => $course->students_count,
        'student_rank' => $studentRank,
    ]);
}


public function studentShowAll5ExamResultsOfAllCourses($studentId)
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
    $studentWithExams = User::with(['exams.course.month' => function ($query) {

    }])->findOrFail($studentId);

    $coursesResults = $studentWithExams->exams
    ->groupBy('course_id')->map(function ($exams, $courseId) {

        $course = $exams->first()->course;
        $monthId = $course->month_id;
        $monthName = $course->month->name ?? 'غير معروف';

        $fourExamResults = collect([1, 2, 3, 4])->map(function ($testId) use ($exams) {
            $exam = $exams->firstWhere('test_id', $testId);
            return $exam ? [
                'exam_id' => $exam->id,
                'test_id' => $exam->test_id,
                'test_name' => optional($exam->test)->name,
                'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
                'has_attempted' => $exam->pivot->has_attempted ?? false,
            ] : null;
        // })->filter()->toArray();
    })->filter()->values()->toArray();

        $finalExam = $exams->firstWhere('test_id', 5);
        $finalExamResult = $finalExam ? [
            'exam_id' => $finalExam->id,
            'test_id' => $finalExam->test_id,
            'test_name' => optional($finalExam->test)->name,
            'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
            'has_attempted' => $finalExam->pivot->has_attempted ?? false,
        ] : null;

        return [
            'course_id' => $courseId,
            'month_id' => $monthId,
            'month_name' => $monthName,
            'four_exam_results' => $fourExamResults,
            'final_exam_result' => $finalExamResult,
        ];
    })->values()->toArray();

    return response()->json([
        'data' => $coursesResults,
    ]);
}

public function parentOrAdminShowAll5ExamResultsOfAllCourses($studentId)
{
    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeParentOrAdmin($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }
    $studentWithExams = User::with([
        'exams.course.month' => function ($query) {

    }])->findOrFail($studentId);

    $coursesResults = $studentWithExams->exams->
    groupBy('course_id')->map(function ($exams, $courseId) {


        $course = $exams->first()->course;
        $monthId = $course->month_id;
        $monthName = $course->month->name ?? 'غير معروف';

        $fourExamResults = collect([1, 2, 3, 4])->map(function ($testId) use ($exams) {
            $exam = $exams->firstWhere('test_id', $testId);
            return $exam ? [
                'exam_id' => $exam->id,
                'test_id' => $exam->test_id,
                'test_name' => optional($exam->test)->name,
                'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
                'has_attempted' => $exam->pivot->has_attempted ?? false,
            ] : null;
        // })->filter()->toArray();
    })->filter()->values()->toArray();


        $finalExam = $exams->firstWhere('test_id', 5);
        $finalExamResult = $finalExam ? [
            'exam_id' => $finalExam->id,
            'test_id' => $finalExam->test_id,
            'test_name' => optional($finalExam->test)->name,
            'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
            'has_attempted' => $finalExam->pivot->has_attempted ?? false,
        ] : null;

        return [
            'course_id' => $courseId,
            'month_id' => $monthId,
            'month_name' => $monthName,
            'four_exam_results' => $fourExamResults,
            'final_exam_result' => $finalExamResult,
        ];
    })->values()->toArray();

    return response()->json([
        'student' => new StudentRegisterResource($student),
        'data' => $coursesResults,
    ]);
}

}
