<?php
// webhook.php

require_once 'config.php';
require_once 'functions.php';

// دریافت داده‌های ورودی
$content = file_get_contents("php://input");
$update = json_decode($content, TRUE);

// ثبت لاگ درخواست
logMessage("درخواست دریافت شد: " . $content);

// بررسی وجود پیام
if (!isset($update['message'])) {
    exit;
}

$message = $update['message'];
$chatId = $message['chat']['id'];
$userId = $message['from']['id'];
$messageId = $message['message_id'];
$username = $message['from']['username'] ?? 'بدون نام کاربری';

// پردازش دستورات متنی
if (isset($message['text'])) {
    $text = $message['text'];
    
    switch ($text) {
        case '/start':
            $welcomeText = "🤖 <b>به ربات تولید لینک مستقیم دانلود خوش آمدید!</b>\n\n";
            $welcomeText .= "📁 فایل، عکس، ویدیو یا هر نوع محتوایی را برای من ارسال کنید.\n";
            $welcomeText .= "🔗 من فوراً لینک مستقیم دانلود آن را برایتان ایجاد می‌کنم.\n\n";
            $welcomeText .= "⚠️ محدودیت حجم: 20 مگابایت\n";
            $welcomeText .= "⏱ اعتبار لینک: 1 ساعت\n\n";
            $welcomeText .= "💡 برای شروع، فایل خود را ارسال کنید.";
            
            sendMessage($chatId, $welcomeText);
            break;
            
        case '/help':
            $helpText = "📖 <b>راهنمای استفاده:</b>\n\n";
            $helpText .= "1️⃣ هر فایلی را به ربات ارسال کنید\n";
            $helpText .= "2️⃣ لینک مستقیم دانلود را دریافت کنید\n";
            $helpText .= "3️⃣ از لینک در دانلود منیجرها استفاده کنید\n\n";
            $helpText .= "<b>دستورات:</b>\n";
            $helpText .= "/start - شروع مجدد\n";
            $helpText .= "/help - راهنما\n";
            $helpText .= "/stats - آمار (فقط ادمین)";
            
            sendMessage($chatId, $helpText);
            break;
            
        case '/stats':
            if ($userId == ADMIN_ID) {
                // نمایش آمار (نیاز به دیتابیس)
                $statsText = "📊 <b>آمار ربات:</b>\n\n";
                $statsText .= "تعداد فایل‌های پردازش شده: N/A\n";
                $statsText .= "تعداد کاربران: N/A\n";
                sendMessage($chatId, $statsText);
            }
            break;
            
        default:
            sendMessage($chatId, "متوجه نشدم! لطفاً فایل خود را ارسال کنید یا از /help استفاده کنید.");
    }
    exit;
}

// پردازش فایل‌های مختلف
$fileId = null;
$fileName = null;
$fileSize = 0;
$fileType = 'نامشخص';

if (isset($message['document'])) {
    // فایل معمولی
    $fileId = $message['document']['file_id'];
    $fileName = $message['document']['file_name'] ?? 'file';
    $fileSize = $message['document']['file_size'] ?? 0;
    $fileType = 'سند';
    
} elseif (isset($message['photo'])) {
    // عکس (بزرگترین سایز را انتخاب می‌کنیم)
    $photos = $message['photo'];
    $largestPhoto = end($photos);
    $fileId = $largestPhoto['file_id'];
    $fileName = 'photo_' . time() . '.jpg';
    $fileSize = $largestPhoto['file_size'] ?? 0;
    $fileType = 'عکس';
    
} elseif (isset($message['video'])) {
    // ویدیو
    $fileId = $message['video']['file_id'];
    $fileName = $message['video']['file_name'] ?? 'video_' . time() . '.mp4';
    $fileSize = $message['video']['file_size'] ?? 0;
    $fileType = 'ویدیو';
    
} elseif (isset($message['audio'])) {
    // صوت
    $fileId = $message['audio']['file_id'];
    $fileName = $message['audio']['file_name'] ?? 'audio_' . time() . '.mp3';
    $fileSize = $message['audio']['file_size'] ?? 0;
    $fileType = 'صوت';
    
} elseif (isset($message['voice'])) {
    // ویس
    $fileId = $message['voice']['file_id'];
    $fileName = 'voice_' . time() . '.ogg';
    $fileSize = $message['voice']['file_size'] ?? 0;
    $fileType = 'پیام صوتی';
    
} elseif (isset($message['video_note'])) {
    // ویدیو پیام
    $fileId = $message['video_note']['file_id'];
    $fileName = 'video_note_' . time() . '.mp4';
    $fileSize = $message['video_note']['file_size'] ?? 0;
    $fileType = 'ویدیو پیام';
    
} elseif (isset($message['sticker'])) {
    // استیکر
    sendMessage($chatId, "❌ استیکرها پشتیبانی نمی‌شوند!");
    exit;
}

// اگر فایلی پیدا نشد
if (!$fileId) {
    sendMessage($chatId, "⚠️ لطفاً یک فایل معتبر ارسال کنید.");
    exit;
}

// بررسی محدودیت حجم
if ($fileSize > MAX_FILE_SIZE) {
    $maxSize = formatFileSize(MAX_FILE_SIZE);
    sendMessage($chatId, "❌ حجم فایل بیش از حد مجاز است!\n\nحداکثر مجاز: $maxSize");
    exit;
}

// ارسال پیام در حال پردازش
sendMessage($chatId, "⏳ در حال پردازش فایل...");

// فوروارد فایل به کانال ذخیره‌سازی
$forwardResult = forwardToStorage($chatId, $messageId);

if (!$forwardResult || !isset($forwardResult['result']['message_id'])) {
    sendMessage($chatId, "❌ خطا در ذخیره‌سازی فایل. لطفاً دوباره تلاش کنید.");
    logMessage("خطا در فوروارد پیام برای کاربر $userId");
    exit;
}

$storageMessageId = $forwardResult['result']['message_id'];

// ایجاد لینک دانلود
$downloadLink = DOWNLOAD_URL . '?file=' . urlencode($fileId) . '&name=' . urlencode($fileName);

// ذخیره در دیتابیس (اختیاری)
saveFileToDatabase($fileId, $fileName, $fileSize, $userId, $storageMessageId);

// آماده‌سازی پیام نهایی
$responseText = "✅ <b>لینک دانلود آماده شد!</b>\n\n";
$responseText .= "📄 <b>نام فایل:</b> " . htmlspecialchars($fileName) . "\n";
$responseText .= "📦 <b>نوع:</b> $fileType\n";
$responseText .= "💾 <b>حجم:</b> " . formatFileSize($fileSize) . "\n\n";
$responseText .= "🔗 <b>لینک مستقیم:</b>\n";
$responseText .= "<code>$downloadLink</code>\n\n";
$responseText .= "⚠️ این لینک برای 1 ساعت معتبر است.";

// ایجاد دکمه دانلود
$keyboard = createInlineKeyboard([
    [
        ['text' => '⬇️ دانلود فایل', 'url' => $downloadLink]
    ]
]);

sendMessage($chatId, $responseText, $keyboard);

logMessage("فایل '$fileName' برای کاربر $userId پردازش شد");
?>
