<?php
// check_webhook.php

require_once 'config.php';
require_once 'functions.php';

$result = sendRequest('getWebhookInfo');

echo "<h2>وضعیت Webhook:</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>
