<?php
include '../db.php';

try {
    echo "<h2>🗑️ Удаление Chicken Road hack bot данных</h2>";
    
    // Удаляем поле positions_chicken
    try {
        $stmt = $conn->prepare("ALTER TABLE users DROP COLUMN positions_chicken");
        $stmt->execute();
        echo "<p style='color: green;'>✅ Поле positions_chicken удалено из базы данных</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Поле positions_chicken: " . $e->getMessage() . "</p>";
    }
    
    // Удаляем поле chicken_multiplier
    try {
        $stmt = $conn->prepare("ALTER TABLE users DROP COLUMN chicken_multiplier");
        $stmt->execute();
        echo "<p style='color: green;'>✅ Поле chicken_multiplier удалено из базы данных</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Поле chicken_multiplier: " . $e->getMessage() . "</p>";
    }
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Очистка завершена!</h3>";
    echo "<p>Все файлы и данные Chicken Road hack bot удалены:</p>";
    echo "<ul>";
    echo "<li>❌ chicken.php</li>";
    echo "<li>❌ chicken_generator.php</li>";
    echo "<li>❌ db-chicken.php</li>";
    echo "<li>❌ setup_chicken_hack.php</li>";
    echo "<li>❌ chicken.webp</li>";
    echo "<li>❌ positions_chicken (поле БД)</li>";
    echo "<li>❌ chicken_multiplier (поле БД)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><strong>Только Mines hack bot остался активным!</strong></p>";
    echo "<p><a href='mines.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🎮 Открыть Mines Hack Bot</a></p>";
    
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
}

// Удаляем этот файл после выполнения
unlink(__FILE__);
?>
