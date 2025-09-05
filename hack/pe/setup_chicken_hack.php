<?php
include '../db.php';

try {
    // Проверяем есть ли поле positions_chicken
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'positions_chicken'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "<p>Добавляем поле positions_chicken...</p>";
        
        // Добавляем поле positions_chicken для хранения безопасных позиций курицы
        $stmt = $conn->prepare("ALTER TABLE users ADD COLUMN positions_chicken TEXT NULL");
        $stmt->execute();
        
        echo "<p style='color: green;'>✅ Поле positions_chicken добавлено!</p>";
    } else {
        echo "<p style='color: green;'>✅ Поле positions_chicken уже существует!</p>";
    }
    
    // Проверяем есть ли поле chicken_multiplier
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'chicken_multiplier'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "<p>Добавляем поле chicken_multiplier...</p>";
        
        // Добавляем поле для хранения предсказанного множителя
        $stmt = $conn->prepare("ALTER TABLE users ADD COLUMN chicken_multiplier DECIMAL(10,2) DEFAULT 2.00");
        $stmt->execute();
        
        echo "<p style='color: green;'>✅ Поле chicken_multiplier добавлено!</p>";
    } else {
        echo "<p style='color: green;'>✅ Поле chicken_multiplier уже существует!</p>";
    }
    
    // Получаем ID текущего пользователя
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    
    // Добавляем тестовые данные для hack bot Chicken Road
    // positions_chicken: строка с безопасными позициями (например "1,3,5,7,9" = безопасные клетки)
    // chicken_multiplier: предсказанный множитель для выигрыша
    $test_positions = "1,3,5,7,9,11,13,15"; // Безопасные позиции курицы
    $test_multiplier = 5.25; // Предсказанный множитель
    
    $stmt = $conn->prepare("UPDATE users SET positions_chicken = :positions, chicken_multiplier = :multiplier WHERE user_id = :user_id");
    $stmt->bindParam(':positions', $test_positions);
    $stmt->bindParam(':multiplier', $test_multiplier);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    echo "<p style='color: blue;'>🐔 Добавлены тестовые данные hack bot для Chicken Road:</p>";
    echo "<ul>";
    echo "<li><strong>Пользователь:</strong> {$user_id}</li>";
    echo "<li><strong>Безопасные позиции:</strong> {$test_positions}</li>";
    echo "<li><strong>Предсказанный множитель:</strong> {$test_multiplier}x</li>";
    echo "</ul>";
    
    // Проверяем что данные сохранились
    $stmt = $conn->prepare("SELECT user_id, email, positions_chicken, chicken_multiplier FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h3>Данные пользователя:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>user_id</th><th>email</th><th>positions_chicken</th><th>chicken_multiplier</th></tr>";
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['positions_chicken']}</td>";
        echo "<td>{$user['chicken_multiplier']}x</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<p><strong>Теперь можно создать hack bot для Chicken Road!</strong></p>";
    }
    
} catch(Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
