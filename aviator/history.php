<?php
session_start();
require_once 'init.php';

header('Content-Type: application/json');

try {
    $dbo = DBO::getInstance();
    
    // Получаем историю последних игр
    $history = $dbo->query("SELECT * FROM history ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'history' => $history,
        'status' => 'success'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
