<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Requests\Auth\UpdateStudentRequest;
use App\Http\Resources\Auth\StudentRegisterResource;

class UserController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
    {
        $this->authorize('manage_users');

        $Students = User::get();
        return response()->json([
            'data' => StudentRegisterResource::collection($Students),
            'message' => "Show All Students Successfully."
        ]);
    }

    public function edit(string $id)
    {
        $this->authorize('manage_users');
        $Student = User::find($id);

        if (!$Student) {
            return response()->json([
                'message' => "Student not found."
            ], 404);
        }

        return response()->json([
            'data' => new StudentRegisterResource($Student),
            'message' => "Edit Student By ID Successfully."
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorize('manage_users');
        $Student = User::findOrFail($id);

        if ($request->filled('parent_code')) {
            $Student->parent_code = $request->parent_code;
        }

        $Student->parnt_id = $request->parnt_id;


        $Student->save();

        return response()->json([
            'data' => new StudentRegisterResource($Student),
            'message' => "Update Student By Id Successfully."
        ]);
    }

    public function destroy(string $id)
    {
        return $this->destroyModel(User::class, StudentRegisterResource::class, $id);
    }

    public function showDeleted(){
        $this->authorize('manage_users');
    $Users=User::onlyTrashed()->get();
    return response()->json([
        'data' =>StudentRegisterResource::collection($Users),
        'message' => "Show Deleted Students Successfully."
    ]);
    }

    public function restore(string $id)
    {
    $this->authorize('manage_users');
    $User = User::withTrashed()->where('id', $id)->first();
    if (!$User) {
        return response()->json([
            'message' => "Student not found."
        ], 404);
    }

    $User->restore();
    return response()->json([
        'data' =>new StudentRegisterResource($User),
        'message' => "Restore Student By Id Successfully."
    ]);
    }
    public function forceDelete(string $id)
    {
        return $this->forceDeleteModel(User::class, $id);
    }


    public function getStudentOverallResults($studentId)
    {
        $this->authorize('manage_users');

        $student = User::findOrFail($studentId);

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

}

