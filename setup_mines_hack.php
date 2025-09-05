<?php
include 'db.php';

try {
    // Проверяем есть ли поле positions_mine
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'positions_mine'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "<p>Добавляем поле positions_mine...</p>";
        
        // Добавляем поле positions_mine
        $stmt = $conn->prepare("ALTER TABLE users ADD COLUMN positions_mine TEXT NULL");
        $stmt->execute();
        
        echo "<p style='color: green;'>✅ Поле positions_mine добавлено!</p>";
    } else {
        echo "<p style='color: green;'>✅ Поле positions_mine уже существует!</p>";
    }
    
    // Получаем ID текущего пользователя из сессии или создаем тестового
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    
    // Добавляем тестовые позиции мин для пользователя
    $test_positions = "3,7,12,15,22"; // Позиции где НЕТ мин (безопасные клетки)
    
    $stmt = $conn->prepare("UPDATE users SET positions_mine = :positions WHERE user_id = :user_id");
    $stmt->bindParam(':positions', $test_positions);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    echo "<p style='color: blue;'>🎯 Добавлены тестовые позиции мин для пользователя {$user_id}: {$test_positions}</p>";
    
    // Проверяем что данные сохранились
    $stmt = $conn->prepare("SELECT user_id, email, positions_mine FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h3>Данные пользователя:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>user_id</th><th>email</th><th>positions_mine</th></tr>";
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['positions_mine']}</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<p><strong>Теперь можно тестировать hack bot!</strong></p>";
        echo "<p><a href='hack/pe/mines.php' target='_blank'>🎮 Открыть Mines Hack Bot</a></p>";
    }
    
} catch(Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
