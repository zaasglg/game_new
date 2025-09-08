<?php
// Прямой тест API без HTML
ob_start();
require_once 'init.php';
ob_end_clean();

header('Content-Type: application/json');

try {
    $users = Users::getInstance();
    $result = $users->save_game_result([
        'user_id' => 12770156,
        'balance' => 1870.97,
        'bet_amount' => 150,
        'win_amount' => 0,
        'game_result' => 'lose'
    ]);
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => 1, 'msg' => $e->getMessage()]);
}
?>