<?php
// Простая проверка подключения к базе для hack bot
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Диагностика hack bot подключения</h2>";

$dbname = 'volurgame';
$username = 'root';
$password = 'root';
$host = '127.0.0.1';
$port = 8889;

echo "<h3>Параметры подключения:</h3>";
echo "<ul>";
echo "<li>База: $dbname</li>";
echo "<li>Хост: $host</li>";
echo "<li>Порт: $port</li>";
echo "<li>Пользователь: $username</li>";
echo "</ul>";

// Тест 1: TCP подключение
echo "<h3>Тест 1: TCP подключение</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ];
    
    $db = new PDO($dsn, $username, $password, $options);
    echo "<p style='color: green;'>✅ TCP подключение успешно!</p>";
    
    // Тестируем запрос
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>📊 Найдено пользователей: " . $result['count'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ TCP ошибка: " . $e->getMessage() . "</p>";
    
    // Тест 2: Socket подключение
    echo "<h3>Тест 2: Socket подключение</h3>";
    try {
        $socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
        echo "<p>Пробуем socket: $socket</p>";
        
        $dsn = "mysql:unix_socket=$socket;dbname=$dbname;charset=utf8mb4";
        $db = new PDO($dsn, $username, $password, $options);
        echo "<p style='color: green;'>✅ Socket подключение успешно!</p>";
        
        // Тестируем запрос
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>📊 Найдено пользователей: " . $result['count'] . "</p>";
        
    } catch (PDOException $e2) {
        echo "<p style='color: red;'>❌ Socket ошибка: " . $e2->getMessage() . "</p>";
        
        // Показываем возможные socket пути
        echo "<h4>Возможные пути socket:</h4>";
        $possible_sockets = [
            '/Applications/MAMP/tmp/mysql/mysql.sock',
            '/var/mysql/mysql.sock',
            '/tmp/mysql.sock',
            '/Applications/MAMP/Library/tmp/mysql.sock'
        ];
        
        foreach ($possible_sockets as $sock) {
            if (file_exists($sock)) {
                echo "<p style='color: blue;'>✅ Найден socket: $sock</p>";
            } else {
                echo "<p style='color: gray;'>❌ Socket не найден: $sock</p>";
            }
        }
    }
}

// Проверяем MAMP статус
echo "<h3>Проверка MAMP:</h3>";
$mamp_running = exec('ps aux | grep -v grep | grep mysql | wc -l');
echo "<p>MySQL процессов: $mamp_running</p>";

if ($mamp_running > 0) {
    echo "<p style='color: green;'>✅ MySQL запущен</p>";
} else {
    echo "<p style='color: red;'>❌ MySQL может быть не запущен</p>";
}
?>
