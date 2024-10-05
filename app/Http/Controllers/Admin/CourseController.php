<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\CourseRequest;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\Admin\LessonResource;
use App\Http\Resources\Admin\AddStudentToCourse;
use App\Http\Resources\Admin\MainCourseResource;
use App\Http\Requests\Admin\StudentCourseRequest;
use App\Http\Resources\Admin\LessonCourseResource;
use App\Http\Resources\Admin\StudentCourseResource;
use App\Http\Resources\Auth\StudentRegisterResource;
use App\Http\Resources\Admin\CourseWithLessonsExamsResource;


class CourseController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Courses = Course::get();
      return response()->json([
          'data' => CourseResource::collection($Courses),
          'message' => "Show All Courses Successfully."
      ]);
  }


  public function create(CourseRequest $request)
  {
      $this->authorize('manage_users');
      $formattedPrice = number_format($request->price, 2, '.', '');
      $status = $request->status ?? 'active';

         $Course =Course::create ([
            "grade_id" => $request->grade_id,
            "month_id" => $request->month_id,
            "nameOfCourse" => $request->nameOfCourse,
            "price" => $formattedPrice,
            "description" => $request->description,
            'status' => $status,
            'creationDate' => now()->format('Y-m-d')

        ]);
        if ($request->hasFile('img')) {
          $imgPath = $request->file('img')->store(Course::storageFolder);
          $Course->img =  $imgPath;
      }

        //   $Course->creationDate = $Course->created_at->format('Y-m-d');
         $Course->save();
         $Course->numOfLessons = $Course->lessons()->count();
         $Course->numOfExams = $Course->exams()->count();
         $Course->save();
         return response()->json([
          'data' =>new CourseResource($Course),
          'message' => "Course Created Successfully."
      ]);

      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Course = Course::find($id);

      if (!$Course) {
          return response()->json([
              'message' => "Course not found."
          ], 404);
      }

      return response()->json([
          'data' =>new CourseResource($Course),
          'message' => "Edit Course By ID Successfully."
      ]);
  }



  public function update(CourseRequest $request, string $id)
  {
      $this->authorize('manage_users');

      $formattedPrice = number_format($request->price, 2, '.', '');
      $status = $request->status ?? 'active';

     $Course =Course::findOrFail($id);

     if (!$Course) {
      return response()->json([
          'message' => "Course not found."
      ], 404);
  }
     $Course->update([
        "grade_id" => $request->grade_id,
        "month_id" => $request->month_id,
        "nameOfCourse" => $request->nameOfCourse,
        "price" => $formattedPrice,
        "description" => $request->description,
        "numOfLessons" => $request->numOfLessons,
        "numOfExams" => $request->numOfExams,
        // 'creationDate' => today()->toDateString(),
        'status' => $status,
        'creationDate' => $request -> creationDate
      ]);

      if ($request->hasFile('img')) {
        if ($Course->img) {
            Storage::disk('public')->delete($Course->img);
        }
        $imgPath = $request->file('img')->store('Course', 'public');
        $Course->img = $imgPath;
    }


     $Course->save();
     return response()->json([
      'data' =>new CourseResource($Course),
      'message' => " Update Course By Id Successfully."
  ]);
}

public function notActive(string $id)
{
    $Course =Course::findOrFail($id);

    if (!$Course) {
     return response()->json([
         'message' => "Course not found."
     ], 404);
 }
    $this->authorize('notActive',$Course);

    $Course->update(['status' => 'notActive']);

    return response()->json([
        'data' => new CourseResource($Course),
        'message' => 'Course has been Not Active.'
    ]);
}
public function active(string $id)
{
    $Course =Course::findOrFail($id);

    if (!$Course) {
     return response()->json([
         'message' => "Course not found."
     ], 404);
 }
    $this->authorize('active',$Course);

    $Course->update(['status' => 'active']);

    return response()->json([
        'data' => new CourseResource($Course),
        'message' => 'Course has been Active.'
    ]);
}

  public function destroy(string $id)
  {
      return $this->destroyModel(Course::class, CourseResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$courses=Course::onlyTrashed()->get();
return response()->json([
    'data' =>CourseResource::collection($courses),
    'message' => "Show Deleted Courses Successfully."
]);
}


public function restore(string $id)
{
$this->authorize('manage_users');
$Course = Course::withTrashed()->where('id', $id)->first();
if (!$Course) {
    return response()->json([
        'message' => "Course not found."
    ], 404);
}

$Course->restore();
return response()->json([
    'data' =>new CourseResource($Course),
    'message' => "Restore Course By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Course::class, $id);
  }

//   public function show($id)
//   {
//     $this->authorize('manage_users');

//       $course = Course::with('lessons')->findOrFail($id);


//       return response()->json([
//      'data' =>new LessonCourseResource($course)
//       ]);
//   }

  public function show($id)
  {
    $this->authorize('manage_users');
      $course = Course::with(['lessons.exam.questions'])->findOrFail($id);
      return response()->json([
     'data' =>new CourseWithLessonsExamsResource($course)
      ]);
  }



public function attachStudentToCourse(StudentCourseRequest $request)
{
    $this->authorize('manage_users');
    $userId = $request->input('user_id');
    $CourseId = $request->input('course_id');
    $purchaseDate = $request->input('purchase_date', now());
    $status = $request->input('status', 'pending');

    $student = User::find($userId);
    if (!$student) {
        return response()->json([
            'message' => 'Student not found'],
             404);
    }


    $course = Course::with('mainCourse')->find($CourseId);
    if (!$course) {
        return response()->json([
            'message' => 'Course not found.']
            , 404);
    }

    $student->courses()->attach($course->id, [
        'purchase_date' => $purchaseDate,
        'status' => $status
    ]);


    $course = $student->courses()
    ->with('mainCourse')
    ->wherePivot('course_id', $course->id)
    ->first();

    if (!$course) {
        return response()->json([
            'message' => 'Failed to retrieve updated course data.'],
             500);
    }

    return response()->json([
        'message' => 'Student successfully added to the course.',
        'student' => new StudentRegisterResource($student),
        'data' => new AddStudentToCourse($course),
    ]);
}


  public function showCourseWithStudent($id)
{
    $this->authorize('manage_users');

    $course = Course::with(['mainCourse','students'])->find($id);
    if (!$course) {
        return response()->json([
            'message' => 'Course not found.'
        ], 404);
    }


    return response()->json([
       'message' => 'Show course By Id With Students Paid.',
        'data' => new StudentCourseResource($course)
    ]);
}


}
