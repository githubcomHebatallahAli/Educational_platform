<?php

namespace App\Http\Controllers\Admin;

use App\Models\Parnt;
use Illuminate\Http\Request;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateParentRequest;
use App\Http\Resources\Auth\ParentRegisterResource;
use App\Http\Resources\Admin\ParentWithSonsResource;

class ParentController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
    {
        $this->authorize('manage_users');

        $Parents = Parnt::get();
        return response()->json([
            'data' => ParentRegisterResource::collection($Parents),
            'message' => "Show All Parents Successfully."
        ]);
    }

    public function edit(string $id)
    {
        $this->authorize('manage_users');
        $Parent = Parnt::with('users')->find($id);

        if (!$Parent) {
            return response()->json([
                'message' => "Parent not found."
            ], 404);
        }

        return response()->json([
            'data' => new ParentWithSonsResource($Parent),
            'message' => "Edit Parent By ID Successfully."
        ]);
    }

    // public function update(UpdateParentRequest $request, string $id)
    // {
    //     $this->authorize('manage_users');
    //     $Parent = Parnt::findOrFail($id);

    //     if ($request->filled('name')) {
    //         $Parent->name = $request->name;
    //     }

    //     if ($request->filled('email')) {
    //         $Parent->email = $request->email;
    //     }
    //     if ($request->filled('parentPhoNum')) {
    //         $Parent->parentPhoNum = $request->parentPhoNum;
    //     }

    //     $Parent->save();

    //     return response()->json([
    //         'data' => new ParentRegisterResource($Parent),
    //         'message' => "Update Parent By Id Successfully."
    //     ]);
    // }

    public function destroy(string $id)
    {
        return $this->destroyModel(Parnt::class, ParentRegisterResource::class, $id);
    }

    public function showDeleted(){
        $this->authorize('manage_users');
    $Users=Parnt::onlyTrashed()->get();
    return response()->json([
        'data' =>ParentRegisterResource::collection($Users),
        'message' => "Show Deleted Parents Successfully."
    ]);
    }

    public function restore(string $id)
    {
    $this->authorize('manage_users');
    $User = Parnt::withTrashed()->where('id', $id)->first();
    if (!$User) {
        return response()->json([
            'message' => "Parent not found."
        ], 404);
    }

    $User->restore();
    return response()->json([
        'data' =>new ParentRegisterResource($User),
        'message' => "Restore Parent By Id Successfully."
    ]);
    }

    public function forceDelete(string $id)
    {
        return $this->forceDeleteModel(Parnt::class, $id);
    }
}
