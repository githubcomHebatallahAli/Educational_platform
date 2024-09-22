<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Http\Resources\Admin\StudentResource;


class StudentController extends Controller
{
    public function showAll()
    {
        $this->authorize('manage_users');

        $students = Student::with(['user'])->get();
        return response()->json([
            'data' => StudentResource::collection($students),
            'message' => "Show All Students Successfully."
        ]);
    }


    public function create(StudentRequest $request)
    {
        $this->authorize('manage_users');

           $student =student::create ([
                'user_id' => $request->user_id,
                'isPay' => $request->input('isPay') ?? 'notPay',
            ]);
           $student->save();
           return response()->json([
            'data' =>new StudentResource($student),
            'message' => "Student Created Successfully."
        ]);

        }


    public function edit(string $id)
    {
        $this->authorize('manage_users');
        $student = Student::with('user')->find($id);

        if (!$student) {
            return response()->json([
                'message' => "student not found."
            ], 404);
        }

        return response()->json([
            'data' =>new StudentResource($student),
            'message' => "Edit Student By ID Successfully."
        ]);
    }



    public function update(StudentRequest $request, string $id)
    {
        $this->authorize('manage_users');
       $student =Student::findOrFail($id);

       if (!$student) {
        return response()->json([
            'message' => "student not found."
        ], 404);
    }
       $student->update([
        'user_id' => $request->user_id,
        "isPay" => $request->input('isPay') ?? 'notPay',
        ]);

       $student->save();
       return response()->json([
        'data' =>new StudentResource($student),
        'message' => " Update Student By Id Successfully."
    ]);
}

public function updateIsPay(Request $request, string $id)
{
    $this->authorize('manage_users');

    $student = Student::findOrFail($id);
    $student->update([
        "isPay" => 'pay'
    ]);

    return response()->json([
        'data' => new StudentResource($student),
        'message' => "Updated Student 'isPay' Field Successfully."
    ]);
}


public function destroy(string $id){
    $this->authorize('manage_users');
    $student =Student::find($id);
    if (!$student) {
     return response()->json([
         'message' => "student not found."
     ], 404);
 }

    $student->delete($id);
    return response()->json([
        'data' =>new StudentResource($student),
        'message' => " Soft Delete Student By Id Successfully."
    ]);
}

    public function showDeleted(){
        $this->authorize('manage_users');
    $students=Student::onlyTrashed()->get();
    return response()->json([
        'data' =>StudentResource::collection($students),
        'message' => "Show Deleted Students Successfully."
    ]);
}

public function restore(string $id)
{
    $this->authorize('manage_users');
    $student = Student::withTrashed()->where('id', $id)->first();
    if (!$student) {
        return response()->json([
            'message' => "Student not found."
        ], 404);
    }

    $student->restore();
    return response()->json([
        'message' => "Restore Student By Id Successfully."
    ]);
}

public function forceDelete(string $id){
    $this->authorize('manage_users');
    $student=Student::withTrashed()->where('id',$id)->first();
    if (!$student) {
        return response()->json([
            'message' => "student not found."
        ], 404);
    }

    $student->forceDelete();
    return response()->json([
        'message' => "Force Delete Student By Id Successfully."
    ]);
}

}
