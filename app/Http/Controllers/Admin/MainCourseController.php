<?php

namespace App\Http\Controllers\Admin;


use App\Models\Grade;
use App\Models\MainCourse;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Requests\Admin\MainCourseRequest;
use App\Http\Resources\Admin\MainCourseResource;


class MainCourseController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
{
    $this->authorize('manage_users');

    $grades = Grade::with('mainCourses')->get();
    return response()->json([
        'data' => GradeResource::collection($grades),
        'message' => 'Grades with MainCourses retrieved successfully.'
    ]);
}




  public function create(MainCourseRequest $request)
  {
      $this->authorize('manage_users');

      $formattedPrice = number_format($request->price, 2, '.', '');

         $MainCourse =MainCourse::create ([
              "grade_id" => $request->grade_id,
              "month_id" => $request->month_id,
              "nameOfCourse" => $request->nameOfCourse,
              "price" => $formattedPrice,
          
          ]);
          if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store(MainCourse::storageFolder);
            $MainCourse->img =  $imgPath;
        }
         $MainCourse->save();
         return response()->json([
          'data' =>new MainCourseResource($MainCourse),
          'message' => "MainCourse Created Successfully."
      ]);

      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $MainCourse = MainCourse::find($id);

      if (!$MainCourse) {
          return response()->json([
              'message' => "MainCourse not found."
          ], 404);
      }

      return response()->json([
          'data' =>new MainCourseResource($MainCourse),
          'message' => "Edit MainCourse By ID Successfully."
      ]);
  }


  public function update(MainCourseRequest $request, string $id)
  {
      $this->authorize('manage_users');
      $formattedPrice = number_format($request->price, 2, '.', '');

     $MainCourse =MainCourse::findOrFail($id);

     if (!$MainCourse) {
      return response()->json([
          'message' => "MainCourse not found."
      ], 404);
  }
     $MainCourse->update([
        "grade_id" => $request->grade_id,
        "month_id" => $request->month_id,
        "nameOfCourse" => $request->nameOfCourse,
        "price" => $formattedPrice,
        // "description" => $request->description,
      ]);

      if ($request->hasFile('img')) {
        if ($MainCourse->img) {
            Storage::disk('public')->delete($MainCourse->img);
        }
        $imgPath = $request->file('img')->store('MainCourse', 'public');
        $MainCourse->img = $imgPath;
    }
     $MainCourse->save();
     return response()->json([
      'data' =>new MainCourseResource($MainCourse),
      'message' => " Update MainCourse By Id Successfully."
  ]);
}


  public function destroy(string $id)
  {
      return $this->destroyModel(MainCourse::class, MainCourseResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$categories=MainCourse::onlyTrashed()->get();
return response()->json([
    'data' =>MainCourseResource::collection($categories),
    'message' => "Show Deleted Main Courses Successfully."
]);
}


public function restore(string $id)
{
$this->authorize('manage_users');
$MainCourse = MainCourse::withTrashed()->where('id', $id)->first();
if (!$MainCourse) {
    return response()->json([
        'message' => "MainCourse not found."
    ], 404);
}

$MainCourse->restore();
return response()->json([
    'data' =>new MainCourseResource($MainCourse),
    'message' => "Restore MainCourse By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(MainCourse::class, $id);
  }




}
