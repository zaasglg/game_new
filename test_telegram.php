<?php
// Тест отправки в Telegram
$botToken = '8076543915:AAHb5upyRzmAL5kEeE833wKg4HLFNouROzc';
$chatId = '-1002585150746';
$message = "🧪 Тест: Регистрация работает!\n🇪🇨 Страна: Ecuador\n👤 Реф: test123";

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode == 200) {
    echo "✅ Telegram отправка работает!";
} else {
    echo "❌ Ошибка отправки в Telegram";
}
?>