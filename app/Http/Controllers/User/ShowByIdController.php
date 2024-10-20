<?php

namespace App\Http\Controllers\User;


use App\Models\User;
use App\Models\Parnt;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Lesson;
use App\Http\Controllers\Controller;

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
    $authenticatedUser = auth()->guard('api')->user();
    $admin = auth()->guard('admin')->user();
    if ($authenticatedUser) {

        if (!$admin || $admin->role_id != 1) {
            return response()->json([
                'message' => "Unauthorized access. You are not allowed to view this data."
            ], 403);
        }
    }


    if ($authenticatedParent && $authenticatedParent->id != $id) {
        if (!$admin || $admin->role_id != 1) {
            return response()->json([
                'message' => "Unauthorized access. You can only view your own data."
            ], 403);
        }
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

// public function getStudentRankOverallResults($studentId)
// {

//     $student = User::findOrFail($studentId);
//     if (!$student) {
//         return response()->json(['message' => 'الطالب غير موجود.'], 404);
//     }

//     if (!$this->authorizeStudentOrParent($student)) {
//         return response()->json(['message' => 'Unauthorized access.'], 403);
//     }

//     $totalOverallScore = 0;
//     $totalMaxScore = 0;

//     $courses = $student->courses()->with('exams')->get();

//     // حساب نتيجة الطالب
//     foreach ($courses as $course) {
//         foreach ($course->exams as $exam) {

//             $studentExam = $exam->students()->where('user_id', $studentId)->first();

//             if ($studentExam && !is_null($studentExam->pivot->score)) {
//                 $totalOverallScore += $studentExam->pivot->score;
//             }
//             $totalMaxScore += 100;
//         }
//     }

//     // حساب النسبة المئوية لإجمالي درجات الطالب
//     $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

//     // حساب ترتيب الطالب وسط زملائه في نفس الكورسات
//     $peerScores = []; // لتخزين درجات الزملاء

//     foreach ($courses as $course) {
//         foreach ($course->exams as $exam) {
//             $examStudents = $exam->students()->get(); // جميع الطلاب الذين قاموا بهذا الامتحان

//             foreach ($examStudents as $peerStudent) {
//                 $peerTotalScore = 0;
//                 $peerMaxScore = 0;

//                 foreach ($peerStudent->courses()->with('exams')->get() as $peerCourse) {
//                     foreach ($peerCourse->exams as $peerExam) {
//                         $peerStudentExam = $peerExam->students()->where('user_id', $peerStudent->id)->first();
//                         if ($peerStudentExam && !is_null($peerStudentExam->pivot->score)) {
//                             $peerTotalScore += $peerStudentExam->pivot->score;
//                         }
//                         $peerMaxScore += 100;
//                     }
//                 }

//                 $peerOverallPercentage = ($peerMaxScore > 0) ? ($peerTotalScore / $peerMaxScore) * 100 : 0;

//                 $peerScores[$peerStudent->id] = $peerOverallPercentage;
//             }
//         }
//     }

//     // ترتيب الطلاب بناءً على النسبة المئوية
//     arsort($peerScores); // ترتيب تنازلي
//     $rank = array_search($studentId, array_keys($peerScores)) + 1; // ترتيب الطالب الحالي

//     return response()->json([
//         'student' => [
//             'id' => $student->id,
//             'name' => $student->name,
//             'email' => $student->email,
//             'img' => $student->img,
//             'grade' => new GradeResource($student->grade),
//         ],
//         'overall_score_percentage' => round($overallScorePercentage, 2),
//         'rank' => $rank, // ترتيب الطالب
//         'total_students' => count($peerScores), // إجمالي عدد الطلاب
//     ]);
// }

public function getStudentRankOverallResults($studentId)
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
    $coursesScores = [];

    $courses = $student->courses()->with('exams')->get();

    foreach ($courses as $course) {
        $courseTotalScore = 0;
        $attendedExamsCount = 0;

        foreach ($course->exams as $exam) {
            $studentExam = $exam->students()->where('user_id', $studentId)->first();

            if ($studentExam && !is_null($studentExam->pivot->score)) {
                $courseTotalScore += $studentExam->pivot->score;
                $attendedExamsCount++;
            }
        }

        $courseScorePercentage = ($attendedExamsCount > 0) ? ($courseTotalScore / ($attendedExamsCount * 100)) * 100 : 0;

        $coursesScores[] = [
            'nameOfCourse' => $course->nameOfCourse,
            'score_percentage' => round($courseScorePercentage, 2),
            'attended_exams_count' => $attendedExamsCount,
            'total_exams_count' => 5,
        ];

        $totalOverallScore += $courseTotalScore;
        $totalMaxScore += ($attendedExamsCount * 100);
    }

    $overallScorePercentage = ($totalMaxScore > 0) ? ($totalOverallScore / $totalMaxScore) * 100 : 0;

    $peerScores = [];
    foreach ($courses as $course) {
        foreach ($course->exams as $exam) {
            $examStudents = $exam->students()->get();
            foreach ($examStudents as $peerStudent) {
                $peerTotalScore = 0;
                $peerMaxScore = 0;
                foreach ($peerStudent->courses()->with('exams')->get() as $peerCourse) {
                    foreach ($peerCourse->exams as $peerExam) {
                        $peerStudentExam = $peerExam->students()->where('user_id', $peerStudent->id)->first();
                        if ($peerStudentExam && !is_null($peerStudentExam->pivot->score)) {
                            $peerTotalScore += $peerStudentExam->pivot->score;
                        }
                        $peerMaxScore += 100;
                    }
                }
                $peerOverallPercentage = ($peerMaxScore > 0) ? ($peerTotalScore / $peerMaxScore) * 100 : 0;
                $peerScores[$peerStudent->id] = $peerOverallPercentage;
            }
        }
    }
    arsort($peerScores);
    $rank = array_search($studentId, array_keys($peerScores)) + 1;

    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'img' => $student->img,
            'grade' => new GradeResource($student->grade),
        ],
        'overall_score_percentage' => round($overallScorePercentage, 2),
        'rank' => $rank,
        'total_students' => count($peerScores),
        'courses_scores' => $coursesScores,
    ]);
}

