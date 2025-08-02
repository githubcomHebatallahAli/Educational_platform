<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;


use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use App\Http\Requests\Admin\LessonRequest;
use App\Http\Resources\Admin\LessonResource;
use GuzzleHttp\Client as GuzzleClient;




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



// public function create(LessonRequest $request)
// {
//     // ini_set('memory_limit', '2G');
//     $this->authorize('manage_users');

//     try {
//         // إنشاء الدرس
//         $Lesson = Lesson::create([
//             "grade_id" => $request->grade_id,
//             "lec_id" => $request->lec_id,
//             "course_id" => $request->course_id,
//             "title" => $request->title,
//             "description" => $request->description,
//             "duration" => $request->duration,
//         ]);

//         // رفع صورة الدرس (Poster)
//         if ($request->hasFile('poster') && $request->file('poster')->isValid()) {
//             $posterPath = $request->file('poster')->store(Lesson::storageFolder);
//             $Lesson->poster = $posterPath;
//         }

//         // رفع فيديو الدرس إلى BunnyCDN
//         if ($request->hasFile('video') && $request->file('video')->isValid()) {
//             $videoFile = $request->file('video');

//             // إعداد بيانات BunnyCDN
//             $libraryId = config('services.streambunny.library_id');
//             $apiKey = config('services.streambunny.api_key');

//             // إنشاء فيديو جديد في BunnyCDN
//             $createVideoUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos";
//             $createVideoHeaders = [
//                 'AccessKey' => $apiKey,
//                 'Accept' => 'application/json',
//                 'Content-Type' => 'application/json',
//             ];

//             $client = new GuzzleClient();
//             $createVideoResponse = $client->post($createVideoUrl, [
//                 'headers' => $createVideoHeaders,
//                 'json' => [
//                     'title' => $Lesson->title, // إرسال البيانات كـ JSON
//                 ],
//             ]);

//             if ($createVideoResponse->getStatusCode() === 200) {
//                 $videoData = json_decode($createVideoResponse->getBody(), true);
//                 $videoId = $videoData['guid']; // الحصول على VideoId

//                 $uploadUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos/{$videoId}";
//                 $uploadHeaders = [
//                     'AccessKey' => $apiKey,
//                     'Content-Type' => 'application/octet-stream',
//                 ];

//                 $uploadResponse = $client->put($uploadUrl, [
//                     'headers' => $uploadHeaders,
//                     'body' => fopen($videoFile->getRealPath(), 'r'),
//                 ]);

//                 if ($uploadResponse->getStatusCode() === 200) {
//                     $Lesson->video = $videoId; // حفظ VideoId في قاعدة البيانات
//                 } else {
//                     return response()->json(['error' => 'فشل رفع الفيديو إلى BunnyCDN.'], 500);
//                 }
//             } else {
//                 return response()->json(['error' => 'فشل إنشاء الفيديو في BunnyCDN.'], 500);
//             }
//         }

//         // رفع ملف الـ ExplainPdf ومعالجة عدد الصفحات
//         if ($request->hasFile('ExplainPdf') && $request->file('ExplainPdf')->isValid()) {
//             $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
//             $Lesson->ExplainPdf = $ExplainPdfPath;

//             $pdfParser = new PdfParser();
//             $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
//             $numberOfPages = count($pdf->getPages());

//             $Lesson->numOfPdf = $numberOfPages;
//         }

//         $Lesson->save();

//         // تحديث عدد الدروس في الكورس
//         $course = $Lesson->course;
//         $course->numOfLessons = $course->lessons()->count();
//         $course->save();

//         return response()->json([
//             'data' => new LessonResource($Lesson),
//             'message' => "Lesson Created Successfully."
//         ]);

//     } catch (\Exception $e) {
//         // تسجيل الخطأ وإرجاع رسالة الخطأ
//         Log::error($e->getMessage());

//         return response()->json([
//             'error' => 'An error occurred while creating the lesson.',
//             'details' => $e->getMessage()
//         ], 500);
//     }
// }

