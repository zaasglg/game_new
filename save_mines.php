<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'db.php';

try {
    // Проверка авторизации
    if (!isset($_SESSION['id'])) {
        throw new Exception("Пользователь не авторизован");
    }

    // Проверка позиций мин
    if (!isset($_POST['mine_positions']) || empty($_POST['mine_positions'])) {
        throw new Exception("Не переданы позиции мин");
    }

    // Получаем и сортируем позиции
    $minePositions = $_POST['mine_positions'];
    $positionsArray = explode(',', $minePositions);
    $positionsArray = array_map('intval', $positionsArray); // Преобразуем в числа
    sort($positionsArray); // Сортируем от меньшего к большему
    $sortedPositions = implode(',', $positionsArray);

    // Обновляем в базе
    $sql = "UPDATE users SET positions_mine = :positions_mine WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':positions_mine', $sortedPositions);
    $stmt->bindParam(':id', $_SESSION['id']);
    $stmt->execute();

    echo json_encode(['success' => true]);

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