<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    // Валидация
    if (empty($input['user_id']) || empty($input['amount'])) {
        throw new Exception("Требуется user_id и amount");
    }

   

} catch (Exception $e) {
    
}
?>