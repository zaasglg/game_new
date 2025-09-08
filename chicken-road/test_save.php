<?php
// Тест сохранения баланса в базе данных
session_start();
require_once 'init.php';
require_once 'currency.php';

echo "<h1>Тест сохранения баланса в базе данных</h1>";

// Тестовые данные
$test_user_id = 123; // ID пользователя для теста
$test_country = 'Argentina'; // Страна пользователя
$test_balance_usd = 95.50; // Баланс в USD из игры

echo "<h2>Исходные данные:</h2>";
echo "User ID: $test_user_id<br>";
echo "Страна: $test_country<br>";
echo "Баланс в игре (USD): $" . number_format($test_balance_usd, 2) . "<br>";
echo "Курс валюты (1 USD = X ARS): " . getCurrencyRate($test_country) . "<br>";

// Конвертируем в национальную валюту
$balance_national = convertFromUSD($test_balance_usd, $test_country);
echo "Должен сохраниться в базе (ARS): " . number_format($balance_national, 2) . "<br>";

echo "<h2>Тест методов Users класса:</h2>";

try {
    // Тестируем save_game_result
    $users = Users::getInstance();
    
    $result = $users->save_game_result([
        'user_id' => $test_user_id,
        'balance' => $test_balance_usd,
        'bet_amount' => 5.0,
        'win_amount' => 0,
        'game_result' => 'lose'
    ]);
    
    echo "<strong>Результат save_game_result:</strong><br>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    if ($result['success']) {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px 0;'>";
        echo "✅ Баланс успешно сохранен!<br>";
        echo "USD в игре: $" . number_format($result['balance'], 2) . "<br>";
        echo "Национальная валюта в базе: " . number_format($result['balance_national'], 2) . " " . $result['country'] . "<br>";
        echo "</div>";
        
        // Проверяем, что сохранилось правильно
        if (abs($result['balance_national'] - $balance_national) < 0.01) {
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 10px; margin: 10px 0;'>";
            echo "✅ Конвертация работает правильно!<br>";
            echo "Ожидалось: " . number_format($balance_national, 2) . "<br>";
            echo "Сохранилось: " . number_format($result['balance_national'], 2) . "<br>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0;'>";
            echo "❌ Ошибка конвертации!<br>";
            echo "Ожидалось: " . number_format($balance_national, 2) . "<br>";
            echo "Сохранилось: " . number_format($result['balance_national'], 2) . "<br>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0;'>";
        echo "❌ Ошибка сохранения: " . $result['msg'];
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0;'>";
    echo "❌ Исключение: " . $e->getMessage();
    echo "</div>";
}

echo "<h2>Проверка обратной конвертации:</h2>";
$back_to_usd = convertToUSD($balance_national, $test_country);
echo "Из базы обратно в USD: $" . number_format($back_to_usd, 2) . "<br>";

if (abs($back_to_usd - $test_balance_usd) < 0.01) {
    echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px 0;'>";
    echo "✅ Обратная конвертация работает правильно!";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0;'>";
    echo "❌ Ошибка обратной конвертации!<br>";
    echo "Исходный USD: $" . number_format($test_balance_usd, 2) . "<br>";
    echo "Получился USD: $" . number_format($back_to_usd, 2) . "<br>";
    echo "</div>";
}

echo "<h2>Итог:</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-left: 4px solid #6c757d;'>";
echo "<strong>Как должна работать система:</strong><br>";
echo "1. Игра работает в USD: $" . number_format($test_balance_usd, 2) . "<br>";
echo "2. При сохранении конвертируется в национальную валюту: " . number_format($balance_national, 2) . " ARS<br>";
echo "3. В основной базе данных (volurgame.users.deposit) сохраняется: " . number_format($balance_national, 2) . "<br>";
echo "4. При загрузке конвертируется обратно в USD для игры<br>";
echo "</div>";
?>