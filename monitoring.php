<?php
// monitoring.php

require_once 'config.php';

// مشاهده لاگ‌های اخیر
$logContent = file_exists(LOG_FILE) ? file_get_contents(LOG_FILE) : 'فایل لاگ یافت نشد';
$logLines = explode("\n", $logContent);
$recentLogs = array_slice($logLines, -50); // 50 خط آخر

?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مانیتورینگ ربات</title>
    <style>
        body { font-family: Tahoma; padding: 20px; background: #f5f5f5; }
        .log-entry { background: white; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>📊 مانیتورینگ ربات تلگرام</h1>
    <h2>لاگ‌های اخیر:</h2>
    <?php foreach (array_reverse($recentLogs) as $log): ?>
        <?php if (!empty(trim($log))): ?>
            <div class="log-entry"><?php echo htmlspecialchars($log); ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>
