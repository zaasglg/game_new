<?php
include_once 'init.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Получаем user_id из параметров
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
    
    if ($user_id) {
        // Получаем реальный баланс из основной базы данных
        $Q = "SELECT deposit FROM `users` WHERE `user_id`='". (int)$user_id ."'";
        $main_user = DB2::GI()->get($Q);
        
        if ($main_user && isset($main_user['deposit'])) {
            $real_balance = (float)$main_user['deposit'];
            echo json_encode(['success' => true, 'balance' => $real_balance]);
        } else {
            echo json_encode(['success' => false, 'balance' => 500.00, 'message' => 'User not found, using demo balance']);
        }
    } else {
        // Нет user_id - возвращаем демо баланс
        echo json_encode(['success' => false, 'balance' => 500.00, 'message' => 'No user_id provided, using demo balance']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'balance' => 500.00, 'error' => $e->getMessage()]);
}
?>
