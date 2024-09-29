<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Parnt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ImgRequest;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\UpdateParentRequest;
use App\Http\Requests\Auth\UpdateStudentRequest;
use App\Http\Resources\Auth\ParentRegisterResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class UpdateController extends Controller
{
    public function studentUpdateProfilePicture(ImgRequest $request , string $id)
{
    $Student= auth()->guard('api')->user();
    if ($Student->id != $id) {
        return response()->json([
            'message' => "Unauthorized to update this profile."
        ], 403);
    }
    if ($request->hasFile('img')) {
        if ($Student->img) {
            Storage::disk('public')->delete($Student->img);
        }
        $imgPath = $request->file('img')->store('Student', 'public');
        $Student->img = $imgPath;

    }
    $Student->save();
        return response()->json([
         'data' => new StudentRegisterResource($Student),
            'message' => 'Profile picture updated successfully'
        ]);
    }


    public function updateCode(Request $request, string $id)
{
    $Student= auth()->guard('api')->user();
    if ($Student->id != $id) {
        return response()->json([
            'message' => "Unauthorized to update this profile."
        ], 403);
    }
    $Student = User::findOrFail($id);

    $Student->update([
        "parent_code" => $request->parent_code
        ]);

        $Student->save();

        return response()->json([
            'data' => new StudentRegisterResource($Student),
            'message' => "Parent code updated successfully."
        ]);

}

public function studentUpdateProfile(UpdateStudentRequest $request, string $id)
{
    $Student= auth()->guard('api')->user();
    if ($Student->id != $id) {
        return response()->json([
            'message' => "Unauthorized to update this profile."
        ], 403);
    }
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

    $Student->save();

    return response()->json([
        'data' => new StudentRegisterResource($Student),
        'message' => "Update Student By Id Successfully."
    ]);
}




// Parent

    public function parentUpdateProfilePicture(ImgRequest $request , string $id)
{

    $Parent = auth()->guard('parnt')->user();
    if ($Parent->id != $id) {
       return response()->json([
           'message' => "Unauthorized to update this profile."
       ], 403);
   }
    if ($request->hasFile('img')) {
        if ($Parent->img) {
            Storage::disk('public')->delete($Parent->img);
        }
        $imgPath = $request->file('img')->store('Parent', 'public');
        $Parent->img = $imgPath;

    }
    $Parent->save();
        return response()->json([
            'data' => new ParentRegisterResource($Parent),
            'message' => 'Profile picture updated successfully'
        ]);
    }

    public function parentUpdateProfile(UpdateParentRequest $request, string $id)
    {
        $authParent = auth()->guard('parnt')->user();
         if ($authParent->id != $id) {
            return response()->json([
                'message' => "Unauthorized to update this profile."
            ], 403);
        }
        $Parent = Parnt::findOrFail($id);

        if ($request->filled('name')) {
            $Parent->name = $request->name;
        }

        if ($request->filled('email')) {
            $Parent->email = $request->email;
        }
        if ($request->filled('parentPhoNum')) {
            $Parent->parentPhoNum = $request->parentPhoNum;
        }

        $Parent->save();

        return response()->json([
            'data' => new ParentRegisterResource($Parent),
            'message' => "Update Parent By Id Successfully."
        ]);
    }

}


