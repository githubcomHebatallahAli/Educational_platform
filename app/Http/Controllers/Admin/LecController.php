<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lec;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainRequest;
use App\Http\Resources\Admin\MainResource;

class LecController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Lecs = Lec::get();
      return response()->json([
          'data' => MainResource::collection($Lecs),
          'message' => "Show All Lecs Successfully."
      ]);
  }


  public function create(MainRequest $request)
  {
      $this->authorize('manage_users');

         $Lec =Lec::create ([

              "name" => $request->name
          ]);
         $Lec->save();
         return response()->json([
          'data' =>new MainResource($Lec),
          'message' => "Lec Created Successfully."
      ]);

      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Lec = Lec::find($id);

      if (!$Lec) {
          return response()->json([
              'message' => "Lec not found."
          ], 404);
      }

      return response()->json([
          'data' =>new MainResource($Lec),
          'message' => "Edit Lec By ID Successfully."
      ]);
  }



  public function update(MainRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Lec =Lec::findOrFail($id);

     if (!$Lec) {
      return response()->json([
          'message' => "Lec not found."
      ], 404);
  }
     $Lec->update([
      "name" => $request->name
      ]);

     $Lec->save();
     return response()->json([
      'data' =>new MainResource($Lec),
      'message' => " Update Lec By Id Successfully."
  ]);

}



  public function destroy(string $id)
  {
      return $this->destroyModel(Lec::class, MainResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$categories=Lec::onlyTrashed()->get();
return response()->json([
    'data' =>MainResource::collection($categories),
    'message' => "Show Deleted Categories Successfully."
]);
}

public function restore(string $id)
{
$this->authorize('manage_users');
$Lec = Lec::withTrashed()->where('id', $id)->first();
if (!$Lec) {
    return response()->json([
        'message' => "Lec not found."
    ], 404);
}

$Lec->restore();
return response()->json([
    'data' =>new MainResource($Lec),
    'message' => "Restore Lec By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Lec::class, $id);
  }
}
