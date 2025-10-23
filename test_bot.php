<?php
// test_bot.php

require_once 'config.php';
require_once 'functions.php';

echo "<h1>تست ربات تلگرام</h1>";

// تست 1: دریافت اطلاعات ربات
echo "<h2>1. اطلاعات ربات:</h2>";
$me = sendRequest('getMe');
echo "<pre>" . print_r($me, true) . "</pre>";

// تست 2: وضعیت Webhook
echo "<h2>2. وضعیت Webhook:</h2>";
$webhook = sendRequest('getWebhookInfo');
echo "<pre>" . print_r($webhook, true) . "</pre>";

// تست 3: ارسال پیام تست به ادمین
echo "<h2>3. ارسال پیام تست:</h2>";
$testMessage = sendMessage(ADMIN_ID, "✅ ربات به درستی کار می‌کند!\n\nزمان: " . date('Y-m-d H:i:s'));
echo "<pre>" . print_r($testMessage, true) . "</pre>";

// تست 4: بررسی دسترسی به لاگ
echo "<h2>4. وضعیت فایل لاگ:</h2>";
$logDir = dirname(LOG_FILE);
echo "مسیر لاگ: " . LOG_FILE . "<br>";
echo "قابل نوشتن: " . (is_writable($logDir) ? '✅ بله' : '❌ خیر') . "<br>";

// تست 5: بررسی اتصال دیتابیس
echo "<h2>5. اتصال دیتابیس:</h2>";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "❌ خطا: " . $conn->connect_error;
    } else {
        echo "✅ اتصال موفق";
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ خطا: " . $e->getMessage();
}
?>
