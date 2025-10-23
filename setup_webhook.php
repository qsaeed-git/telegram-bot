<?php
// setup_webhook.php

require_once 'config.php';
require_once 'functions.php';

$result = sendRequest('setWebhook', [
    'url' => WEBHOOK_URL,
    'max_connections' => 100,
    'drop_pending_updates' => false
]);

echo "<pre>";
print_r($result);
echo "</pre>";

if ($result['ok']) {
    echo "\n✅ Webhook با موفقیت تنظیم شد!";
} else {
    echo "\n❌ خطا در تنظیم webhook: " . $result['description'];
}
?>
