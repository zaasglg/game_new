<?php
require_once 'db.php';

try {
    // Получаем временные метки
    $twentyMinutesAgo = date('Y-m-d H:i:s', strtotime('-20 minutes'));
    $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));

    // Находим все платежи со статусом 'esperando'
    $query = "SELECT hp.*, u.stage 
              FROM historial_pagos hp
              JOIN users u ON hp.id = u.id
              WHERE hp.estado = 'esperando' 
              AND (
                  (u.stage = 'meet' AND hp.transacciones_data <= :fiveMinutesAgo)
                  OR 
                  (u.stage != 'meet' AND hp.transacciones_data <= :twentyMinutesAgo)
              )";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':fiveMinutesAgo', $fiveMinutesAgo);
    $stmt->bindParam(':twentyMinutesAgo', $twentyMinutesAgo);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($payments as $payment) {
        $userId = $payment['user_id'];
        $amount = $payment['transacciones_monto'];
        $paymentId = $payment['id'];
        $userStage = $payment['stage'];
        
        // Начинаем транзакцию
        $conn->beginTransaction();
        
        try {
            if ($userStage === 'meet') {
                // Для пользователей с stage 'meet' - подтверждаем платеж
                $updatePayment = "UPDATE historial_pagos SET estado = 'completed' WHERE id = :paymentId";
                $action = 'completed';
                $message = "Payment ID {$paymentId} completed for meet user {$userId}";
            } else {
                // Для остальных - отклоняем и возвращаем средства
                $updatePayment = "UPDATE historial_pagos SET estado = 'declined' WHERE id = :paymentId";
                $action = 'declined';
                $message = "Payment ID {$paymentId} declined and amount {$amount} returned to user {$userId}";
                
                $updateUser = "UPDATE users SET deposit = deposit + :amount WHERE id = :userId";
                $stmtUser = $conn->prepare($updateUser);
                $stmtUser->bindParam(':amount', $amount, PDO::PARAM_STR);
                $stmtUser->bindParam(':userId', $userId, PDO::PARAM_STR);
                $stmtUser->execute();
            }
            
            // Обновляем статус платежа
            $stmtUpdate = $conn->prepare($updatePayment);
            $stmtUpdate->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // Фиксируем транзакцию
            $conn->commit();
            
            echo $message . "\n";
        } catch (PDOException $e) {
            // Откатываем транзакцию в случае ошибки
            $conn->rollBack();
            echo "Error processing payment ID {$paymentId}: " . $e->getMessage() . "\n";
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

$conn = null;
?>