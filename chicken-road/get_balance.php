<?php
header('Content-Type: application/json');
session_start();

// Минимальная проверка авторизации без вывода HTML
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

require_once '../db.php';
require_once 'currency.php';

try {
    $stmt = $conn->prepare("SELECT email, deposit, country, bonificaciones FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Устанавливаем курс валюты для пользователя
        $_SESSION['CHICKEN_USER_RATE'] = getCurrencyRate($user['country']);

        // Конвертируем баланс в USD для отображения в игре
        $balance_usd = convertToUSD($user['deposit'], $user['country']);

        $response = [
            'email' => $user['email'],
            'deposit' => number_format($balance_usd, 2, '.', ''), // Баланс в USD
            'deposit_original' => $user['deposit'], // Оригинальный баланс в национальной валюте
            'country' => $user['country'],
            'bonificaciones' => $user['bonificaciones'],
            'currency_rate' => $_SESSION['CHICKEN_USER_RATE'],
            'currency' => 'USD' // Всегда показываем USD в игре
        ];

        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>