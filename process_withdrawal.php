<?php
// process_withdrawal.php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

ob_start();

require_once 'db.php';

if (!$conn) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

if (headers_sent()) {
    error_log("Headers already sent, output started somewhere");
    die(json_encode(['success' => false, 'error' => 'Headers already sent']));
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Получаем основные данные
    $monto = floatval($_POST['amount'] ?? 0);
    $userId = $_SESSION['user_id'] ?? 'anonimo';
    $currency = $_POST['currency'] ?? 'USD';
    $payment_method = 'Transferencia bancaria';

    // Получаем данные из формы напрямую по именам полей
    $phone = $_POST['client_phone'] ?? '';
    $account_type = $_POST['account_type'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $document_type = $_POST['document_type'] ?? '';
    $document_number = $_POST['document_number'] ?? '';
    $bank_name = $_POST['bank_name'] ?? '';

    // Валидация суммы
    if ($monto <= 0) {
        throw new Exception('Invalid amount');
    }

    // Проверяем баланс пользователя
    $stmt = $conn->prepare("SELECT deposit FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('User not found');
    }

    if ($monto > $user['deposit']) {
        throw new Exception('Amount exceeds available balance');
    }

    // Генерация номера транзакции
    $numeroTransaccion = "№" . (100000000 + random_int(0, 899999999));

    // Создание транзакции в базе данных
    $conn->beginTransaction();
    try {
        $sql = "INSERT INTO historial_pagos (
            user_id, 
            transacciones_data, 
            transacciones_monto, 
            estado, 
            transacción_number, 
            método_de_pago,
            phone,
            cuenta_corriente,
            numero_de_cuenta,
            tipo_de_documento,
            numero_documento,
            banco
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $userId,
            date('Y-m-d H:i:s'),
            $monto,
            'esperando',
            $numeroTransaccion,
            $payment_method,
            $phone,
            $account_type,
            $account_number,
            $document_type,
            $document_number,
            $bank_name
        ]);

        // Обновляем баланс пользователя
        $updateStmt = $conn->prepare("UPDATE users SET deposit = deposit - ? WHERE user_id = ?");
        $updateStmt->execute([$monto, $userId]);
        
        $conn->commit();
        
        $response = [
            'success' => true,
            'transaction_number' => $numeroTransaccion,
            'amount' => $monto,
            'currency' => $currency
        ];

        ob_end_clean();
        echo json_encode($response);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>