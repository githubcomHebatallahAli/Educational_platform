<?php

namespace App\Http\Controllers\Admin;

use FFMpeg\FFMpeg;
use App\Models\Lesson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Smalot\PdfParser\Parser as PdfParser;
use App\Http\Requests\Admin\LessonRequest;
use App\Http\Resources\Admin\LessonResource;




class LessonController extends Controller
{
    public function showAll()
    {
        $this->authorize('manage_users');

        $Lessons = Lesson::with('students')->get();
        return response()->json([
            'data' => LessonResource::collection($Lessons),
            'message' => "Show All Lessons Successfully."
        ]);
    }


    public function create(LessonRequest $request)
{
    ini_set('memory_limit', '2G');
    $this->authorize('manage_users');

    try {
        $Lesson = Lesson::create([
            "grade_id" => $request->grade_id,
            "lec_id" => $request->lec_id,
            "course_id" => $request->course_id,
            "title" => $request->title,
            "description" => $request->description,
        ]);

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store(Lesson::storageFolder);
            $Lesson->poster = $posterPath;
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store(Lesson::storageFolder);
            $videoFullPath = public_path($videoPath);

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => 'E:/ffmpeg/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'E:/ffmpeg/bin/ffprobe.exe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            $video = $ffmpeg->open($videoFullPath);
            $durationInSeconds = $video->getFormat()->get('duration');
            $duration = gmdate('H:i:s', $durationInSeconds);

            $Lesson->video = $videoPath;
            $Lesson->duration = $duration;
        }

        if ($request->hasFile('ExplainPdf')) {
            $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
            $Lesson->ExplainPdf = $ExplainPdfPath;

            $pdfParser = new PdfParser();
            $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
            $numberOfPages = count($pdf->getPages());

            $Lesson->numOfPdf = $numberOfPages;
        }

        $Lesson->save();
        $course = $Lesson->course;
        $course->numOfLessons = $course->lessons()->count();
        $course->save();

        return response()->json([
            'data' => new LessonResource($Lesson),
            'message' => "Lesson Created Successfully."
        ]);

    } catch (\Exception $e) {

        \Log::error($e->getMessage());

        return response()->json([
            'error' => 'An error occurred while creating the lesson.',
            'details' => $e->getMessage()
        ], 500);
    }
    }




//         public function assignStudentsToLesson(StudentLessonRequest $request)
//         {
//             $this->authorize('manage_users');


// $Lesson = Lesson::with('students.user')->find($request->lesson_id);

// $paidStudentIds = Student::whereIn('id', $request->student_ids)
//     ->where('isPay', 'pay')
//     ->pluck('id')
//     ->toArray();

// $unpaidStudentIds = array_diff($request->student_ids, $paidStudentIds);

// $syncResult = $Lesson->students()->sync($paidStudentIds);

// if (!empty($syncResult['attached']) || !empty($syncResult['updated'])) {

//     $paidStudents = Student::with('user')
//         ->whereIn('id', $paidStudentIds)
//         ->get();


//     if (!empty($unpaidStudentIds)) {
//         $unpaidStudents = Student::with('user')
//             ->whereIn('id', $unpaidStudentIds)
//             ->get();

//         return response()->json([
//             'message' => 'Only students who have paid were added to the Lesson successfully.',
//             'paid_students' => StudentResource::collection($paidStudents),
//             'error' => 'Some students have not paid.',
//             'unpaid_students' => StudentResource::collection($unpaidStudents),
//         ], 400);
//     }

//     return response()->json([
//         'message' => 'Only students who have paid were added to the Lesson successfully.',
//         'paid_students' => StudentResource::collection($paidStudents),
//     ]);
// } else {
//     return response()->json([
//         'error' => 'Failed to add paid students to the lesson.',
//     ], 500);
// }

//         }

//         public function revokeAllStudentsFromLesson(Request $request)
// {

//     $this->authorize('manage_users');
//     $validator = Validator::make($request->all(), [
//         'lesson_id' => 'required|exists:lessons,id',
//     ]);

//     // إذا كانت البيانات المدخلة غير صحيحة
//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors(),
//         ], 422);
//     }

//     $Lesson = Lesson::with('students')->find($request->lesson_id);
//     if (!$Lesson) {
//         return response()->json([
//             'error' => 'Lesson not found.',
//         ], 404);
//     }

//     $Lesson->students()->detach();

