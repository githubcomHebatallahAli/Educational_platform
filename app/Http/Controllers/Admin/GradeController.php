<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GradeRequest;
use App\Http\Resources\Admin\GradeResource;


class GradeController extends Controller
{
    public function showAll()
    {
        $this->authorize('manage_users');

        $Grades = Grade::get();
        return response()->json([
            'data' => GradeResource::collection($Grades),
            'message' => "Show All Grades Successfully."
        ]);
    }


    public function create(GradeRequest $request)
    {
        $this->authorize('manage_users');

           $Grade =Grade::create ([

                "grade" => $request->grade
            ]);
           $Grade->save();
           return response()->json([
            'data' =>new GradeResource($Grade),
            'message' => "Grade Created Successfully."
        ]);

        }


    public function edit(string $id)
    {
        $this->authorize('manage_users');
        // $Grade = Grade::find($id);
        $Grade = Grade::with('Courses')->findOrFail($id);

        if (!$Grade) {
            return response()->json([
                'message' => "Grade not found."
            ], 404);
        }

        return response()->json([
            'data' =>new GradeResource($Grade),
            'message' => "Edit Grade By ID Successfully."
        ]);
    }



    public function update(GradeRequest $request, string $id)
    {
        $this->authorize('manage_users');
       $Grade =Grade::findOrFail($id);

       if (!$Grade) {
        return response()->json([
            'message' => "Grade not found."
        ], 404);
    }
       $Grade->update([
        "grade" => $request->grade
        ]);

       $Grade->save();
       return response()->json([
        'data' =>new GradeResource($Grade),
        'message' => " Update Grade By Id Successfully."
    ]);
}

public function destroy(string $id){
    $this->authorize('manage_users');
    $Grade =Grade::find($id);
    if (!$Grade) {
     return response()->json([
         'message' => "Grade not found."
     ], 404);
 }
 $this->authorize('delete', $Grade);
    $Grade->delete($id);
    return response()->json([
        'data' =>new GradeResource($Grade),
        'message' => " Soft Delete Grade By Id Successfully."
    ]);
}

    public function showDeleted(){
        $this->authorize('manage_users');
    $Grades=Grade::onlyTrashed()->get();
    return response()->json([
        'data' =>GradeResource::collection($Grades),
        'message' => "Show Deleted Grades Successfully."
    ]);
}

public function restore(string $id)
{
       $this->authorize('manage_users');
    $Grade = Grade::withTrashed()->where('id', $id)->first();
    if (!$Grade) {
        return response()->json([
            'message' => "Grade not found."
        ], 404);
    }
    $this->authorize('restore', $Grade);
    $Grade->restore();
    return response()->json([
        'data' =>new GradeResource($Grade),
        'message' => "Restore Grade By Id Successfully."
    ]);
}

public function forceDelete(string $id){
    $this->authorize('manage_users');
    $Grade=Grade::withTrashed()->where('id',$id)->first();
    if (!$Grade) {
        return response()->json([
            'message' => "Grade not found."
        ], 404);
    }
    $this->authorize('forceDelete', $Grade);

    $Grade->forceDelete();
    return response()->json([
        'message' => " Force Delete Grade By Id Successfully."
    ]);
}
}
