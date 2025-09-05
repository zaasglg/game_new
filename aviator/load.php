<?php
session_start();
require_once 'init.php';

header('Content-Type: application/json');

try {
    // Получаем данные текущей игры
    $dbo = DBO::getInstance();
    
    // Получаем последнюю игру
    $game = $dbo->query("SELECT * FROM games ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if (!$game) {
        echo json_encode(['error' => 'No games found']);
        exit;
    }
    
    // Получаем ставки для текущей игры
    $bets = $dbo->query("SELECT b.*, u.name, u.img 
                         FROM bets b 
                         LEFT JOIN users u ON b.user = u.uid 
                         WHERE b.game = ? 
                         ORDER BY b.id DESC", 
                         [$game['id']])->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'game' => $game,
        'bets' => $bets,
        'status' => 'success'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
