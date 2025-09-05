<?php
session_start();
require_once 'init.php';

header('Content-Type: application/json');

try {
    $dbo = DBO::getInstance();
    
    // Получаем параметры поиска
    $user = isset($_GET['user']) ? $_GET['user'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'desc';
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    
    // Базовый запрос
    $where = '';
    $params = [];
    
    if ($user) {
        $where = "WHERE user = ?";
        $params[] = $user;
    }
    
    // Получаем ставки
    $bets = $dbo->query("SELECT b.*, u.name, u.img 
                         FROM bets b 
                         LEFT JOIN users u ON b.user = u.uid 
                         $where 
                         ORDER BY $sort $dir 
                         LIMIT $length", 
                         $params)->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'bets' => $bets,
        'status' => 'success'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
