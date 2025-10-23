<?php
// get_channel_id.php

require_once 'config.php';
require_once 'functions.php';

// ابتدا یک پیام در کانال ارسال کنید، سپس این فایل را اجرا کنید
$result = sendRequest('getUpdates');

echo "<pre>";
print_r($result);
echo "</pre>";
?>
