<?php

namespace App\Http\Controllers\User;


use App\Models\Exam;
use App\Models\User;
use App\Models\Month;
use App\Models\Parnt;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Resources\StudentResultResource;
use App\Http\Resources\User\ExamByIdResource;
use App\Http\Resources\User\LessonByIdResource;
use App\Http\Resources\Auth\ParentRegisterResource;
use App\Http\Resources\Auth\StudentRegisterResource;
use App\Http\Resources\User\CourseWithExamsLessonsResource;
use App\Http\Resources\User\StudentShowHisCourseByIdResource;


class ShowByIdController extends Controller
{
    public function studentShowCourse($id)
    {
        $user = auth()->guard('api')->user();
        $admin = auth()->guard('admin')->user();
        if ($user && !$user->courses()
        ->where('course_id', $id)
        ->wherePivot('status', 'paid')
        ->exists()) {
            return response()->json([
                'error' => 'Unauthorized access to this course.'
            ]);
        }

        if (!$user && (!$admin || $admin->role_id != 1)) {
            return response()->json([
                'error' => 'Unauthorized access to this course.'
            ]);
        }
        $course = Course::with(['lessons.exam'])->findOrFail($id);
        return response()->json([
       'data' =>new StudentShowHisCourseByIdResource($course)
        ]);
    }


    public function showLessonById($lessonId)
{
    $user = auth()->guard('api')->user();
    $admin = auth()->guard('admin')->user();


    $lesson = Lesson::with('course')->findOrFail($lessonId);
    $courseId = $lesson->course->id;

    if ($user && !$user->courses()
        ->where('course_id', $courseId)
        ->wherePivot('status', 'paid')
        ->exists()) {
        return response()->json([
            'error' => 'Unauthorized access to this lesson.'
        ]);
    }


    if (!$user && (!$admin || $admin->role_id != 1)) {
        return response()->json([
            'error' => 'Unauthorized access to this lesson.'
        ]);
    }


    return response()->json([
        'data' => new LessonByIdResource($lesson)
    ]);
}

public function showExamById($examId)
{
    $user = auth()->guard('api')->user();
    $admin = auth()->guard('admin')->user();


    $exam = Exam::with('questions')
    ->findOrFail($examId);
    $courseId = $exam->course->id;

    if ($user && !$user->courses()
        ->where('course_id', $courseId)
        ->wherePivot('status', 'paid')
        ->exists()) {
        return response()->json([
            'error' => 'Unauthorized access to this exam.'
        ]);
    }


    if (!$user && (!$admin || $admin->role_id != 1)) {
        return response()->json([
            'error' => 'Unauthorized access to this exam.'
        ]);
    }

    $score = null;
    $hasAttempted = false;

    if ($user) {
        $pivotData = $exam->students()
            ->where('user_id', $user->id)
            ->first();

        if ($pivotData) {
            $score = $pivotData->pivot->score;
            $hasAttempted = true;
        }
    }


    return response()->json([
        'data' => new ExamByIdResource($exam),
        'score' => $score,
        'has_attempted' => $hasAttempted,
    ], 200, [], JSON_UNESCAPED_UNICODE);

}


    public function showExamResults($examId, $studentId)
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

    $answers = Answer::with('question.exam')
        ->where('exam_id', $examId)
        ->where('user_id', $studentId)
        ->get();

    if ($answers->isEmpty()) {
        return response()->json([
            'message' => 'لا توجد إجابات لهذا الامتحان.'
        ]);
    }

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

    $score = ($correctAnswers / $totalQuestions) * 100;

    $studentResource = new StudentRegisterResource($student);

