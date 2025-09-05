<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'db.php';

try {
    // Проверка авторизации
    if (!isset($_SESSION['id'])) {
        throw new Exception("Пользователь не авторизован");
    }

    // Проверка данных
    if (!isset($_POST['balance']) || !is_numeric($_POST['balance'])) {
        throw new Exception("Неверные данные баланса");
    }

    $userId = $_SESSION['id'];
    $newBalance = floatval($_POST['balance']);

    // Обновляем только баланс
    $sql = "UPDATE users SET deposit = :balance WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':balance', $newBalance);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'balance' => $newBalance
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка базы данных: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage()
    ]);
}