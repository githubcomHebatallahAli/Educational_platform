<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class TestBunnyController extends Controller
{


public function testBunnyCDNConnection()
{
    try {
        // تحديد القرص (Disk) الذي يستخدم BunnyCDN
        $disk = Storage::disk('bunnycdn');

        // اسم الملف الاختباري
        $testFileName = 'test.txt';

        // محتوى الملف الاختباري
        $testFileContent = 'Hello, BunnyCDN!';

        // محاولة رفع الملف إلى BunnyCDN
        $disk->put($testFileName, $testFileContent);

        // التحقق من وجود الملف بعد الرفع
        if ($disk->exists($testFileName)) {
            Log::info('Successfully connected to BunnyCDN and uploaded a test file.');

            // حذف الملف الاختباري بعد التأكد من نجاح العملية
            $disk->delete($testFileName);

            return response()->json([
                'message' => 'Successfully connected to BunnyCDN and uploaded a test file.'
            ], 200);
        } else {
            Log::error('Failed to upload test file to BunnyCDN.');
            return response()->json([
                'error' => 'Failed to upload test file to BunnyCDN.'
            ], 500);
        }
    } catch (\Exception $e) {
        Log::error('Error uploading file to BunnyCDN: ' . $e->getMessage());
        return response()->json([
            'error' => 'An error occurred while uploading the file.',
            'details' => $e->getMessage()
        ], 500);
    }
}
}
