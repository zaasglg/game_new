<?php
session_start();

// Подключаем систему chicken-road
require_once '../../chicken-road/init.php';

// Очистка буфера вывода
if (ob_get_length()) ob_clean();

header('Content-Type: application/json');

// Проверка аутентификации для реальных пользователей (пропускаем для hack bot)
if (!isset($_SESSION['AUTH']) && !isset($_POST['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Только для авторизованных пользователей с реальной валютой'
    ]);
    exit();
}

// Получаем user_id из AUTH сессии или из параметров запроса
$userHostId = $_SESSION['AUTH'] ?? null;
$requestUserId = $_POST['user_id'] ?? null;

// Для hack bot используем user_id из запроса, иначе из сессии
$finalUserId = $requestUserId ? intval($requestUserId) : $userHostId;

try {
    // Подключаемся к основной базе данных
    include '../../db.php';
    
    if (!isset($conn)) {
        die(json_encode([
            "success" => false,
            "message" => "Ошибка подключения к базе данных"
        ], JSON_UNESCAPED_UNICODE));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] == 'get_chicken_coefficient') {
            // Получаем коэффициент ловушки по host_id
            $query = $conn->prepare("SELECT chicken_trap_coefficient FROM users WHERE user_id = :user_id");
            $query->bindParam(':user_id', $finalUserId, PDO::PARAM_INT);
            $query->execute();
            
            $coefficient = $query->fetchColumn();
            
            if ($coefficient !== false) {
                echo json_encode([
                    'success' => true,
                    'coefficient' => floatval($coefficient),
                    'user_id' => $finalUserId
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ]);
            }
        } 
        elseif ($_POST['action'] == 'update_chicken_coefficient') {
            // Обновляем коэффициент ловушки только для реальных пользователей
            $coefficient = isset($_POST['coefficient']) ? floatval($_POST['coefficient']) : 0.00;
            
            $stmt = $conn->prepare("UPDATE users SET chicken_trap_coefficient = :coefficient WHERE user_id = :user_id");
            $stmt->bindParam(':coefficient', $coefficient);
            $stmt->bindParam(':user_id', $finalUserId);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Коэффициент ловушки сохранен для реального пользователя',
                'coefficient' => $coefficient,
                'user_id' => $finalUserId
            ]);
        }
        else {
            echo json_encode([
                'success' => false,
                'message' => 'Неверное действие'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка базы данных: ' . $e->getMessage()
        ]);
    }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Неверный метод запроса'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Общая ошибка: ' . $e->getMessage()
    ]);
}

exit();
?>
