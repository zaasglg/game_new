<?php
session_start();

// Устанавливаем тестовый user_id для проверки
$_SESSION['user_id'] = 1;

echo "<h2>Тестирование подключения hack bot к базе</h2>";

// Тестируем db-valor.php
echo "<h3>Проверка db-valor.php:</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/hack/pe/db-valor.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=get_user_mines&user_id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Response:</strong></p>";
echo "<pre>$response</pre>";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "<p style='color: green;'>✅ Hack bot API работает!</p>";
        } else {
            echo "<p style='color: red;'>❌ Ошибка API: " . $data['message'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Неверный формат ответа</p>";
    }
} else {
    echo "<p style='color: red;'>❌ HTTP ошибка: $httpCode</p>";
}

curl_close($ch);

// Проверяем основное подключение
echo "<h3>Проверка основного подключения:</h3>";
try {
    include 'db.php';
    
    $stmt = $conn->prepare("SELECT user_id, email, positions_mine FROM users WHERE user_id = 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>✅ Основная база данных работает</p>";
        echo "<table border='1'>";
        echo "<tr><th>user_id</th><th>email</th><th>positions_mine</th></tr>";
        echo "<tr><td>{$user['user_id']}</td><td>{$user['email']}</td><td>{$user['positions_mine']}</td></tr>";
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Пользователь не найден</p>";
    }
    
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Ошибка основной базы: " . $e->getMessage() . "</p>";
}
?>
