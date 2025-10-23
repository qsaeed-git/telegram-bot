<?php
define('BOT_TOKEN','8496951166:AAG-AMH9i5ffzdE1NhIC5NKBPgWI-p7Y4-s');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('WEBHOOK_URL', 'https://sfit.ir/telegram-bot/webhook.php');
define('DOWNLOAD_URL', 'https://sfit.ir/telegram-bot/download.php');
define('STORAGE_CHANNEL', '@qadimisaeed'); 
define('ADMIN_ID', 12345678);
define('MAX_FILE_SIZE', 20*1024*1024); // 20MB
define('LOG_FILE', __DIR__.'/logs/bot.log');
date_default_timezone_set('Asia/Tehran');
error_reporting(E_ALL);
ini_set('display_errors',1);
?>
