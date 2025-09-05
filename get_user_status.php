<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit();
}

$userId = $_GET['user_id'];

try {
    $stmt = $conn->prepare("SELECT stage, deposit as balance FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['error' => 'User not found']);
        exit();
    }
    
    echo json_encode([
        'stage' => $user['stage'],
        'balance' => (float)$user['balance']
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>