public function create(LessonRequest $request)
{
    // ini_set('memory_limit', '2G');
    $this->authorize('manage_users');

    try {
        // إنشاء الدرس
        $Lesson = Lesson::create([
            "grade_id" => $request->grade_id,
            "lec_id" => $request->lec_id,
            "course_id" => $request->course_id,
            "title" => $request->title,
            "description" => $request->description,
            "duration" => $request->duration,
        ]);

        // رفع صورة الدرس (Poster)
        if ($request->hasFile('poster') && $request->file('poster')->isValid()) {
            $posterPath = $request->file('poster')->store(Lesson::storageFolder);
            $Lesson->poster = $posterPath;
        }

        // رفع فيديو الدرس إلى BunnyCDN
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoFile = $request->file('video');

            // إعداد بيانات BunnyCDN
            $libraryId = config('services.streambunny.library_id');
            $apiKey = config('services.streambunny.api_key');

            // إنشاء فيديو جديد في BunnyCDN
            $createVideoUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos";
            $createVideoHeaders = [
                'AccessKey' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $client = new GuzzleClient();
            $createVideoResponse = $client->post($createVideoUrl, [
                'headers' => $createVideoHeaders,
                'json' => [
                    'title' => $Lesson->title, // إرسال البيانات كـ JSON
                ],
            ]);

            if ($createVideoResponse->getStatusCode() === 200) {
                $videoData = json_decode($createVideoResponse->getBody(), true);
                $videoId = $videoData['guid']; // الحصول على VideoId

                $uploadUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos/{$videoId}";
                $uploadHeaders = [
                    'AccessKey' => $apiKey,
                    'Content-Type' => 'application/octet-stream',
                ];

                $uploadResponse = $client->put($uploadUrl, [
                    'headers' => $uploadHeaders,
                    'body' => fopen($videoFile->getRealPath(), 'r'),
                ]);

                if ($uploadResponse->getStatusCode() === 200) {
                    $zone = config('services.streambunny.zone'); // سيتم قراءة الـ Zone من الإعدادات
                    $videoUrl = "https://{$zone}.b-cdn.net/{$videoId}/play_480p.mp4";

                    $Lesson->video = $videoUrl; // حفظ رابط الفيديو في قاعدة البيانات
                } else {
                    return response()->json([
                        'error' => 'فشل رفع الفيديو إلى BunnyCDN.'
                    ], 500);
                }
            } else {
                return response()->json(['error' => 'فشل إنشاء الفيديو في BunnyCDN.'], 500);
            }
        }
        if ($request->hasFile('ExplainPdf') && $request->file('ExplainPdf')->isValid()) {
            $ExplainPdfPath = $request->file('ExplainPdf')->store(Lesson::storageFolder);
            $Lesson->ExplainPdf = $ExplainPdfPath;

            $pdfParser = new PdfParser();
            $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
            $numberOfPages = count($pdf->getPages());

            $Lesson->numOfPdf = $numberOfPages;

}

$Lesson->save();
        // تحديث عدد الدروس في الكورس
        $course = $Lesson->course;
        $course->numOfLessons = $course->lessons()->count();
        $course->save();

        // إضافة رابط الفيديو إلى الـ response
        $responseData = [
            'data' => new LessonResource($Lesson),
            'message' => "Lesson Created Successfully.",
            // 'video_url' => $Lesson->video, // رابط الفيديو المخزن في قاعدة البيانات
        ];

        return response()->json($responseData);

    } catch (\Exception $e) {
        // تسجيل الخطأ وإرجاع رسالة الخطأ
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



//     public function update(LessonRequest $request, string $id)
//     {
//         // ini_set('memory_limit', '2G');
//         $this->authorize('manage_users');
//         $Lesson = Lesson::findOrFail($id);

//         $Lesson->update([
//             "grade_id" => $request->grade_id,
//             "lec_id" => $request->lec_id,
//             "course_id" => $request->course_id,
//             "title" => $request->title,
//             "description" => $request->description,
//             "duration" => $request->duration,
//         ]);


//         if ($request->hasFile('poster')) {
//             if ($Lesson->poster) {
//                 Storage::disk('public')->delete($Lesson->poster);
//             }
//             $posterPath = $request->file('poster')->store('Lessons', 'public');
//             $Lesson->poster = $posterPath;
//         }

//         if ($request->hasFile('video')) {
//             if ($Lesson->video) {
//                 Storage::disk('public')->delete($Lesson->video);
//             }
//             $videoPath = $request->file('video')->store('Lessons', 'public');
//             $Lesson->video = $videoPath;
//         }

//         if ($request->hasFile('ExplainPdf')) {
//             if ($Lesson->ExplainPdf) {
//                 Storage::disk('public')->delete($Lesson->ExplainPdf);
//             }
//             $ExplainPdfPath = $request->file('ExplainPdf')->store('Lessons', 'public');
//             $Lesson->ExplainPdf = $ExplainPdfPath;

//             $pdfParser = new PdfParser();
//             $pdf = $pdfParser->parseFile(public_path($ExplainPdfPath));
//             $numberOfPages = count($pdf->getPages());

//             $Lesson->numOfPdf = $numberOfPages;
//         }

//         $Lesson->save();

//         return response()->json([
//             'data' => new LessonResource($Lesson),
//             'message' => "Lesson updated successfully."
//         ]);

// }

public function update(Request $request, string $id)
{
    $this->authorize('manage_users');
    $Lesson = Lesson::findOrFail($id);

    try {
        // تحديث تفاصيل الدرس الأساسية
        $Lesson->update([
            "grade_id" => $request->grade_id,
            "lec_id" => $request->lec_id,
            "course_id" => $request->course_id,
            "title" => $request->title,
            "description" => $request->description,
            "duration" => $request->duration,
        ]);

        // تحديث البوستر
        if ($request->hasFile('poster') && $request->file('poster')->isValid()) {
            if ($Lesson->poster) {
                Storage::delete($Lesson->poster); // حذف البوستر القديم
            }
            $posterPath = $request->file('poster')->store(Lesson::storageFolder);
            $Lesson->poster = $posterPath;
        }

        // تحديث الفيديو
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoFile = $request->file('video');

            // جلب بيانات BunnyCDN
            $libraryId = config('services.streambunny.library_id');
            $apiKey = config('services.streambunny.api_key');
            $zone = config('services.streambunny.zone');
            $client = new GuzzleClient();

            // 1. حذف الفيديو القديم إذا كان موجودًا
            if (!empty($Lesson->video)) {
                preg_match('/\/(\w+)\/play_480p\.mp4$/', $Lesson->video, $matches);
                if (isset($matches[1])) {
                    $oldVideoId = $matches[1];
                    $deleteUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos/{$oldVideoId}";

                    try {
                        $client->delete($deleteUrl, [
                            'headers' => [
                                'AccessKey' => $apiKey,
                                'Accept' => 'application/json',
                            ],
                        ]);
                    } catch (\Exception $e) {
                        Log::error('فشل حذف الفيديو القديم من BunnyCDN: ' . $e->getMessage());
                        return response()->json(['error' => 'فشل حذف الفيديو القديم.'], 500);
                    }
                }
            }

            // 2. إنشاء فيديو جديد في BunnyCDN
            $createVideoUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos";
            try {
                $createVideoResponse = $client->post($createVideoUrl, [
                    'headers' => [
                        'AccessKey' => $apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => ['title' => $Lesson->title],
                ]);

                $videoData = json_decode($createVideoResponse->getBody(), true);
                $newVideoId = $videoData['guid'];
            } catch (\Exception $e) {
                Log::error('فشل إنشاء الفيديو في BunnyCDN: ' . $e->getMessage());
                return response()->json(['error' => 'فشل إنشاء الفيديو الجديد في BunnyCDN.'], 500);
            }

            // 3. رفع الفيديو الجديد باستخدام GuzzleClient
            $uploadUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos/{$newVideoId}";

            try {
                $uploadResponse = $client->put($uploadUrl, [
                    'headers' => [
                        'AccessKey' => $apiKey,
                        'Content-Type' => 'application/octet-stream',
                    ],
                    'body' => fopen($videoFile->getRealPath(), 'r'),
                ]);

                if ($uploadResponse->getStatusCode() !== 200) {
                    throw new \Exception('خطأ أثناء رفع الفيديو.');
                }
            } catch (\Exception $e) {
                Log::error('فشل رفع الفيديو إلى BunnyCDN: ' . $e->getMessage());
                return response()->json(['error' => 'فشل رفع الفيديو إلى BunnyCDN.'], 500);
            }

            // 4. تحديث رابط الفيديو في قاعدة البيانات
            $Lesson->video = "https://{$zone}.b-cdn.net/{$newVideoId}/play_480p.mp4";
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

        // حفظ التحديثات
        $Lesson->save();

        // تحديث عدد الدروس في الكورس
        $course = $Lesson->course;
        $course->numOfLessons = $course->lessons()->count();
        $course->save();

        // الاستجابة بالنجاح
        return response()->json([
            'data' => new LessonResource($Lesson),
            'message' => "Lesson updated successfully."
        ]);

    } catch (\Exception $e) {
        // تسجيل الخطأ في اللوج
        Log::error($e->getMessage());

        return response()->json([
            'error' => 'An error occurred while updating the lesson.',
            'details' => $e->getMessage()
        ], 500);
    }
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
