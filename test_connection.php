<?php
echo "Trying connection...<br>";
$test = @file_get_contents("https://api.telegram.org");
if ($test === false) {
    echo "❌ سرور شما به api.telegram.org دسترسی ندارد.";
} else {
    echo "✅ ارتباط با api.telegram.org برقرار است.";
}
?>
