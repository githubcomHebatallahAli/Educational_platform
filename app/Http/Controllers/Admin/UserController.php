<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
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

    public function update(UpdateStudentRequest $request, string $id)
    {
        $this->authorize('manage_users');
        $Student = User::findOrFail($id);

        if ($request->filled('name')) {
            $Student->name = $request->name;
        }

        if ($request->filled('email')) {
            $Student->email = $request->email;
        }
        if ($request->filled('parentPhoNum')) {
            $Student->parentPhoNum = $request->parentPhoNum;
        }
        if ($request->filled('studentPhoNum')) {
            $Student->studentPhoNum = $request->studentPhoNum;
        }
        if ($request->filled('governorate')) {
            $Student->governorate = $request->governorate;
        }
        if ($request->filled('grade_id')) {
            $Student->grade_id = $request->grade_id;
        }

        $Student->parnt_id = $request->parnt_id;
        $Student->parent_code = $request->parent_code;

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
}

