<?php

namespace App\Http\Controllers\Admin;

use Log;


use App\Models\Lesson;
use GuzzleHttp\Client;
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
// {
//     ini_set('memory_limit', '2G');
//     $this->authorize('manage_users');

//     try {
//         $Lesson = Lesson::create([
//             "grade_id" => $request->grade_id,
//             "lec_id" => $request->lec_id,
//             "course_id" => $request->course_id,
//             "title" => $request->title,
//             "description" => $request->description,
//             "duration" => $request->duration,
//         ]);

//         if ($request->hasFile('poster')) {
//             $posterPath = $request->file('poster')->store(Lesson::storageFolder);
//             $Lesson->poster = $posterPath;
//         }


//         if ($request->hasFile('video')) {
//             $videoPath = $request->file('video')->store(Lesson::storageFolder);
//             $Lesson->video = $videoPath;

//          if ($request->hasFile('ExplainPdf')) {
//              $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
//              $Lesson->ExplainPdf = $ExplainPdfPath;

//              $pdfParser = new PdfParser();
//              $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
//              $numberOfPages = count($pdf->getPages());

//             $Lesson->numOfPdf = $numberOfPages;
//         }

//         $Lesson->save();
//         $course = $Lesson->course;
//         $course->numOfLessons = $course->lessons()->count();
//         $course->save();

//         return response()->json([
//             'data' => new LessonResource($Lesson),
//             'message' => "Lesson Created Successfully."
//         ]);

//     } catch (\Exception $e) {
//         // تسجيل الخطأ في السجلات
//         Log::error($e->getMessage());

//         return response()->json([
//             'error' => 'An error occurred while creating the lesson.',
//             'details' => $e->getMessage()
//         ], 500);
//     }


// }

