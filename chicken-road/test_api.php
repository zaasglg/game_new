<?php
// Простой тест для проверки сохранения коэффициентов ловушки

echo "<h2>Тест сохранения коэффициентов ловушки в volurgame.users</h2>";

// Тест через POST запрос
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $coefficient = $_POST['coefficient'] ?? 0;
    $user_id = $_POST['user_id'] ?? 0;
    $is_demo = $_POST['is_demo'] ?? 0;
    
    echo "<h3>Результат теста:</h3>";
    echo "<p>Action: $action</p>";
    echo "<p>Coefficient: $coefficient</p>";
    echo "<p>User ID: $user_id</p>";
    echo "<p>Is Demo: $is_demo</p>";
    
    // Симулируем запрос к API
    $postData = http_build_query([
        'action' => $action,
        'coefficient' => $coefficient,
        'user_id' => $user_id,
        'is_demo' => $is_demo
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        ]
    ]);
    
    $result = file_get_contents('http://localhost/valorgames/hack/pe/db-chicken-api.php', false, $context);
    echo "<h4>Ответ API:</h4>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
}
?>

<form method="post">
    <h3>Тест сохранения коэффициента (Реальный пользователь)</h3>
    <input type="hidden" name="action" value="update_chicken_coefficient">
    <label>User ID: <input type="number" name="user_id" value="1" required></label><br><br>
    <label>Коэффициент: <input type="number" step="0.01" name="coefficient" value="<?= number_format(rand(150, 750) / 100, 2) ?>" required></label><br><br>
    <input type="hidden" name="is_demo" value="0">
    <button type="submit">Сохранить коэффициент (Реальный)</button>
</form>

<form method="post">
    <h3>Тест сохранения коэффициента (Демо)</h3>
    <input type="hidden" name="action" value="update_chicken_coefficient">
    <input type="hidden" name="user_id" value="demo">
    <label>Коэффициент: <input type="number" step="0.01" name="coefficient" value="<?= number_format(rand(150, 750) / 100, 2) ?>" required></label><br><br>
    <input type="hidden" name="is_demo" value="1">
    <button type="submit">Сохранить коэффициент (Демо)</button>
</form>

<form method="post">
    <h3>Тест загрузки коэффициента (Реальный пользователь)</h3>
    <input type="hidden" name="action" value="get_chicken_coefficient">
    <label>User ID: <input type="number" name="user_id" value="1" required></label><br><br>
    <input type="hidden" name="is_demo" value="0">
    <button type="submit">Загрузить коэффициент (Реальный)</button>
</form>

<form method="post">
    <h3>Тест загрузки коэффициента (Демо)</h3>
    <input type="hidden" name="action" value="get_chicken_coefficient">
    <input type="hidden" name="user_id" value="demo">
    <input type="hidden" name="is_demo" value="1">
    <button type="submit">Загрузить коэффициент (Демо)</button>
</form>

<hr>
<h3>Проверка существующих данных</h3>
<?php
require_once '../db.php';

try {
    // Показываем несколько записей с коэффициентами
    $stmt = $conn->query("SELECT user_id, chicken_trap_coefficient, created_at FROM users WHERE chicken_trap_coefficient IS NOT NULL ORDER BY created_at DESC LIMIT 5");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($results) {
        echo "<h4>Последние сохраненные коэффициенты в volurgame.users:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>User ID</th><th>Коэффициент</th><th>Дата создания</th></tr>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>{$row['user_id']}</td>";
            echo "<td>{$row['chicken_trap_coefficient']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Пока нет сохраненных коэффициентов в volurgame.users</p>";
    }
    
    // Проверяем демо файл
    $demoFile = 'demo_coefficients.json';
    if (file_exists($demoFile)) {
        $demoData = json_decode(file_get_contents($demoFile), true);
        echo "<h4>Демо коэффициент:</h4>";
        echo "<pre>" . json_encode($demoData, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p>Демо коэффициент еще не сохранен</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>Ошибка подключения к БД: " . $e->getMessage() . "</p>";
}
?>

<script>
console.log("Тестовая страница загружена. Готова к тестированию API.");
</script>