//     return response()->json([
//         'message' => 'All students have been removed from the Lesson successfully.',
//     ], 200);
// }



    public function edit(string $id)
    {
        $this->authorize('manage_users');
        $Lesson = Lesson::with('students')->find($id);

        if (!$Lesson) {
            return response()->json([
                'message' => "Lesson not found."
            ], 404);
        }
        return response()->json([
            'data' =>new LessonResource($Lesson),
            'message' => "Edit Lesson By ID Successfully."
        ]);
    }



    public function update(LessonRequest $request, string $id)
    {
        ini_set('memory_limit', '2G');
        $this->authorize('manage_users');
        $Lesson = Lesson::findOrFail($id);

        $Lesson->update([
            "grade_id" => $request->grade_id,
            "lec_id" => $request->lec_id,
            "course_id" => $request->course_id,
            "title" => $request->title,
            "description" => $request->description,
        ]);

        if ($request->hasFile('poster')) {
            if ($Lesson->poster) {
                Storage::disk('public')->delete($Lesson->poster);
            }
            $posterPath = $request->file('poster')->store('Lessons', 'public');
            $Lesson->poster = $posterPath;
        }

        if ($request->hasFile('video')) {
            if ($Lesson->video) {
                Storage::disk('public')->delete($Lesson->video);
            }
            $videoPath = $request->file('video')->store('Lessons', 'public');
            $videoFullPath = public_path($videoPath);

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => 'E:/ffmpeg/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'E:/ffmpeg/bin/ffprobe.exe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            $video = $ffmpeg->open($videoFullPath);
            $durationInSeconds = $video->getFormat()->get('duration');
            $duration = gmdate('H:i:s', $durationInSeconds);

            $Lesson->video = $videoPath;
            $Lesson->duration = $duration;
        }

        if ($request->hasFile('ExplainPdf')) {
            if ($Lesson->ExplainPdf) {
                Storage::disk('public')->delete($Lesson->ExplainPdf);
            }
            $ExplainPdfPath = $request->file('ExplainPdf')->store('Lessons', 'public');
            $Lesson->ExplainPdf = $ExplainPdfPath;

            $pdfParser = new PdfParser();
            $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
            $numberOfPages = count($pdf->getPages());

            $Lesson->numOfPdf = $numberOfPages;
        }

        $Lesson->save();

        return response()->json([
            'data' => new LessonResource($Lesson),
            'message' => "Lesson updated successfully."
        ]);

}

public function destroy(string $id){
    $this->authorize('manage_users');
    $Lesson =Lesson::find($id);
    if (!$Lesson) {
     return response()->json([
         'message' => "Lesson not found."
     ], 404);
 }

    $Lesson->delete($id);
    return response()->json([
        'data' =>new LessonResource($Lesson),
        'message' => " Soft Delete Lesson By Id Successfully."
    ]);
}

    public function showDeleted(){

        $this->authorize('manage_users');

    $Lessons=Lesson::onlyTrashed()->get();
    return response()->json([
        'data' =>LessonResource::collection($Lessons),
        'message' => "Show Deleted Lessons Successfully."
    ]);
}

public function restore(string $id)
{
    $this->authorize('manage_users');

    $Lesson = Lesson::withTrashed()->where('id', $id)->first();
    if (!$Lesson) {
        return response()->json([
            'message' => "Lesson not found."
        ], 404);
    }
    $Lesson->restore();
    return response()->json([
        'message' => "Restore Lesson By Id Successfully."
    ]);
}

public function forceDelete(string $id){

    $this->authorize('manage_users');

    $Lesson=Lesson::withTrashed()->where('id',$id)->first();
    if (!$Lesson) {
        return response()->json([
            'message' => "Lesson not found."
        ], 404);
    }

    $Lesson->forceDelete();
    return response()->json([
        'message' => " Force Delete Lesson By Id Successfully."
    ]);
}

// public function assignExamToLesson(ExamLessonRequest $request)
// {
//     $this->authorize('manage_users');

//     $lesson = Lesson::with('exams')->find($request->lesson_id);
//     if (!$lesson) {
//         return response()->json([
//             'message' => 'Lesson not found'
//         ], 404);
//     }

//     $exam = Exam::find($request->exam_id);
//     if (!$exam) {
//         return response()->json([
//             'message' => 'Exam not found'
//         ], 404);
//     }


//     $lesson->exams()->attach($exam->id);
//     $lesson->load('exams');

//     return response()->json([
//         'data' => new LessonResource($lesson),
//         'message' => 'Exam added to Lesson successfully'
//     ]);
// }

// public function revokeExamFromLesson(ExamLessonRequest $request)
// {

//     $this->authorize('manage_users');


//     $lesson = Lesson::with('exams')->find($request->lesson_id);
//     if (!$lesson) {
//         return response()->json([
//             'message' => 'Lesson not found'
//         ], 404);
//     }


//     $exam = Exam::find($request->exam_id);
//     if (!$exam) {
//         return response()->json([
//             'message' => 'Exam not found'
//         ], 404);
//     }

//     $lesson->exams()->detach($request->exam_id);

//     $lesson->load('exams');


//     return response()->json([
//         'data' => new LessonResource($lesson),
//         'message' => 'Exam revoked from Lesson successfully'
//     ]);
// }


}
