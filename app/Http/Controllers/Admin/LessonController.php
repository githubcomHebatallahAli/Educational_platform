<?php

namespace App\Http\Controllers\Admin;

use Log;


use App\Models\Lesson;
use GuzzleHttp\Client;
use FFMpeg\Media\Video;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
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


//     public function create(LessonRequest $request)
//     {
//         ini_set('memory_limit', '2G');
//         $this->authorize('manage_users');

//         try {
//             $Lesson = Lesson::create([
//                 "grade_id" => $request->grade_id,
//                 "lec_id" => $request->lec_id,
//                 "course_id" => $request->course_id,
//                 "title" => $request->title,
//                 "description" => $request->description,
//                 "duration" => $request->duration,
//             ]);

//             if ($request->hasFile('poster')) {
//                 $posterPath = $request->file('poster')->store(Lesson::storageFolder);
//                 $Lesson->poster = $posterPath;
//             }

//             // if ($request->hasFile('video')) {
//             //     $videoPath = $request->file('video')->store(Lesson::storageFolder);
//             //     $Lesson->video = $videoPath;
//             // }

//             if ($request->video_url) {
//                 $Lesson->video = $request->video_url;
//             }

//             if ($request->hasFile('ExplainPdf')) {
//                 $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
//                 $Lesson->ExplainPdf = $ExplainPdfPath;

//                 $pdfParser = new PdfParser();
//                 $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
//                 $numberOfPages = count($pdf->getPages());

//                 $Lesson->numOfPdf = $numberOfPages;
//             }

//             $Lesson->save();

//             $course = $Lesson->course;
//             $course->numOfLessons = $course->lessons()->count();
//             $course->save();

//             return response()->json([
//                 'data' => new LessonResource($Lesson),
//                 'message' => "Lesson Created Successfully."
//             ]);

//         } catch (\Exception $e) {
//             // تسجيل الخطأ في السجلات
//             Log::error($e->getMessage());

//             return response()->json([
//                 'error' => 'An error occurred while creating the lesson.',
//                 'details' => $e->getMessage()
//             ], 500);
//         }
//     }


//     public function uploadChunk(Request $request)
// {
//     $file = $request->file('file');
//     $chunkNumber = $request->input('chunkNumber');
//     $fileName = $request->input('fileName');
//     $totalChunks = $request->input('totalChunks');

//     $tempPath = storage_path("app/temp_videos/{$fileName}");
//     if (!file_exists($tempPath)) {
//         mkdir($tempPath, 0777, true);
//     }

//     $file->move($tempPath, "{$fileName}.part{$chunkNumber}");

//     return response()->json([
//         'message' => "تم رفع الجزء {$chunkNumber} من {$totalChunks}"
//     ]);
// }


// public function mergeChunks(Request $request)
// {
//     $fileName = $request->input('fileName');
//     $tempPath = storage_path("app/temp_videos/{$fileName}");
//     $finalPath = storage_path("app/videos/{$fileName}");

//     $finalFile = fopen($finalPath, "w");
//     $chunkNumber = 1;

//     while (file_exists("$tempPath/{$fileName}.part{$chunkNumber}")) {
//         $chunk = fopen("$tempPath/{$fileName}.part{$chunkNumber}", "r");
//         stream_copy_to_stream($chunk, $finalFile);
//         fclose($chunk);
//         unlink("$tempPath/{$fileName}.part{$chunkNumber}");
//         $chunkNumber++;
//     }

//     fclose($finalFile);
//     rmdir($tempPath);

