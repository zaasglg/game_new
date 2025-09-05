<?php
// Тест системы обновления коэффициента ловушки Chicken Road
include 'db.php';

$user_id = 12770156;

// Проверяем текущий коэффициент
$stmt = $conn->prepare('SELECT chicken_trap_coefficient FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$current_coefficient = $stmt->fetchColumn();

echo "<h1>🐔 Тест системы Chicken Road Hack Bot</h1>";
echo "<p><strong>User ID:</strong> $user_id</p>";
echo "<p><strong>Текущий коэффициент в БД:</strong> " . ($current_coefficient ?: 'Не найден') . "</p>";

// Симулируем обновление коэффициента (как если бы игрок нажал Play)
$new_coefficient = round(mt_rand(150, 750) / 100, 2); // от 1.50 до 7.50

echo "<hr>";
echo "<h2>🎮 Симуляция нажатия кнопки Play в игре</h2>";
echo "<p>Генерируем новый коэффициент: <strong>$new_coefficient</strong></p>";

// Обновляем коэффициент
$stmt = $conn->prepare('UPDATE users SET chicken_trap_coefficient = ? WHERE user_id = ?');
$result = $stmt->execute([$new_coefficient, $user_id]);

if ($result) {
    echo "<p style='color: green;'>✅ Коэффициент успешно обновлен в базе данных!</p>";
} else {
    echo "<p style='color: red;'>❌ Ошибка обновления коэффициента</p>";
}

// Проверяем обновленное значение
$stmt = $conn->prepare('SELECT chicken_trap_coefficient FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$updated_coefficient = $stmt->fetchColumn();

echo "<p><strong>Новый коэффициент в БД:</strong> " . ($updated_coefficient ?: 'Не найден') . "</p>";

echo "<hr>";
echo "<h2>🔗 Ссылки для тестирования</h2>";
echo "<p><a href='/chicken-road/?user_id=$user_id' target='_blank'>🎮 Открыть игру Chicken Road</a></p>";
echo "<p><a href='/hack/chicken_road.php?user_id=$user_id' target='_blank'>🤖 Открыть Hack Bot</a></p>";

echo "<hr>";
echo "<h2>📋 Инструкция по тестированию</h2>";
echo "<ol>";
echo "<li>Откройте игру Chicken Road по ссылке выше</li>";
echo "<li>Сделайте ставку и нажмите кнопку 'Play'</li>";
echo "<li>Система автоматически обновит коэффициент ловушки в базе данных</li>";
echo "<li>Откройте Hack Bot и нажмите 'Анализ игры'</li>";
echo "<li>Hack Bot покажет актуальный коэффициент из базы данных</li>";
echo "</ol>";

echo "<p><em>Каждый раз при нажатии 'Play' в игре генерируется новый коэффициент ловушки и сохраняется в базе данных.</em></p>";
?>
