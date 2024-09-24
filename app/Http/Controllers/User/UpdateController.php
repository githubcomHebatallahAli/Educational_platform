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
    public function updateProfilePicture(ImgRequest $request)
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

}


