<?php

namespace App\Http\Controllers\Admin;

use App\Models\Test;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainRequest;
use App\Http\Resources\Admin\MainResource;

class TestController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Tests = Test::get();
      return response()->json([
          'data' => MainResource::colTesttion($Tests),
          'message' => "Show All Tests Successfully."
      ]);
  }


  public function create(MainRequest $request)
  {
      $this->authorize('manage_users');

         $Test =Test::create ([

              "name" => $request->name
          ]);
         $Test->save();
         return response()->json([
          'data' =>new MainResource($Test),
          'message' => "Test Created Successfully."
      ]);

      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Test = Test::find($id);

      if (!$Test) {
          return response()->json([
              'message' => "Test not found."
          ], 404);
      }

      return response()->json([
          'data' =>new MainResource($Test),
          'message' => "Edit Test By ID Successfully."
      ]);
  }



  public function update(MainRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Test =Test::findOrFail($id);

     if (!$Test) {
      return response()->json([
          'message' => "Test not found."
      ], 404);
  }
     $Test->update([
      "name" => $request->name
      ]);

     $Test->save();
     return response()->json([
      'data' =>new MainResource($Test),
      'message' => " Update Test By Id Successfully."
  ]);

}



  public function destroy(string $id)
  {
      return $this->destroyModel(Test::class, MainResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$categories=Test::onlyTrashed()->get();
return response()->json([
    'data' =>MainResource::colTesttion($categories),
    'message' => "Show Deleted Categories Successfully."
]);
}

public function restore(string $id)
{
$this->authorize('manage_users');
$Test = Test::withTrashed()->where('id', $id)->first();
if (!$Test) {
    return response()->json([
        'message' => "Test not found."
    ], 404);
}

$Test->restore();
return response()->json([
    'data' =>new MainResource($Test),
    'message' => "Restore Test By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Test::class, $id);
  }
}
