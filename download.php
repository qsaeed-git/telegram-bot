<?php
// download.php

require_once 'config.php';
require_once 'functions.php';

// دریافت پارامترها
$fileId = $_GET['file'] ?? '';
$fileName = $_GET['name'] ?? 'file';

// اعتبارسنجی
if (empty($fileId)) {
    http_response_code(400);
    die('❌ فایل یافت نشد');
}

// ثبت لاگ درخواست دانلود
logMessage("درخواست دانلود: $fileName (FileID: $fileId)");

// دریافت اطلاعات فایل از تلگرام
$fileInfo = getFileInfo($fileId);

if (!$fileInfo || !isset($fileInfo['result']['file_path'])) {
    http_response_code(404);
    logMessage("خطا در دریافت اطلاعات فایل: $fileId");
    die('❌ فایل یافت نشد یا منقضی شده است');
}

$filePath = $fileInfo['result']['file_path'];
$fileUrl = getFileUrl($filePath);

// تنظیم هدرهای دانلود
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

// پشتیبانی از Resume
if (isset($_SERVER['HTTP_RANGE'])) {
    header('HTTP/1.1 206 Partial Content');
}

// دانلود و ارسال فایل
$fileContent = @file_get_contents($fileUrl);

if ($fileContent === false) {
    http_response_code(500);
    logMessage("خطا در دانلود فایل از تلگرام: $fileUrl");
    die('❌ خطا در دانلود فایل');
}

echo $fileContent;

logMessage("دانلود موفق: $fileName");
?>
