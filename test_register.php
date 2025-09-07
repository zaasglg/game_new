<?php
// Простой тест регистрации
error_log("TEST: Registration script started");

if ($_POST) {
    error_log("TEST: POST data received: " . print_r($_POST, true));
    
    $email = $_POST['email'] ?? 'test@test.com';
    $country = $_POST['country'] ?? 'Ecuador';
    $user_id = rand(100000, 999999);
    
    // Тест отправки в Telegram
    $botToken = '8076543915:AAHb5upyRzmAL5kEeE833wKg4HLFNouROzc';
    $chatId = '-1002585150746';
    $message = "🧪 ТЕСТ Рег: $user_id\n🇪🇨 Страна: $country\n👤 Email: $email";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("TEST: Telegram HTTP: $httpCode, Response: $response");
    
    echo json_encode(["success" => true, "message" => "Тест успешен!"]);
} else {
    echo "Отправьте POST запрос для теста";
}
?>