//     return response()->json([
//         'message' => 'تم دمج الفيديو بنجاح',
//         'video_url' => url("storage/videos/{$fileName}")
//     ]);
// }





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
            "duration" => $request->duration,
        ]);

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store(Lesson::storageFolder);
            $Lesson->poster = $posterPath;
        }

        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');

            $libraryId = config('services.streambunny.project_id');
            $apiKey = config('services.streambunny.api_key');
            $videoId = "fd1a981d-e030-40e4-861a-51ebd4bff1a8";
            $expirationTime = time() + 3600;

            $signature = hash('sha256', $libraryId . $apiKey . $expirationTime . $videoId);


            $uploadUrl = "https://video.bunnycdn.com/tusupload";
            $uploadHeaders = [
                'AuthorizationSignature' => $signature,
                'AuthorizationExpire' => $expirationTime,
                'VideoId' => $videoId,
                'LibraryId' => $libraryId,
                'Content-Type' => 'application/json',
            ];

            $upload = new \tusphp\Tus\Client($uploadUrl);
            $upload->setKey($signature);
            $upload->setMetadata([
                'filetype' => $videoFile->getMimeType(),
                'title' => $Lesson->title
            ]);
            $upload->file($videoFile->getRealPath(), $Lesson->title);

            $retryCount = 0;
            $maxRetries = 3;
            $retryDelay = 5000;

            while ($retryCount < $maxRetries) {
                try {
                    $upload->upload();
                    break;
                } catch (\Exception $e) {
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        return response()->json(['error' => 'فشل رفع الفيديو بعد عدة محاولات: ' . $e->getMessage()], 500);
                    }
                    usleep($retryDelay * 1000);
                }
            }

            $Lesson->video = $videoId;
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
        Log::error($e->getMessage());

        return response()->json([
            'error' => 'An error occurred while creating the lesson.',
            'details' => $e->getMessage()
        ], 500);
    }
}

    public function edit(string $id)
    {
        $this->authorize('manage_users');
        $Lesson = Lesson::with('students')->find($id);

        if (!$Lesson) {
            return response()->json([
                'message' => "Lesson not found."
            ]);
        }
        return response()->json([
            'data' =>new LessonResource($Lesson),
            'message' => "Edit Lesson By ID Successfully."
        ]);
    }



    public function update(Request $request, string $id)
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
            "duration" => $request->duration,
        ]);

        // if ($request->hasFile('poster')) {
        //     if ($Lesson->poster) {
        //         Storage::disk('bunnycdn')->delete($Lesson->poster);
        //     }
        //     $posterPath = $request->file('poster')->store('Lessons', 'bunnycdn');
        //     $Lesson->poster = $posterPath;
        //     $Lesson->poster_url = Storage::disk('bunnycdn')->url($posterPath); // الحصول على الرابط المباشر
        // }

        // // تحديث الفيديو إذا تم رفع واحد جديد
        // if ($request->hasFile('video')) {
        //     if ($Lesson->video) {
        //         Storage::disk('bunnycdn')->delete($Lesson->video);
        //     }
        //     $videoPath = $request->file('video')->store('Lessons', 'bunnycdn');
        //     $Lesson->video = $videoPath;
        //     $Lesson->video_url = Storage::disk('bunnycdn')->url($videoPath); // الحصول على الرابط المباشر
        // }

        // // تحديث ملف PDF إذا تم رفع واحد جديد
        // if ($request->hasFile('ExplainPdf')) {
        //     if ($Lesson->ExplainPdf) {
        //         Storage::disk('bunnycdn')->delete($Lesson->ExplainPdf);
        //     }
        //     $ExplainPdfPath = $request->file('ExplainPdf')->store('Lessons', 'bunnycdn');
        //     $Lesson->ExplainPdf = $ExplainPdfPath;
        //     $Lesson->ExplainPdf_url = Storage::disk('bunnycdn')->url($ExplainPdfPath); // الحصول على الرابط المباشر
        //     try {
        //         $pdfParser = new PdfParser();
        //         $pdf = $pdfParser->parseFile(Storage::disk('bunnycdn')->path($ExplainPdfPath));
        //         $numberOfPages = count($pdf->getPages());
        //         $Lesson->numOfPdf = $numberOfPages;
        //     } catch (\Exception $e) {
        //         return response()->json([
        //             'error' => 'Error processing PDF: ' . $e->getMessage()
        //         ], 500);
        //     }


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
            $Lesson->video = $videoPath;
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
    $lesson = Lesson::findOrFail($id);
    $course = $lesson->course;

    $lesson->delete();

    $course->numOfLessons = $course->lessons()->count();
    $course->save();
    return response()->json([
        'data' =>new LessonResource($lesson),
        'actual_lesson_count' => $course->numOfLessons,
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

    $lesson = Lesson::onlyTrashed()->findOrFail($id);
    $lesson->restore();

    $course = $lesson->course;
    $course->numOfLessons = $course->lessons()->count();
    $course->save();
    return response()->json([
        'message' => "Restore Lesson By Id Successfully.",
        'data' =>new LessonResource($lesson),
        'actual_lesson_count' => $course->numOfLessons,
    ]);
}

public function forceDelete(string $id){

    $this->authorize('manage_users');

    $lesson = Lesson::withTrashed()->findOrFail($id);
    $course = $lesson->course;

    $lesson->forceDelete();

    $course->numOfLessons = $course->lessons()->count();
    $course->save();
    return response()->json([
        'message' => " Force Delete Lesson By Id Successfully.",
        'actual_lesson_count' => $course->numOfLessons,
    ]);
}




}
