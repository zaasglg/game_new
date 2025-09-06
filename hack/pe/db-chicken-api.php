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
            $isDemo = isset($_POST['is_demo']) ? intval($_POST['is_demo']) : 0;
            
            if ($isDemo == 1 || $finalUserId === 'demo') {
                // Получаем коэффициент для демо режима
                $demoFile = '../../chicken-road/demo_coefficients.json';
                if (file_exists($demoFile)) {
                    $demoData = json_decode(file_get_contents($demoFile), true);
                    $coefficient = $demoData['coefficient'] ?? 2.5; // По умолчанию 2.5
                } else {
                    $coefficient = 2.5; // По умолчанию для демо
                }
                
                echo json_encode([
                    'success' => true,
                    'coefficient' => floatval($coefficient),
                    'mode' => 'demo'
                ]);
            } else {
                // Получаем коэффициент для реального пользователя из volurgame
                $query = $conn->prepare("SELECT chicken_trap_coefficient FROM users WHERE user_id = :user_id");
                $query->bindParam(':user_id', $finalUserId, PDO::PARAM_INT);
                $query->execute();
                
                $coefficient = $query->fetchColumn();
                
                if ($coefficient !== false) {
                    echo json_encode([
                        'success' => true,
                        'coefficient' => floatval($coefficient),
                        'user_id' => $finalUserId,
                        'mode' => 'real',
                        'database' => 'volurgame'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Пользователь не найден в volurgame.users'
                    ]);
                }
            }
        } 
        elseif ($_POST['action'] == 'update_chicken_coefficient') {
            // Обновляем коэффициент ловушки как для реальных, так и для демо пользователей
            $coefficient = isset($_POST['coefficient']) ? floatval($_POST['coefficient']) : 0.00;
            $isDemo = isset($_POST['is_demo']) ? intval($_POST['is_demo']) : 0;
            $isAutoUpdate = isset($_POST['auto_update']) ? true : false;
            $isManualUpdate = isset($_POST['manual_update']) ? true : false;
            $gameStarted = isset($_POST['game_started']) ? true : false;
            $difficultyLevel = isset($_POST['difficulty_level']) ? $_POST['difficulty_level'] : 'unknown';
            $coefficientIndex = isset($_POST['coefficient_index']) ? intval($_POST['coefficient_index']) : null;
            
            $updateType = 'normal';
            if ($isAutoUpdate) $updateType = 'auto';
            if ($isManualUpdate) $updateType = 'manual'; 
            if ($gameStarted) $updateType = 'game_started';
            
            if ($isDemo == 1 || $finalUserId === 'demo') {
                // Сохраняем коэффициент для демо режима в отдельную таблицу или файл
                $demoData = [
                    'demo_id' => 'demo_user',
                    'coefficient' => $coefficient,
                    'difficulty_level' => $difficultyLevel,
                    'coefficient_index' => $coefficientIndex,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'update_type' => $updateType
                ];
                
                // Сохраняем в JSON файл для демо режима
                $demoFile = '../../chicken-road/demo_coefficients.json';
                file_put_contents($demoFile, json_encode($demoData, JSON_PRETTY_PRINT));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Коэффициент ловушки сохранен для демо режима',
                    'coefficient' => $coefficient,
                    'difficulty_level' => $difficultyLevel,
                    'coefficient_index' => $coefficientIndex,
                    'mode' => 'demo',
                    'update_type' => $updateType
                ]);
            } else {
                // Сохраняем для реального пользователя в основной базе volurgame
                $stmt = $conn->prepare("UPDATE users SET chicken_trap_coefficient = :coefficient WHERE user_id = :user_id");
                $stmt->bindParam(':coefficient', $coefficient);
                $stmt->bindParam(':user_id', $finalUserId);
                $result = $stmt->execute();
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Коэффициент ловушки сохранен в volurgame.users',
                        'coefficient' => $coefficient,
                        'difficulty_level' => $difficultyLevel,
                        'coefficient_index' => $coefficientIndex,
                        'user_id' => $finalUserId,
                        'mode' => 'real',
                        'database' => 'volurgame',
                        'update_type' => $updateType
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ошибка сохранения в базу данных volurgame'
                    ]);
                }
            }
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
