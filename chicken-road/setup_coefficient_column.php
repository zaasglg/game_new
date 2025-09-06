<?php
// Скрипт для добавления колонки chicken_trap_coefficient в volurgame.users

require_once '../db.php';

try {
    echo "Подключение к базе данных volurgame...\n";
    
    // Проверяем существование колонки
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'chicken_trap_coefficient'");
    $column = $stmt->fetch();
    
    if (!$column) {
        echo "Добавляем колонку chicken_trap_coefficient в таблицу users...\n";
        $conn->exec("ALTER TABLE users ADD COLUMN chicken_trap_coefficient DECIMAL(5,2) DEFAULT NULL COMMENT 'Коэффициент ловушки для chicken road hack bot'");
        echo "Колонка chicken_trap_coefficient добавлена успешно!\n";
    } else {
        echo "Колонка chicken_trap_coefficient уже существует в таблице users\n";
    }
    
    // Показываем информацию о колонке
    $stmt = $conn->query("SHOW COLUMNS FROM users WHERE Field = 'chicken_trap_coefficient'");
    $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($columnInfo) {
        echo "Информация о колонке chicken_trap_coefficient:\n";
        echo "Тип: " . $columnInfo['Type'] . "\n";
        echo "Null: " . $columnInfo['Null'] . "\n";
        echo "Default: " . ($columnInfo['Default'] ?: 'NULL') . "\n";
        echo "Extra: " . $columnInfo['Extra'] . "\n";
    }
    
    // Проверяем сколько пользователей в таблице
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Всего пользователей в таблице users: " . $userCount . "\n";
    
    echo "Готово!\n";
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
?>
