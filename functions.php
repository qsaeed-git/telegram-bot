<?php
// functions.php

require_once 'config.php';

// ارسال درخواست به API تلگرام
function sendRequest($method, $data = []) {
    $url = API_URL . $method;
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        logMessage("خطا در ارسال درخواست به: $method");
        return false;
    }
    
    return json_decode($result, true);
}

// ارسال پیام متنی
function sendMessage($chatId, $text, $replyMarkup = null, $parseMode = 'HTML') {
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => $parseMode
    ];
    
    if ($replyMarkup) {
        $data['reply_markup'] = json_encode($replyMarkup);
    }
    
    return sendRequest('sendMessage', $data);
}

// فوروارد پیام به کانال ذخیره‌سازی
function forwardToStorage($chatId, $messageId) {
    $data = [
        'chat_id' => STORAGE_CHANNEL,
        'from_chat_id' => $chatId,
        'message_id' => $messageId
    ];
    
    return sendRequest('forwardMessage', $data);
}

// دریافت اطلاعات فایل
function getFileInfo($fileId) {
    $data = ['file_id' => $fileId];
    return sendRequest('getFile', $data);
}

// دریافت URL دانلود فایل
function getFileUrl($filePath) {
    return 'https://api.telegram.org/file/bot' . BOT_TOKEN . '/' . $filePath;
}

// ثبت لاگ
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

// ایجاد دکمه‌های شیشه‌ای
function createInlineKeyboard($buttons) {
    return ['inline_keyboard' => $buttons];
}

// تبدیل حجم فایل به فرمت خوانا
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' Bytes';
    }
}

// ذخیره اطلاعات فایل در دیتابیس (اختیاری)
function saveFileToDatabase($fileId, $fileName, $fileSize, $userId, $storageMessageId) {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            logMessage("خطای اتصال به دیتابیس: " . $conn->connect_error);
            return false;
        }
        
        $conn->set_charset("utf8mb4");
        
        $stmt = $conn->prepare("INSERT INTO files (file_id, file_name, file_size, user_id, storage_message_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssiii", $fileId, $fileName, $fileSize, $userId, $storageMessageId);
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        return $result;
    } catch (Exception $e) {
        logMessage("خطا در ذخیره فایل: " . $e->getMessage());
        return false;
    }
}
?>
