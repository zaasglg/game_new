<?php
header('Content-Type: application/json');

// Минимальная проверка авторизации без вывода HTML
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

require_once 'db.php';

try {
    $stmt = $conn->prepare("SELECT email, deposit, country, bonificaciones FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
