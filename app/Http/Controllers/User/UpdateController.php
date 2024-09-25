<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ImgRequest;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\Auth\StudentRegisterResource;

class UpdateController extends Controller
{
    public function studentUpdateProfilePicture(ImgRequest $request)
{
    $Student= auth()->guard('api')->user();
    if ($request->hasFile('img')) {
        if ($Student->img) {
            Storage::disk('public')->delete($Student->img);
        }
        $imgPath = $request->file('img')->store('Student', 'public');
        $Student->img = $imgPath;

    }
    $Student->save();
        return response()->json([
            'message' => 'Profile picture updated successfully'
        ]);
    }


    public function updateCode(Request $request, string $id)
{
    $Student= auth()->guard('api')->user();
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

// public function adminUpdateProfilePicture(ImgRequest $request)
// {
//     $Admin= auth()->guard('admin')->user();
//     if ($request->hasFile('img')) {
//         if ($Admin->img) {
//             Storage::disk('public')->delete($Admin->img);
//         }
//         $imgPath = $request->file('img')->store('Admin', 'public');
//         $Admin->img = $imgPath;

//     }
//     $Admin->save();
//         return response()->json([
//             'message' => 'Profile picture updated successfully'
//         ]);
//     }


    public function parentUpdateProfilePicture(ImgRequest $request)
{
    $Parent= auth()->guard('parnt')->user();
    if ($request->hasFile('img')) {
        if ($Parent->img) {
            Storage::disk('public')->delete($Parent->img);
        }
        $imgPath = $request->file('img')->store('Parent', 'public');
        $Parent->img = $imgPath;

    }
    $Parent->save();
        return response()->json([
            'message' => 'Profile picture updated successfully'
        ]);
    }

}