    return response()->json([
        'exam' => $exam,
        'student' => $studentResource,
        'data' => $answersDetail,
        'score' => $score,
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

    $admin = auth()->guard('admin')->user();
    if ($admin && $admin->role_id == 1) {
        return true;
    }

    return false;
}


// public function getStudentExamResults($studentId, $courseId)
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
//     // $fourExamResults = collect([1, 2, 3, 4])->map(function ($testId) use ($exams) {
//     //     $exam = $exams->firstWhere('test_id', $testId);
//     return [
//         'exam_id' => $exam->id,
//         'title' => $exam->title,
//         'score' => $exam->pivot->has_attempted ? $exam->pivot->score
//         : 'absent',
//         'has_attempted' => $exam->pivot->has_attempted,
//     ];
// })->toArray();

// // $finalExam = $student->exams->last();
// $finalExam = $student->exams->firstWhere('test_id',5);
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
//     'data' => new StudentResultResource($student),
//     'four_exam_results' => $fourExamResults,
//     // 'total_percentage_for_four_exams' => round($totalPercentageForFourExams, 2),
//     'final_exam_result' => $finalExamResult,
//     'overall_total_percentage' => round($overallTotalPercentage, 2),
// ]);

// }

public function getStudentExamResults($studentId, $courseId)
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
    $studentsScores = $studentsScores->sortByDesc('total_score')->values();

    // إيجاد ترتيب الطالب الحالي
    $studentRank = $studentsScores->
    search(fn($score) => $score['user_id'] === $studentId) + 1;

    return response()->json([
        'data' => new StudentResultResource($student),
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







public function getStudent4ExamsResult($studentId, $courseId)
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

    $student = User::with(['grade', 'parent'])->findOrFail($studentId);


    $fourExams = $student->exams()
    ->where('course_id', $courseId)
    // ->take(4)
    ->whereIn('test_id', [1, 2, 3, 4])
    ->values()
    ->get();

    $fourExamResults = $fourExams->map(function ($exam) {
        $score = $exam->pivot->score;
        $hasAttempted = $exam->pivot->has_attempted;
        $started_at = $exam->pivot->started_at;
        $submitted_at = $exam->pivot->submitted_at;
        $time_taken = $exam->pivot->time_taken;
        $correctAnswers = $exam->pivot->correctAnswers;


        $resultScore = ($score === null && $hasAttempted == 0) ? 'absent' : ($hasAttempted ? $score : 'absent');

        return [
            'exam_id' => $exam->id,
            'title' => $exam->title,
            'score' => $resultScore,
            'has_attempted' => $hasAttempted,
            'started_at' => $started_at,
            'submitted_at' => $submitted_at,
            'time_taken' => $time_taken,
            'correctAnswers' => $correctAnswers,
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
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }


    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }

    $totalOverallScore = 0;
    $totalMaxScore = 0;

    $courses = $student->courses()->with('exams')->get();

    foreach ($courses as $course) {
        foreach ($course->exams as $exam) {

            $studentExam = $exam->students()
            ->where('user_id', $studentId)
            ->first();

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
    $authenticatedUser = auth()->guard('api')->user();
    $admin = auth()->guard('admin')->user();
    if ($authenticatedUser) {

        if (!$admin || $admin->role_id != 1) {
            return response()->json([
                'message' => "Unauthorized access. You are not allowed to view this data."
            ]);
        }
    }


    if ($authenticatedParent && $authenticatedParent->id != $id) {
        if (!$admin || $admin->role_id != 1) {
            return response()->json([
                'message' => "Unauthorized access. You can only view your own data."
            ]);
        }
    }

    $Parent = Parnt::with('users')->find($id);

    if (!$Parent) {
        return response()->json([
            'message' => "Parent not found."
        ]);
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

// =============================

// public function getStudentRankOverallResults($studentId)
// {
//     $student = User::findOrFail($studentId);

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

//     // جلب كل الكورسات في نفس المرحلة الدراسية
//     $allGradeCourses = Course::where('grade_id', $student->grade->id)
//         ->with('exams', 'month')
//         ->get();

//     // عدد الكورسات التي تم إنشاؤها لهذه المرحلة الدراسية
//     $totalGradeCoursesCount = $allGradeCourses->count();

//     // عدد الكورسات التي اشتراها الطالب
//     $purchasedCoursesCount = $student->courses()->where('grade_id', $student->grade->id)->count();

//     $coursesScores = [];
//     $totalOverallScore = 0;
//     $totalMaxScore = 0;
//     $totalCoursesCount = 0;

//     // هذه المتغيرات لحساب عدد الامتحانات الكلي وعدد الامتحانات التي امتحن فيها الطالب
//     $totalExamsCount = 0;  // عدد الامتحانات في كل الكورسات
//     $attendedExamsCount = 0; // عدد الامتحانات التي امتحن فيها الطالب

//     // التكرار عبر الكورسات
//     foreach ($allGradeCourses as $course) {
//         $courseTotalScore = 0;
//         $courseExamsCount = $course->exams->count(); // عدد الامتحانات في الكورس الحالي
//         $courseAttendedExamsCount = 0; // عدد الامتحانات التي امتحن فيها الطالب في هذا الكورس

//         // التكرار عبر الامتحانات
//         foreach ($course->exams as $exam) {
//             $studentExam = $exam->students()
//                 ->where('user_id', $studentId)
//                 ->first();

//             if ($studentExam && !is_null($studentExam->pivot->score)) {
//                 $courseTotalScore += $studentExam->pivot->score;
//                 $courseAttendedExamsCount++;
//             }
//         }

//         // حساب النسبة المئوية للدرجة بناءً على 5 امتحانات
//         $courseScorePercentage = ($courseExamsCount > 0) ? ($courseTotalScore / (5 * 100)) * 100 : 0;

//         // تتبع الدرجات عبر الكورسات
//         $coursesScores[] = [
//             'nameOfCourse' => $course->nameOfCourse,
//             'month' => [
//                 'id' => $course->month->id,
//                 'name' => $course->month->name,
//             ],
//             'score_percentage' => round($courseScorePercentage, 2),
//             'attended_exams_count' => $courseAttendedExamsCount,
//             'total_exams_count' => 5, // القيمة ثابتة
//         ];

//         // جمع الدرجات الكلية للمجموع النهائي فقط للكورسات التي حضر فيها الطالب
//         if ($courseAttendedExamsCount > 0) {
//             $totalOverallScore += $courseTotalScore;
//             $totalMaxScore += (5 * 100); // 5 امتحانات × 100 درجة لكل امتحان
//             $totalCoursesCount++; // زيادة عدد الكورسات التي شارك فيها الطالب
//         }

//         // حساب إجمالي عدد الامتحانات في كل الكورسات التي اشتراها الطالب
//         $totalExamsCount += $courseExamsCount; // جمع عدد الامتحانات في هذا الكورس
//         $attendedExamsCount += $courseAttendedExamsCount; // عدد الامتحانات التي امتحن فيها الطالب
//     }

//     // حساب النسبة المئوية الإجمالية لجميع الكورسات التي شارك فيها الطالب فقط
//     $overallScorePercentage = 0;
//     if ($totalCoursesCount > 0) {
//         $overallScorePercentage = ($totalOverallScore / $totalMaxScore) * 100;
//     }

//     // في حالة إذا كانت هناك كورسات لم يشارك فيها الطالب (مثل الكورس الثاني الذي لم يشارك فيه)
//     if ($totalCoursesCount == 0) {
//         // الطالب لم يشارك في أي كورس، التقييم النهائي يكون صفر
//         $overallScorePercentage = 0;
//     }

//     return response()->json([
//         'student' => [
//             'id' => $student->id,
//             'name' => $student->name,
//             'email' => $student->email,
//             'img' => $student->img,
//             'grade' => [
//                 'id' => $student->grade->id,
//                 'grade' => $student->grade->grade,
//             ],
//         ],
//         'total_courses_count' => $totalGradeCoursesCount, // عدد الكورسات في نفس المرحلة
//         'purchased_courses_count' => $purchasedCoursesCount, // عدد الكورسات التي اشتراها الطالب
//         'total_exams_count' => $totalExamsCount, // عدد الامتحانات الكلي في كل الكورسات التي اشتراها الطالب
//         'attended_exams_count' => $attendedExamsCount, // عدد الامتحانات التي امتحن فيها الطالب
//         'overall_score_percentage' => round($overallScorePercentage, 2), // النسبة المئوية الإجمالية
//         'courses_scores' => $coursesScores,
//     ]);
// }

public function getStudentRankOverallResults($studentId)
{
    $student = User::findOrFail($studentId);

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

    $allExams = $student
    ->courses()
    ->with('exams')
    ->get()
    ->flatMap(function ($course) {
        return $course->exams;
    });


    $lastExamDetails = null;
    if ($allExams->isNotEmpty()) {

        $lastExam = $allExams->
        sortByDesc('created_at')
        ->first();

        $studentExam = $lastExam->
        students()->where('user_id', $student->id)->first();
        $score = $studentExam ? $studentExam->pivot->score : null;

        $lastExamDetails = [
            'exam_id' => $lastExam->id,
            'title' => $lastExam->title,
            'test_id' => $lastExam->test_id,
            'test_name' => $lastExam->test->name,
            'course_id' => $lastExam->course_id,
            'course_name' => $lastExam->course->nameOfCourse,
            'month_id' => $lastExam->course->month->id,
            'month_name' => $lastExam->course->month->name,
            'grade_id' => $lastExam->course->grade->id,
            'grade_name' => $lastExam->course->grade->grade,
            'score' => $score,
        ];
    }


    $allGradeCourses = Course::where('grade_id', $student->grade->id)
        ->with('exams', 'month')
        ->get();

    $totalGradeCoursesCount = $allGradeCourses->count();
    $purchasedCoursesCount = $student->courses()
    ->where('grade_id', $student->grade->id)
    ->count();

    $coursesScores = [];
    $totalOverallScore = 0;
    $totalMaxScore = 0;
    $totalCoursesCount = 0;
    $totalExamsCount = 0;
    $attendedExamsCount = 0;


    foreach ($allGradeCourses as $course) {
        $courseTotalScore = 0;
        $courseExamsCount = $course->exams->count();
        $courseAttendedExamsCount = 0;


        foreach ($course->exams as $exam) {
            $studentExam = $exam->students()
            ->where('user_id', $studentId)
            ->first();

            if ($studentExam && !is_null($studentExam->pivot->score)) {
                $courseTotalScore += $studentExam->pivot->score;
                $courseAttendedExamsCount++;
            }
        }

        $courseScorePercentage = ($courseExamsCount > 0) ? ($courseTotalScore / (5 * 100)) * 100 : 0;

        $coursesScores[] = [
            'nameOfCourse' => $course->nameOfCourse,
            'month' => [
                'id' => $course->month->id,
                'name' => $course->month->name,
            ],
            'score_percentage' => round($courseScorePercentage, 2),
            'attended_exams_count' => $courseAttendedExamsCount,
            'total_exams_count' => 5,
        ];

        if ($courseAttendedExamsCount > 0) {
            $totalOverallScore += $courseTotalScore;
            $totalMaxScore += (5 * 100);
            $totalCoursesCount++;
        }

        $totalExamsCount += $courseExamsCount;
        $attendedExamsCount += $courseAttendedExamsCount;
    }

    $overallScorePercentage = $totalCoursesCount > 0 ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

    if ($totalCoursesCount == 0) {
        $overallScorePercentage = 0;
    }

    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'img' => $student->img,
            'grade' => [
                'id' => $student->grade->id,
                'grade' => $student->grade->grade,
            ],
        ],
        'last_exam_details' => $lastExamDetails ?? [],
        'total_courses_count' => $totalGradeCoursesCount,
        'purchased_courses_count' => $purchasedCoursesCount,
        'total_exams_count' => $totalExamsCount,
        'attended_exams_count' => $attendedExamsCount,
        'overall_score_percentage' => round($overallScorePercentage, 2),
        'courses_scores' => $coursesScores,
    ]);
}












public function getRankAndOverAllResultsForAllStudents($courseId, $gradeId)
{
    $students = User::where('grade_id', $gradeId)
                    ->whereHas('courses', function ($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->get();

    $studentResults = [];

    foreach ($students as $student) {
        $totalOverallScore = 0;
        $totalMaxScore = 500;


        $course = $student->courses()
        ->where('course_id', $courseId)
        ->with('exams')
        ->first();

        if ($course) {
            foreach ($course->exams as $exam) {
                $studentExam = $exam->students()
                ->where('user_id', $student->id)
                ->first();

                if ($studentExam && !is_null($studentExam->pivot->score)) {
                    $totalOverallScore += $studentExam->pivot->score; // جمع الدرجات التي حصل عليها الطالب فقط
                }
            }

            $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

            $studentResults[] = [
                'id' => $student->id,
                'name' => $student->name,
                'img' => $student->img,
                'grade' => new GradeResource($student->grade),
                'overall_score_percentage' => round($overallScorePercentage, 2),
            ];
        }
    }

    // ترتيب الطلاب بناءً على النسبة المئوية للتقييم الإجمالي من الأعلى إلى الأدنى
    usort($studentResults, function ($a, $b) {
        return $b['overall_score_percentage'] <=> $a['overall_score_percentage'];
    });

    // إضافة ترتيب لكل طالب بناءً على ترتيبه في النتيجة النهائية
    foreach ($studentResults as $index => $studentResult) {
        $studentResults[$index]['rank'] = $index + 1;
    }

    return response()->json([
        'students' => $studentResults,
    ]);
}

public function getRankAndOverAllResultsForTopThreeStudents($courseId, $gradeId)
{

    $students = User::where('grade_id', $gradeId)
                    ->whereHas('courses', function ($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->get();

    $studentResults = [];

    foreach ($students as $student) {
        $totalOverallScore = 0;
        $totalMaxScore = 500;

        $course = $student->courses()
        ->where('course_id', $courseId)
        ->with('exams')
        ->first();

        if ($course) {
            foreach ($course->exams as $exam) {
                $studentExam = $exam->students()
                ->where('user_id', $student->id)
                ->first();

                if ($studentExam && !is_null($studentExam->pivot->score)) {
                    $totalOverallScore += $studentExam->pivot->score; // جمع الدرجات التي حصل عليها الطالب فقط
                }
            }

            
            $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

            $studentResults[] = [
                'id' => $student->id,
                'name' => $student->name,
                'img' => $student->img,
                'grade' => new GradeResource($student->grade),
                'overall_score_percentage' => round($overallScorePercentage, 2),
            ];
        }
    }

    // ترتيب الطلاب بناءً على النسبة المئوية للتقييم الإجمالي من الأعلى إلى الأدنى
    usort($studentResults, function ($a, $b) {
        return $b['overall_score_percentage'] <=> $a['overall_score_percentage'];
    });

    // إضافة ترتيب لكل طالب بناءً على ترتيبه في النتيجة النهائية
    foreach ($studentResults as $index => $studentResult) {
        $studentResults[$index]['rank'] = $index + 1;
    }

    // إعادة الثلاثة الأوائل فقط
    $topThreeStudents = array_slice($studentResults, 0, 3);

    return response()->json([
        'students' => $topThreeStudents,
    ]);
}

protected function authorizeStudentOrAdmin($student)
{
    $user = auth()->guard('api')->user();

    if ($user && $user->id === $student->id) {
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

    if ($parent && $parent->id === $student->parnt_id) {
        return true;
    }

    $admin = auth()->guard('admin')->user();
    if ($admin && $admin->role_id == 1) {
        return true;
    }
    return false;
}


public function getLessonPdf($studentId)
{
    $student = User::findOrFail($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ]);
    }

    if (!$this->authorizeStudentOrAdmin($student)) {
        return response()->json([
            'message' => 'Unauthorized access.'
        ]);
    }
    $hasPurchased = $student->courses()->exists();
    if (!$hasPurchased) {
        return response()->json([
            'error' => 'Unauthorized access: Course not purchased'
        ]);
    }

    // $courses = $student->courses()->with(['month', 'lessons'])->get();
    $courses = $student->courses()
    ->with(['month', 'lessons' => function ($query) use ($student) {
        $query->where('grade_id', $student->grade_id);
    }])->get();


    $coursesData = $courses->map(function ($course) {
        return [
            'course_id' => $course->id,
            'month' => [
                'id' => $course->month->id,
                'name' => $course->month->name,
            ],
            'lessons' => $course->lessons->map(function ($lesson) {
                return [
                    'lec_id' => $lesson->lec_id,
                    'title' => $lesson->title,
                    'numOfPdf' => $lesson->numOfPdf,
                    'ExplainPdf' => $lesson->ExplainPdf,
                ];
            })->values(),
        ];
    });

    return response()->json([
        'data' => $coursesData,
        'message' => 'PDFs retrieved successfully.'
    ]);
}



public function showCourse(string $id)
{
    $course = Course::with(['lessons.exam'])->findOrFail($id);
      return response()->json([
     'data' =>new CourseWithExamsLessonsResource($course)
      ]);
}


    public function studentEditProfile(string $id)
    {
        $Student = User::find($id);

        if (!$Student) {
            return response()->json([
                'message' => "Student not found."
            ]);
        }

        if (!$this->authorizeStudentOrAdmin($Student)) {
            return response()->json([
                'message' => 'Unauthorized access.'
            ]);
        }

        return response()->json([
            'data' => new StudentRegisterResource($Student),
            'message' => "Edit Student By ID Successfully."
        ]);
    }

    public function parentEditProfile(string $id)
    {
        $authParent = auth()->guard('parnt')->user();
        if ($authParent->id != $id) {
           return response()->json([
               'message' => "Unauthorized to edit this profile."
           ]);
       }
        $Parent = Parnt::find($id);

        if (!$Parent) {
            return response()->json([
                'message' => "Parent not found."
            ]);
        }

        return response()->json([
            'data' => new ParentRegisterResource($Parent),
            'message' => "Edit Parent By ID Successfully."
        ]);
    }

}
