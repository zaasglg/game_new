<?php
session_start();
require 'db.php';
require_once 'stage_balance_updater.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$user_id = $_POST['user_id'] ?? null;
$stage = 'verif2'; // Фиксированное значение вместо параметра

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id parameter']);
    exit();
}

try {
    // Получаем текущий stage и страну пользователя
    $stmt = $conn->prepare("SELECT stage, country FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
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
    
    // Определяем stage_balance для страны пользователя
    $country = $user['country'];
    $stage_balance = 0;
    
    // Значения stage_balance для разных стран
    $country_balances = [
        'Argentina' => 120000,
        'Paraguay' => 700000,
        'Costa Rica' => 60000,
        'Ecuador' => 100,
        'Colombia' => 400000,
        'Peru' => 400,
        'Venezuela' => 8000,
        'Bolivia' => 1200,
        'default' => 120
    ];
    
    $stage_balance = $country_balances[$country] ?? $country_balances['default'];
    
    // При переходе в verif2 устанавливаем текущую дату/время и stage_balance
    $stmt = $conn->prepare("
        UPDATE users 
        SET stage = :stage, 
            verification_start_date = NOW(),
            stage_balance = :stage_balance
        WHERE user_id = :user_id
    ");
    $stmt->execute([
        ':stage' => $stage, 
        ':stage_balance' => $stage_balance,
        ':user_id' => $user_id
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}