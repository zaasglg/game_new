<?php
require_once 'db.php';

header('Content-Type: application/json');

// Получаем данные из POST
$userId = $_POST['user_id'] ?? null;
$action = $_POST['action'] ?? 'block';

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'User ID is required']);
    exit;
}

// Настройки Telegram
$botToken = '8076543915:AwewewerwAHb5upyRzmAL5kEeE833wKg4HLFNouROzc';
$chatId = '-1002585erwerwer150746';

// Формируем сообщение
$message = "🚨 <b>User Blocked</b>\n";
$message .= "🆔 <b>User ID:</b> $userId\n";
$message .= "🛑 <b>Action:</b> $action\n";
$message .= "⏰ <b>Time:</b> " . date('d.m.Y H:i:s');

// Отправляем сообщение в Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send Telegram notification']);
    exit;
}

echo json_encode(['success' => true, 'response' => json_decode($result, true)]);
?>