<?php
// Тестовая страница для проверки сохранения коэффициентов

echo "<h2>Тест сохранения коэффициентов ловушки Chicken Road</h2>";

// Проверяем демо коэффициент
$demoFile = 'demo_coefficients.json';
if (file_exists($demoFile)) {
    $demoData = json_decode(file_get_contents($demoFile), true);
    echo "<h3>Демо коэффициент:</h3>";
    echo "<pre>" . json_encode($demoData, JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p>Демо коэффициент не найден</p>";
}

// Подключаемся к базе данных для проверки реальных коэффициентов
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASSWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Проверяем структуру таблицы users
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'chicken_trap_coefficient'");
    $column = $stmt->fetch();
    
    if ($column) {
        echo "<h3>Колонка chicken_trap_coefficient существует</h3>";
        
        // Показываем несколько записей с коэффициентами
        $stmt = $pdo->query("SELECT user_id, chicken_trap_coefficient FROM users WHERE chicken_trap_coefficient IS NOT NULL LIMIT 10");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($results) {
            echo "<h4>Сохраненные коэффициенты:</h4>";
            echo "<table border='1'>";
            echo "<tr><th>User ID</th><th>Коэффициент</th></tr>";
            foreach ($results as $row) {
                echo "<tr><td>{$row['user_id']}</td><td>{$row['chicken_trap_coefficient']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Пока нет сохраненных коэффициентов</p>";
        }
    } else {
        echo "<h3>Нужно добавить колонку chicken_trap_coefficient в таблицу users</h3>";
        echo "<p>Выполните SQL:</p>";
        echo "<pre>ALTER TABLE users ADD COLUMN chicken_trap_coefficient DECIMAL(5,2) DEFAULT NULL;</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p>Ошибка подключения к БД: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>JavaScript тест</h3>";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Тест сохранения коэффициента
function testSaveCoefficient(mode) {
    var testCoeff = (Math.random() * 6 + 1.5).toFixed(2);
    var isDemo = mode === 'demo' ? 1 : 0;
    var userId = mode === 'demo' ? 'demo' : Math.floor(Math.random() * 1000);
    
    $.ajax({
        url: "/hack/pe/db-chicken-api.php",
        type: "json",
        method: "post",
        data: {
            action: "update_chicken_coefficient",
            coefficient: testCoeff,
            user_id: userId,
            is_demo: isDemo
        },
        success: function(r) {
            console.log("Тест сохранения (" + mode + "):", r);
            alert("Коэффициент " + testCoeff + " сохранен для " + mode + " режима");
        },
        error: function(e) {
            console.error("Ошибка сохранения (" + mode + "):", e);
            alert("Ошибка сохранения для " + mode + " режима");
        }
    });
}

// Тест загрузки коэффициента
function testLoadCoefficient(mode) {
    var isDemo = mode === 'demo' ? 1 : 0;
    var userId = mode === 'demo' ? 'demo' : Math.floor(Math.random() * 1000);
    
    $.ajax({
        url: "/hack/pe/db-chicken-api.php",
        type: "json",
        method: "post",
        data: {
            action: "get_chicken_coefficient",
            user_id: userId,
            is_demo: isDemo
        },
        success: function(r) {
            console.log("Тест загрузки (" + mode + "):", r);
            alert("Загружен коэффициент: " + r.coefficient + " для " + mode + " режима");
        },
        error: function(e) {
            console.error("Ошибка загрузки (" + mode + "):", e);
            alert("Ошибка загрузки для " + mode + " режима");
        }
    });
}
</script>

<button onclick="testSaveCoefficient('demo')">Тест сохранения (Демо)</button>
<button onclick="testSaveCoefficient('real')">Тест сохранения (Реальный)</button>
<button onclick="testLoadCoefficient('demo')">Тест загрузки (Демо)</button>
<button onclick="testLoadCoefficient('real')">Тест загрузки (Реальный)</button>

<p>Проверьте консоль браузера для результатов</p>