public function getRankAndOverAllResultsForAllStudents($courseId, $gradeId)
{
    // الحصول على جميع الطلاب المشاركين في نفس الكورس والذين لديهم grade_id محدد
    $students = User::where('grade_id', $gradeId)
                    ->whereHas('courses', function ($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->get();

    $studentResults = [];

    foreach ($students as $student) {
        $totalOverallScore = 0;
        $totalMaxScore = 500; // عدد الامتحانات الكلي هو 5 وكل امتحان درجته 100

        // الحصول على الكورس المحدد للطالب وحساب درجاته
        $course = $student->courses()->where('course_id', $courseId)->with('exams')->first();

        if ($course) {
            foreach ($course->exams as $exam) {
                $studentExam = $exam->students()->where('user_id', $student->id)->first();

                if ($studentExam && !is_null($studentExam->pivot->score)) {
                    $totalOverallScore += $studentExam->pivot->score; // جمع الدرجات التي حصل عليها الطالب فقط
                }
            }

            // حساب النسبة المئوية للتقييم الإجمالي لكل طالب
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
    // الحصول على جميع الطلاب المشاركين في نفس الكورس والذين لديهم grade_id محدد
    $students = User::where('grade_id', $gradeId)
                    ->whereHas('courses', function ($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->get();

    $studentResults = [];

    foreach ($students as $student) {
        $totalOverallScore = 0;
        $totalMaxScore = 500; // عدد الامتحانات الكلي هو 5 وكل امتحان درجته 100

        // الحصول على الكورس المحدد للطالب وحساب درجاته
        $course = $student->courses()->where('course_id', $courseId)->with('exams')->first();

        if ($course) {
            foreach ($course->exams as $exam) {
                $studentExam = $exam->students()->where('user_id', $student->id)->first();

                if ($studentExam && !is_null($studentExam->pivot->score)) {
                    $totalOverallScore += $studentExam->pivot->score; // جمع الدرجات التي حصل عليها الطالب فقط
                }
            }

            // حساب النسبة المئوية للتقييم الإجمالي لكل طالب
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

public function getLessonPdf($studentId)
{
    $student = User::findOrFail($studentId);
    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود.'], 404);
    }

    if (!$this->authorizeStudentOrParent($student)) {
        return response()->json(['message' => 'Unauthorized access.'], 403);
    }

    $hasPurchased = $student->courses()->exists(); // إذا كنت تريد التحقق من الدورات التي اشتراها الطالب

    if (!$hasPurchased) {
        return response()->json(['error' => 'Unauthorized access: Course not purchased'], 403);
    }


    $lessons = Lesson::where('grade_id', $student->grade_id)->get()->map(function ($lesson) {
        return [
            'course_id' => $lesson->course_id,
            'lec_id' => $lesson->lec_id,
            'title' => $lesson->title,
            'numOfPdf' => $lesson->numOfPdf,
            'ExplainPdf' => $lesson->ExplainPdf,
        ];
    });

    return response()->json([
        'data' => $lessons,
        'message' => 'PDFs retrieved successfully.'
    ]);
}


}
