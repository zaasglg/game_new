<?php
include '../db.php';

try {
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    
    echo "<h2>🐔 Генератор предсказаний для Chicken Road</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
        // Генерируем случайные безопасные позиции
        $totalPositions = 25;
        $safeCount = rand(8, 15); // От 8 до 15 безопасных позиций
        $allPositions = range(1, $totalPositions);
        shuffle($allPositions);
        $safePositions = array_slice($allPositions, 0, $safeCount);
        sort($safePositions);
        
        // Генерируем случайный множитель
        $multiplier = round(rand(150, 800) / 100, 2); // От 1.50x до 8.00x
        
        // Сохраняем в базу
        $positionsString = implode(',', $safePositions);
        $stmt = $conn->prepare("UPDATE users SET positions_chicken = :positions, chicken_multiplier = :multiplier WHERE user_id = :user_id");
        $stmt->bindParam(':positions', $positionsString);
        $stmt->bindParam(':multiplier', $multiplier);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ Новое предсказание сгенерировано!</h3>";
        echo "<p><strong>Пользователь:</strong> {$user_id}</p>";
        echo "<p><strong>Безопасные позиции:</strong> " . implode(', ', $safePositions) . "</p>";
        echo "<p><strong>Предсказанный множитель:</strong> {$multiplier}x</p>";
        echo "<p><strong>Количество безопасных позиций:</strong> {$safeCount} из {$totalPositions}</p>";
        echo "</div>";
    }
    
    // Показываем текущее предсказание
    $stmt = $conn->prepare("SELECT positions_chicken, chicken_multiplier FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h3>📊 Текущее предсказание:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th style='padding: 10px;'>Параметр</th><th style='padding: 10px;'>Значение</th></tr>";
        echo "<tr><td style='padding: 10px;'>Безопасные позиции</td><td style='padding: 10px;'>{$user['positions_chicken']}</td></tr>";
        echo "<tr><td style='padding: 10px;'>Предсказанный множитель</td><td style='padding: 10px;'>{$user['chicken_multiplier']}x</td></tr>";
        
        if (!empty($user['positions_chicken'])) {
            $positions = explode(',', $user['positions_chicken']);
            echo "<tr><td style='padding: 10px;'>Количество безопасных</td><td style='padding: 10px;'>" . count($positions) . " из 25</td></tr>";
        }
        echo "</table>";
    }
    
    echo "<form method='post' style='margin: 30px 0;'>";
    echo "<button type='submit' name='generate' style='background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>🎲 Сгенерировать новое предсказание</button>";
    echo "</form>";
    
    echo "<div style='background: #e2e3e5; border: 1px solid #d6d8db; color: #383d41; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>ℹ️ Как использовать:</h4>";
    echo "<ol>";
    echo "<li>Нажмите кнопку выше для генерации нового предсказания</li>";
    echo "<li>Откройте <a href='chicken.php' target='_blank'>Chicken Road Hack Bot</a></li>";
    echo "<li>Hack bot автоматически покажет новые предсказания</li>";
    echo "<li>Безопасные позиции будут выделены зеленым цветом</li>";
    echo "<li>Используйте предсказанный множитель для принятия решений</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p style='text-align: center; margin: 30px 0;'>";
    echo "<a href='chicken.php' style='background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🎮 Открыть Chicken Road Hack Bot</a>";
    echo "</p>";
    
} catch(Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "❌ Ошибка: " . $e->getMessage();
    echo "</div>";
}
?>
