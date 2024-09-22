<?php

namespace App\Http\Controllers\Admin;

use App\Models\Month;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainRequest;
use App\Http\Resources\Admin\MainResource;

class MonthController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Months = Month::get();
      return response()->json([
          'data' => MainResource::collection($Months),
          'message' => "Show All Months Successfully."
      ]);
  }


  public function create(MainRequest $request)
  {
      $this->authorize('manage_users');

         $Month =Month::create ([
              "name" => $request->name
          ]);
         $Month->save();
         return response()->json([
          'data' =>new MainResource($Month),
          'message' => "Month Created Successfully."
      ]);

      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Month = Month::find($id);

      if (!$Month) {
          return response()->json([
              'message' => "Month not found."
          ], 404);
      }

      return response()->json([
          'data' =>new MainResource($Month),
          'message' => "Edit Month By ID Successfully."
      ]);
  }



  public function update(MainRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Month =Month::findOrFail($id);

     if (!$Month) {
      return response()->json([
          'message' => "Month not found."
      ], 404);
  }
     $Month->update([
      "name" => $request->name
      ]);

     $Month->save();
     return response()->json([
      'data' =>new MainResource($Month),
      'message' => " Update Month By Id Successfully."
  ]);

}


  public function destroy(string $id)
  {
      return $this->destroyModel(Month::class, MainResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$categories=Month::onlyTrashed()->get();
return response()->json([
    'data' =>MainResource::collection($categories),
    'message' => "Show Deleted Categories Successfully."
]);
}


public function restore(string $id)
{
$this->authorize('manage_users');
$Month = Month::withTrashed()->where('id', $id)->first();
if (!$Month) {
    return response()->json([
        'message' => "Month not found."
    ], 404);
}

$Month->restore();
return response()->json([
    'data' =>new MainResource($Month),
    'message' => "Restore Month By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Month::class, $id);
  }
}
