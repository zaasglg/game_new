<?php
include 'db.php';

try {
    // Проверяем структуру таблицы users
    $stmt = $conn->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Структура таблицы users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $hasPositionsMine = false;
    foreach($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
        
        if($column['Field'] === 'positions_mine') {
            $hasPositionsMine = true;
        }
    }
    echo "</table>";
    
    if($hasPositionsMine) {
        echo "<p style='color: green;'>✅ Поле positions_mine существует!</p>";
        
        // Проверяем данные
        $stmt = $conn->prepare("SELECT user_id, email, positions_mine FROM users LIMIT 5");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Пример данных:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>user_id</th><th>email</th><th>positions_mine</th></tr>";
        foreach($users as $user) {
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['positions_mine']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>❌ Поле positions_mine НЕ существует!</p>";
        echo "<p>Нужно добавить поле в таблицу users.</p>";
    }
    
} catch(Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
