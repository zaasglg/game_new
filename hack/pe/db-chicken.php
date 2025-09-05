<?php
session_start();
include 'overlaying.php';

// Используем точно такие же настройки как в основном db.php
$host = '127.0.0.1';
$dbname = 'volurgame';
$username = 'root';
$password = 'root';
$port = 8889;
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';

// Очистка буфера вывода
if (ob_get_length()) ob_clean();

header('Content-Type: application/json');

// Проверка аутентификации
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit();
}

try {
    // Пробуем подключиться через TCP/IP (как в основном db.php)
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ];
    
    $conn = new PDO($dsn, $username, $password, $options);
    
} catch (PDOException $e) {
    // Пробуем через socket, если TCP не работает
    try {
        $dsn = "mysql:unix_socket=$socket;dbname=$dbname;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password, $options);
        
    } catch (PDOException $e) {
        // Выводим ошибку только если оба способа не сработали
        die(json_encode([
            "success" => false,
            "error" => $e->getMessage(),
            "solution" => "Database connection failed"
        ], JSON_UNESCAPED_UNICODE));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] == 'get_chicken_prediction') {
            // Используем user_id из сессии для безопасности
            $userId = $_SESSION['user_id'];
            
            $query = $conn->prepare("SELECT positions_chicken, chicken_multiplier FROM users WHERE user_id = :user_id");
            $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $query->execute();
            
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $positions = [];
                if (!empty($user['positions_chicken'])) {
                    $positions = array_filter(explode(',', $user['positions_chicken']), function($v) {
                        return is_numeric($v) && $v >= 1 && $v <= 25;
                    });
                    $positions = array_map('intval', $positions);
                }
                
                echo json_encode([
                    'success' => true,
                    'user_id' => $userId,
                    'safe_positions' => $positions,
                    'predicted_multiplier' => floatval($user['chicken_multiplier']),
                    'message' => 'Chicken Road prediction loaded'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
        } 
        elseif ($_POST['action'] == 'update_chicken_prediction') {
            // Обновление предсказания (для админов или автоматической системы)
            $userId = $_SESSION['user_id'];
            $positions = isset($_POST['positions']) ? $_POST['positions'] : '';
            $multiplier = isset($_POST['multiplier']) ? floatval($_POST['multiplier']) : 2.0;
            
            $stmt = $conn->prepare("UPDATE users SET positions_chicken = :positions, chicken_multiplier = :multiplier WHERE user_id = :user_id");
            $stmt->bindParam(':positions', $positions);
            $stmt->bindParam(':multiplier', $multiplier);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Chicken prediction updated',
                'positions' => $positions,
                'multiplier' => $multiplier
            ]);
        }
        else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

exit();
?>
