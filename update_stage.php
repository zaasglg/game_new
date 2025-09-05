<?php
session_start();
require 'db.php';
require_once 'stage_balance_updater.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

 $id = $_POST['id'] ?? null;
$stage = $_POST['stage'] ?? null;

if (!$id || !$stage) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

try {
    // Получаем текущий stage и страну пользователя
    $stmt = $conn->prepare("SELECT stage, country FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit();
    }
    
    // Если stage уже установлен в нужное значение, ничего не делаем
    if ($user['stage'] === $stage) {
        echo json_encode(['success' => true]);
        exit();
    }
    
    // Подготавливаем запрос в зависимости от нового stage
    if ($stage === 'verif') {
        // Определяем stage_balance для страны пользователя
        $country = $user['country'];
        $stage_balance = 0;
        
        // Значения stage_balance для разных стран
        $country_balances = [
            'Argentina' => 100000,    // Аргентина
            'Paraguay' => 600000,     // Парагвай
            'Costa Rica' => 50000,    // Коста-Рика
            'Ecuador' => 100,         // Эквадор
            'Colombia' => 300000,     // Колумбия
            'Peru' => 400,
            'Venezuela' => 8000,
            'Bolivia' => 1200,
            // Значение по умолчанию для других стран
            'default' => 100
        ];
        
        $stage_balance = $country_balances[$country] ?? $country_balances['default'];
        
        // При переходе в verif устанавливаем текущую дату/время и stage_balance
        $stmt = $conn->prepare("
            UPDATE users 
            SET stage = :stage, 
                verification_start_date = NOW(),
                stage_balance = :stage_balance
            WHERE id = :id
        ");
        $stmt->execute([
            ':stage' => $stage, 
            ':stage_balance' => $stage_balance,
            ':id' => $id
        ]);
        
    } else {
        // Для других stage просто обновляем значение
        $stmt = $conn->prepare("
            UPDATE users 
            SET stage = :stage 
            WHERE user_id = :user_id
        ");
        $stmt->execute([':stage' => $stage, ':user_id' => $user_id]);
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}