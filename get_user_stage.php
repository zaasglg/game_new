<?php
session_start();
require 'db.php';

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

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT stage FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode(['success' => true, 'stage' => $result['stage']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}