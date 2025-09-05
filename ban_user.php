<?php
session_start();
require 'db.php';

if (isset($_POST['ban_user']) && $_POST['ban_user'] === 'true') {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode(['success' => false]);