<?php
// Проверка cURL
if (!function_exists('curl_init')) {
    echo "❌ cURL не установлен";
    exit;
}

$botToken = '8076543915:AAHb5upyRzmAL5kEeE833wKg4HLFNouROzc';
$chatId = '-1002585150746';
$message = "🧪 Тест регистрации: " . date('Y-m-d H:i:s');

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: $error\n";
echo "Response: $response\n";

if ($httpCode == 200) {
    echo "✅ cURL работает!";
} else {
    echo "❌ Ошибка cURL";
}
?>