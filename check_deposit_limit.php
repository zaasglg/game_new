<?php
header('Content-Type: application/json');
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit();
}

require 'db.php'; // Подключаем файл с подключением к БД

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT deposit, country FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Пользователь не найден']);
        exit();
    }

    $country = $user['country'];
    $deposit = $user['deposit'];

    // Загружаем лимиты из JSON-файла
    $limitsFile = __DIR__ . '/limited.json';
    if (!file_exists($limitsFile)) {
        echo json_encode(['error' => 'Файл с лимитами не найден']);
        exit();
    }

    $limitsJson = file_get_contents($limitsFile);
    $limitsData = json_decode($limitsJson, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Ошибка при чтении лимитов']);
        exit();
    }

    // Проверяем лимит для страны
    $response = [
        'country' => $country,
        'deposit' => $deposit,
        'limit' => $limitsData['limitedByCountry'][$country] ?? null,
        'status' => 'ok', // может быть 'ok', 'limit_reached', 'exceeded', 'forbidden'
        'message' => ''
    ];

    if (!isset($limitsData['limitedByCountry'][$country])) {
        $response['status'] = 'no_limit';
        $response['message'] = "Для страны {$country} не установлен лимит депозита";
    } 
    elseif ($limitsData['limitedByCountry'][$country] === 0) {
        $response['status'] = 'forbidden';
        $response['message'] = "Депозиты для страны {$country} запрещены";
    } 
    elseif ($deposit > $limitsData['limitedByCountry'][$country]) {
        $response['status'] = 'exceeded';
        $response['message'] = "Депозит превышает лимит для страны {$country}";
    }
    elseif ($deposit == $limitsData['limitedByCountry'][$country]) {
        $response['status'] = 'limit_reached';
        $response['message'] = "Депозит достиг лимита для страны {$country}";
    }

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных']);
}
?>