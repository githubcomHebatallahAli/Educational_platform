<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;

use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainRequest;
use App\Http\Resources\Admin\MainResource;

class RoleController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Roles = Role::get();
      return response()->json([
          'data' => MainResource::collection($Roles),
          'message' => "Show All Roles Successfully."
      ]);
  }


  public function create(MainRequest $request)
  {
      $this->authorize('manage_users');

         $Role =Role::create ([
              "name" => $request->name
          ]);
         $Role->save();
         return response()->json([
          'data' =>new MainResource($Role),
          'message' => "Role Created Successfully."
      ]);
      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Role = Role::find($id);

      if (!$Role) {
          return response()->json([
              'message' => "Role not found."
          ], 404);
      }

      return response()->json([
          'data' =>new MainResource($Role),
          'message' => "Edit Role By ID Successfully."
      ]);
  }



  public function update(MainRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Role =Role::findOrFail($id);

     if (!$Role) {
      return response()->json([
          'message' => "Role not found."
      ], 404);
  }
     $Role->update([
      "name" => $request->name
      ]);

     $Role->save();
     return response()->json([
      'data' =>new MainResource($Role),
      'message' => " Update Role By Id Successfully."
  ]);

}



  public function destroy(string $id)
  {
      return $this->destroyModel(Role::class, MainResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$Roles=Role::onlyTrashed()->get();
return response()->json([
    'data' =>MainResource::collection($Roles),
    'message' => "Show Deleted Roles Successfully."
]);
}

public function restore(string $id)
{
   $this->authorize('manage_users');
$Role = Role::withTrashed()->where('id', $id)->first();
if (!$Role) {
    return response()->json([
        'message' => "Role not found."
    ], 404);
}
$Role->restore();
return response()->json([
    'data' =>new MainResource($Role),
    'message' => "Restore Role By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Role::class, $id);
  }
}
