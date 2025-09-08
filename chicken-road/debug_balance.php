<?php
// Файл для отладки сохранения баланса в Chicken Road
include_once "init.php";

echo "<h1>Отладка сохранения баланса в Chicken Road</h1>";

// Получаем параметры из URL
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$test_balance = isset($_GET['balance']) ? (float)$_GET['balance'] : 100;

echo "<h2>Параметры:</h2>";
echo "User ID: $user_id<br>";
echo "Test Balance: $test_balance USD<br>";

if ($user_id > 0) {
    echo "<h2>Тест 1: Получение данных пользователя из основной базы</h2>";
    
    try {
        $user_data = DB2::GI()->get("SELECT user_id, country, deposit FROM users WHERE user_id = ?", [$user_id]);
        if ($user_data) {
            echo "✅ Пользователь найден в основной базе:<br>";
            echo "- User ID: " . $user_data['user_id'] . "<br>";
            echo "- Country: " . $user_data['country'] . "<br>";
            echo "- Current Deposit: " . $user_data['deposit'] . " (в национальной валюте)<br>";
            
            // Тест конвертации валют
            echo "<h2>Тест 2: Конвертация валют</h2>";
            require_once BASE_DIR . 'currency.php';
            
            $balance_national = (float)$user_data['deposit'];
            $balance_usd = convertToUSD($balance_national, $user_data['country']);
            
            echo "Текущий баланс: $balance_national " . $user_data['country'] . " = $balance_usd USD<br>";
            
            // Тест обновления баланса
            echo "<h2>Тест 3: Обновление баланса</h2>";
            $users = Users::getInstance();
            
            $update_result = $users->save_game_result([
                'user_id' => $user_id,
                'balance' => $test_balance,
                'bet_amount' => 10,
                'win_amount' => 0,
                'game_result' => 'test'
            ]);
            
            if ($update_result['success']) {
                echo "✅ Баланс успешно обновлен:<br>";
                echo "- Новый баланс в USD: " . $update_result['balance'] . "<br>";
                echo "- Новый баланс в национальной валюте: " . $update_result['balance_national'] . "<br>";
                echo "- Страна: " . $update_result['country'] . "<br>";
                
                // Проверяем, что баланс действительно сохранился
                echo "<h2>Тест 4: Проверка сохранения</h2>";
                $check_data = DB2::GI()->get("SELECT deposit FROM users WHERE user_id = ?", [$user_id]);
                if ($check_data) {
                    $saved_balance_national = (float)$check_data['deposit'];
                    $saved_balance_usd = convertToUSD($saved_balance_national, $user_data['country']);
                    
                    echo "Сохраненный баланс: $saved_balance_national " . $user_data['country'] . " = $saved_balance_usd USD<br>";
                    
                    if (abs($saved_balance_usd - $test_balance) < 0.01) {
                        echo "✅ Баланс сохранен корректно!<br>";
                    } else {
                        echo "❌ Ошибка: баланс сохранен неправильно<br>";
                    }
                }
            } else {
                echo "❌ Ошибка обновления баланса: " . $update_result['msg'] . "<br>";
            }
            
        } else {
            echo "❌ Пользователь с ID $user_id не найден в основной базе данных<br>";
        }
    } catch (Exception $e) {
        echo "❌ Ошибка: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<p>Для тестирования добавьте параметры в URL:</p>";
    echo "<p><code>debug_balance.php?user_id=123&balance=150</code></p>";
}

echo "<h2>Тест API endpoints:</h2>";
echo "<p>Для тестирования API используйте:</p>";
echo "<ul>";
echo "<li><a href='/chicken-road/test_api.php'>Тест PHP API</a></li>";
echo "<li><a href='/chicken-road/test_js.html'>Тест JavaScript API</a></li>";
echo "</ul>";

echo "<h2>Ссылки для игры:</h2>";
echo "<ul>";
echo "<li><a href='/chicken-road/?user_id=demo'>Демо режим</a></li>";
if ($user_id > 0) {
    echo "<li><a href='/chicken-road/?user_id=$user_id&balance=$test_balance'>Реальный режим (User ID: $user_id)</a></li>";
}
echo "</ul>";
?>