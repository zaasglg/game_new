<?php
require_once 'db.php';

// Режим разработки - устанавливаем в true для тестирования
$developmentMode = true;

try {
    // Получаем временные метки в зависимости от режима
    $timeAgo = $developmentMode ? '-1 minutes' : '-20 minutes';
    $timeLimit = date('Y-m-d H:i:s', strtotime($timeAgo));
    
    echo "Mode: " . ($developmentMode ? "Development (1 min)" : "Production (20 min)") . "\n";
    echo "Checking payments older than: $timeLimit\n\n";

    // Находим все платежи со статусом 'esperando' старше установленного времени
    $query = "SELECT hp.*, u.stage 
              FROM historial_pagos hp
              JOIN users u ON hp.user_id = u.user_id
              WHERE hp.estado = 'esperando' 
              AND hp.transacciones_data <= :timeLimit";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':timeLimit', $timeLimit);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($payments)) {
        echo "No expired payments found.\n";
    } else {
        echo "Found " . count($payments) . " expired payments to process.\n\n";
    }

    foreach ($payments as $payment) {
        $userId = $payment['user_id'];
        $amount = $payment['transacciones_monto'];
        $paymentId = $payment['id'];
        $userStage = $payment['stage'];
        
        echo "Processing payment ID: $paymentId, User: $userId, Amount: $amount, Stage: $userStage\n";
        
        // Начинаем транзакцию
        $conn->beginTransaction();
        
        try {
            // 1. Обновляем статус платежа на 'declined'
            $updatePayment = "UPDATE historial_pagos SET estado = 'declined' WHERE id = :paymentId";
            $stmtUpdate = $conn->prepare($updatePayment);
            $stmtUpdate->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // 2. Возвращаем деньги пользователю
            $updateUser = "UPDATE users SET deposit = deposit + :amount WHERE user_id = :userId";
            $stmtUser = $conn->prepare($updateUser);
            $stmtUser->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmtUser->bindParam(':userId', $userId, PDO::PARAM_STR);
            $stmtUser->execute();
            
            // 3. Если пользователь в стадии верификации, сбрасываем её
            if ($userStage === 'verif' || $userStage === 'verif2') {
                $updateStage = "UPDATE users SET stage = 'normal', stage_balance = 0, verification_start_date = NULL WHERE user_id = :userId";
                $stmtStage = $conn->prepare($updateStage);
                $stmtStage->bindParam(':userId', $userId, PDO::PARAM_STR);
                $stmtStage->execute();
                $message = "✓ Payment ID {$paymentId} declined, {$amount} returned to user {$userId}, stage reset from {$userStage} to normal";
            } else {
                $message = "✓ Payment ID {$paymentId} declined, {$amount} returned to user {$userId}";
            }
            
            // Фиксируем транзакцию
            $conn->commit();
            
            echo $message . "\n\n";
        } catch (PDOException $e) {
            // Откатываем транзакцию в случае ошибки
            $conn->rollBack();
            echo "✗ Error processing payment ID {$paymentId}: " . $e->getMessage() . "\n\n";
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

$conn = null;
?>