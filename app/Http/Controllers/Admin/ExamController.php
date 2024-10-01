<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\User;
use App\Models\Parnt;
use App\Models\Answer;
use App\Models\Student;
use App\Models\Question;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExamRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\AnswerResource;
use App\Http\Resources\Admin\StudentResource;
use App\Http\Resources\StudentResultResource;
use App\Http\Requests\Admin\StudentExamRequest;
use App\Http\Resources\Admin\LessonCourseResource;
use App\Http\Resources\Admin\ExamQuestionsResource;
use App\Http\Resources\Auth\ParentRegisterResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class ExamController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Exams = Exam::with(['students','questions'])->get();
      return response()->json([
          'data' => ExamQuestionsResource::collection($Exams),
          'message' => "Show All Exams Successfully."
      ]);
  }


  public function create(ExamRequest $request)
  {
      $this->authorize('manage_users');
         $Exam =Exam::create ([
            "title" => $request-> title,
            "grade_id" => $request-> grade_id,
            "course_id" => $request-> course_id,
            "test_id" => $request-> test_id,
            "lesson_id" => $request-> lesson_id,
            "totalMarke" => $request-> totalMarke,
            "duration" => $request-> duration,
            "examNumber" => $request-> examNumber,
            "numOfQ" => $request-> numOfQ,
            "deadLineExam" => $request-> deadLineExam
          ]);

         $Exam->save();
         return response()->json([
          'data' =>new ExamResource($Exam),
          'message' => "Exam Created Successfully."
      ]);

      }

      public function assignStudentsToExam(StudentExamRequest $request)
      {
          $this->authorize('manage_users');

          $Exam = Exam::with('students')->find($request->exam_id);
          if (!$Exam) {
              return response()->json([
                  'message' => 'Exam not found'
              ], 404);
          }
          $Exam->students()->sync($request->student_ids);

          return response()->json([
              'data' => new ExamResource($Exam),
              "message" => "Students added to Exam successfully"
          ]);
      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Exam = Exam::with(['students','questions'])->find($id);

      if (!$Exam) {
          return response()->json([
              'message' => "Exam not found."
          ], 404);
      }

      return response()->json([
          'data' =>new ExamResource($Exam),
          'message' => "Edit Exam By ID Successfully."
      ]);
  }


  public function showExamQuestions($examId)
  {
    $this->authorize('manage_users');
    $exam = Exam::with('questions')->findOrFail($examId);
    return response()->json([
        'data' =>new ExamQuestionsResource($exam),
        'message' => "Show Exam With Questions By Id Successfully."
    ]);

  }


public function showExamResults($examId, $studentId)
{
    $this->authorize('manage_users');

    $student = User::find($studentId);
    if (!$student) {
        return response()->json([
            'message' => 'الطالب غير موجود.'
        ], 404);
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


  public function update(ExamRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Exam =Exam::findOrFail($id);


     if (!$Exam) {
      return response()->json([
          'message' => "Exam not found."
      ], 404);
  }

     $Exam->update([
        "title" => $request-> title,
        "grade_id" => $request-> grade_id,
        "course_id" => $request-> course_id,
        "test_id" => $request-> test_id,
        "lesson_id" => $request-> lesson_id,
        "totalMarke" => $request-> totalMarke,
        "duration" => $request-> duration,
        "examNumber" => $request-> examNumber,
        "numOfQ" => $request-> numOfQ,
        "deadLineExam" => $request-> deadLineExam
      ]);

     $Exam->save();
     return response()->json([
      'data' =>new ExamResource($Exam),
      'message' => " Update Exam By Id Successfully."
  ]);

}

  public function destroy(string $id)
  {
      return $this->destroyModel(Exam::class, ExamResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$exams=Exam::onlyTrashed()->get();
return response()->json([
    'data' =>ExamResource::collection($exams),
    'message' => "Show Deleted Exams Successfully."
]);
}

public function restore(string $id)
{
$this->authorize('manage_users');
$Exam = Exam::withTrashed()->where('id', $id)->first();
if (!$Exam) {
    return response()->json([
        'message' => "Exam not found."
    ], 404);
}

$Exam->restore();
return response()->json([
    'data' =>new ExamResource($Exam),
    'message' => "Restore Exam By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Exam::class, $id);
  }

  public function getStudentExamResults($studentId, $courseId)
{
    $this->authorize('manage_users');
    $student = User::find($studentId);

    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود.'], 404);
    }


$student = User::with(['exams' => function ($query) use ($courseId) {
    $query->where('course_id', $courseId);
}])->findOrFail($studentId);

$fourExams = $student->exams->take(4);

$fourExamResults = $fourExams->map(function ($exam) {
    return [
        'exam_id' => $exam->id,
        'title' => $exam->title,
        'score' => $exam->pivot->has_attempted ? $exam->pivot->score : 'absent',
        'has_attempted' => $exam->pivot->has_attempted,
    ];
})->toArray();

$finalExam = $student->exams->last();
$finalExamResult = [
    'exam_id' => $finalExam->id,
    'title' => $finalExam->title,
    'score' => $finalExam->pivot->has_attempted ? $finalExam->pivot->score : 'absent',
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


$totalPercentageForFourExams = $attemptedCount > 0 ? ($totalScore / ($attemptedCount * 100)) * 100 : 0;


$overallTotalScore = $totalScore + ($finalExam->pivot->has_attempted ? $finalExam->pivot->score : 0);
$totalExamsCount = $attemptedCount + ($finalExam->pivot->has_attempted ? 1 : 0);
$overallTotalPercentage = $totalExamsCount > 0 ? ($overallTotalScore / ($totalExamsCount * 100)) * 100 : 0;

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
    $this->authorize('manage_users');
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


}