public function create(LessonRequest $request)
{
    ini_set('memory_limit', '2G');
    $this->authorize('manage_users');

    try {
        $client = new Client();

        // إنشاء الدرس
        $lesson = Lesson::create([
            "grade_id" => $request->grade_id,
            "lec_id" => $request->lec_id,
            "course_id" => $request->course_id,
            "title" => $request->title,
            "description" => $request->description,
            "duration" => $request->duration,
        ]);

        // رفع الصورة (Poster) إذا وجدت
        if ($request->hasFile('poster')) {
            $posterFile = $request->file('poster');
            $posterPath = $posterFile->store(Lesson::storageFolder);
            $lesson->poster = $posterPath;
        }

        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');

            // التحقق من حجم الفيديو (حد أقصى 2 جيجابايت)
            if ($videoFile->getSize() > 2 * 1024 * 1024 * 1024) {
                throw new \Exception('حجم الفيديو يتجاوز الحد المسموح به (2 جيجابايت).');
            }

            $libraryId = config('services.bunny.library_id');
            $apiKey = config('services.bunny.api_key');

            if (!$libraryId || !$apiKey) {
                throw new \Exception('معرف المكتبة أو مفتاح API مفقود في الإعدادات.');
            }

            // 1. **إنشاء فيديو جديد في Bunny Stream**
            $createVideoUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos";
            $createVideoResponse = $client->post($createVideoUrl, [
                'headers' => [
                    'AccessKey' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'title' => $request->title,
                ],
            ]);

            $createVideoData = json_decode($createVideoResponse->getBody(), true);
            if (!isset($createVideoData['guid'])) {
                throw new \Exception('فشل في إنشاء الفيديو على BunnyCDN.');
            }

            $videoId = $createVideoData['guid'];

            // 2. **إنشاء التوقيع المطلوب للرفع**
            $expirationTime = time() + 3600; // صلاحية التوقيع لمدة ساعة
            $signature = hash('sha256', "{$libraryId}{$apiKey}{$expirationTime}{$videoId}");

            // 3. **بدء رفع الفيديو باستخدام TUS Upload**
            $tusUploadUrl = "https://video.bunnycdn.com/tusupload";
            $videoStream = fopen($videoFile->getRealPath(), 'r');

            $tusResponse = $client->request('POST', $tusUploadUrl, [
                'headers' => [
                    'AuthorizationSignature' => $signature,
                    'AuthorizationExpire' => $expirationTime,
                    'VideoId' => $videoId,
                    'LibraryId' => $libraryId,
                    'Tus-Resumable' => '1.0.0',
                    'Upload-Length' => $videoFile->getSize(),
                    'Content-Type' => 'application/offset+octet-stream',
                ],
                'body' => $videoStream,
            ]);

            if ($tusResponse->getStatusCode() !== 204) {
                throw new \Exception('فشل رفع الفيديو عبر TUS.');
            }

            fclose($videoStream);

            // 4. **حفظ رابط الفيديو بعد التأكد من نجاح الرفع**
            $lesson->video = "https://video.bunnycdn.com/play/{$libraryId}/{$videoId}";
        }

        if ($request->hasFile('ExplainPdf')) {
            $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
            $lesson->ExplainPdf = $ExplainPdfPath;

            $pdfParser = new PdfParser();
            $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
            $numberOfPages = count($pdf->getPages());

            $lesson->numOfPdf = $numberOfPages;
        }

        $lesson->save();

        $course = $lesson->course;
        $course->numOfLessons = $course->lessons()->count();
        $course->save();

        return response()->json([
            'data' => new LessonResource($lesson),
            'message' => "Lesson Created Successfully."
        ]);

    } catch (\Exception $e) {
        Log::error('Error creating lesson: ' . $e->getMessage());

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

        if ($request->hasFile('poster')) {
            if ($Lesson->poster) {
                Storage::disk('bunnycdn')->delete($Lesson->poster);
            }
            $posterPath = $request->file('poster')->store('Lessons', 'bunnycdn');
            $Lesson->poster = $posterPath;
            $Lesson->poster_url = Storage::disk('bunnycdn')->url($posterPath); // الحصول على الرابط المباشر
        }

        // تحديث الفيديو إذا تم رفع واحد جديد
        if ($request->hasFile('video')) {
            if ($Lesson->video) {
                Storage::disk('bunnycdn')->delete($Lesson->video);
            }
            $videoPath = $request->file('video')->store('Lessons', 'bunnycdn');
            $Lesson->video = $videoPath;
            $Lesson->video_url = Storage::disk('bunnycdn')->url($videoPath); // الحصول على الرابط المباشر
        }

        // تحديث ملف PDF إذا تم رفع واحد جديد
        if ($request->hasFile('ExplainPdf')) {
            if ($Lesson->ExplainPdf) {
                Storage::disk('bunnycdn')->delete($Lesson->ExplainPdf);
            }
            $ExplainPdfPath = $request->file('ExplainPdf')->store('Lessons', 'bunnycdn');
            $Lesson->ExplainPdf = $ExplainPdfPath;
            $Lesson->ExplainPdf_url = Storage::disk('bunnycdn')->url($ExplainPdfPath); // الحصول على الرابط المباشر
            try {
                $pdfParser = new PdfParser();
                $pdf = $pdfParser->parseFile(Storage::disk('bunnycdn')->path($ExplainPdfPath));
                $numberOfPages = count($pdf->getPages());
                $Lesson->numOfPdf = $numberOfPages;
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error processing PDF: ' . $e->getMessage()
                ], 500);
            }


        // if ($request->hasFile('poster')) {
        //     if ($Lesson->poster) {
        //         Storage::disk('public')->delete($Lesson->poster);
        //     }
        //     $posterPath = $request->file('poster')->store('Lessons', 'public');
        //     $Lesson->poster = $posterPath;
        // }

        // if ($request->hasFile('video')) {
        //     if ($Lesson->video) {
        //         Storage::disk('public')->delete($Lesson->video);
        //     }
        //     $videoPath = $request->file('video')->store('Lessons', 'public');
        //     $Lesson->video = $videoPath;
        // }

        // if ($request->hasFile('ExplainPdf')) {
        //     if ($Lesson->ExplainPdf) {
        //         Storage::disk('public')->delete($Lesson->ExplainPdf);
        //     }
        //     $ExplainPdfPath = $request->file('ExplainPdf')->store('Lessons', 'public');
        //     $Lesson->ExplainPdf = $ExplainPdfPath;

        //     $pdfParser = new PdfParser();
        //     $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
        //     $numberOfPages = count($pdf->getPages());

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
