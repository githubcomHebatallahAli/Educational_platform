<?php

require 'vendor/autoload.php';

use TusPhp\Tus\Client;

$client = new Client('https://video.bunnycdn.com/tusupload');
echo "Client initialized successfully!\n";

// تحديد الملف الذي تريد رفعه
$filePath = __DIR__ . '/test-file.txt'; // قم بإنشاء ملف نصي بسيط في نفس المجلد
file_put_contents($filePath, 'This is a test file.');

// تعيين الملف للعميل
$client->setKey('unique-file-key'); // يمكنك تغيير هذا المفتاح
$client->file($filePath, 'test-file.txt');

// بدء عملية الرفع
$client->upload();

echo "File uploaded successfully!\n";
