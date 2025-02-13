<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class TestBunnyController extends Controller
{
    public function uploadVideoToBunnyCDN(Request $request)
    {
        try {
            // تأكد من وجود ملف الفيديو
            if (!$request->hasFile('video')) { // تم تصحيح الخطأ هنا
                return response()->json(['error' => 'لم يتم تقديم ملف فيديو.']);
            }

            $videoFile = $request->file('video');

            // إعداد بيانات BunnyCDN
            $libraryId = config('services.streambunny.library_id'); // تأكد من وجود هذه القيمة في ملف .env
            $apiKey = config('services.streambunny.api_key'); // تأكد من وجود هذه القيمة في ملف .env

            // إنشاء فيديو جديد في BunnyCDN
            $createVideoUrl = "https://video.bunnycdn.com/library/{$libraryId}/videos";
            $createVideoHeaders = [
                'AccessKey' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $client = new GuzzleClient();

            // إنشاء فيديو جديد
            $createVideoResponse = $client->post($createVideoUrl, [
                'headers' => $createVideoHeaders,
                'json' => [
                    'title' => 'Test Video', // يمكنك تغيير العنوان حسب الحاجة
                ],
            ]);

            if ($createVideoResponse->getStatusCode() === 200) {
                $videoData = json_decode($createVideoResponse->getBody(), true);
                $videoId = $videoData['guid']; // الحصول على VideoId

                // إعداد بيانات رفع الفيديو
                $expirationTime = time() + 3600; // صلاحية التوقيع (1 ساعة)
                $signature = hash('sha256', $libraryId . $apiKey . $expirationTime . $videoId);

                $uploadUrl = "https://video.bunnycdn.com/tusupload";
                $uploadHeaders = [
                    'AuthorizationSignature' => $signature,
                    'AuthorizationExpire' => $expirationTime,
                    'VideoId' => $videoId,
                    'LibraryId' => $libraryId,
                    'Content-Type' => 'application/offset+octet-stream',
                ];

                // محاولة رفع الفيديو
                $uploadResponse = $client->post($uploadUrl, [
                    'headers' => $uploadHeaders,
                    'body' => fopen($videoFile->getRealPath(), 'r'),
                ]);

                if ($uploadResponse->getStatusCode() === 201) {
                    return response()->json([
                        'message' => 'تم رفع الفيديو بنجاح.',
                        'videoId' => $videoId,
                    ]);
                } else {
                    return response()->json(['error' => 'فشل رفع الفيديو إلى BunnyCDN.'], 500);
                }
            } else {
                return response()->json(['error' => 'فشل إنشاء الفيديو في BunnyCDN.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('BunnyCDN Upload Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء رفع الفيديو.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
