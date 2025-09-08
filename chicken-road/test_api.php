<?php
// Тестовый файл для проверки API Chicken Road
include_once "init.php";

echo "<h1>Тест API Chicken Road</h1>";

// Тест 1: Проверка подключения к базе данных
echo "<h2>1. Тест подключения к базе данных</h2>";
try {
    $users = Users::getInstance();
    echo "✅ Подключение к базе данных успешно<br>";
} catch (Exception $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "<br>";
}

// Тест 2: Проверка конвертации валют
echo "<h2>2. Тест конвертации валют</h2>";
try {
    require_once BASE_DIR . 'currency.php';
    
    $test_amount_usd = 100;
    $test_country = 'Argentina';
    
    $converted_to_national = convertFromUSD($test_amount_usd, $test_country);
    $converted_back_to_usd = convertToUSD($converted_to_national, $test_country);
    
    echo "Тест: $test_amount_usd USD → $converted_to_national ARS → $converted_back_to_usd USD<br>";
    
    if (abs($test_amount_usd - $converted_back_to_usd) < 0.01) {
        echo "✅ Конвертация валют работает корректно<br>";
    } else {
        echo "❌ Ошибка в конвертации валют<br>";
    }
} catch (Exception $e) {
    echo "❌ Ошибка конвертации валют: " . $e->getMessage() . "<br>";
}

// Тест 3: Проверка методов Users
echo "<h2>3. Тест методов Users</h2>";
try {
    $users = Users::getInstance();
    
    // Тест get_user_balance (с несуществующим пользователем)
    $balance_result = $users->get_user_balance(['user_id' => 999999]);
    if (isset($balance_result['error'])) {
        echo "✅ Метод get_user_balance корректно обрабатывает несуществующего пользователя<br>";
    } else {
        echo "❌ Метод get_user_balance работает некорректно<br>";
    }
    
    // Тест updateBalance (с несуществующим пользователем)
    $update_result = $users->updateBalance(['user_id' => 999999, 'balance' => 100]);
    if (isset($update_result['error'])) {
        echo "✅ Метод updateBalance корректно обрабатывает несуществующего пользователя<br>";
    } else {
        echo "❌ Метод updateBalance работает некорректно<br>";
    }
    
    // Тест save_game_result (с несуществующим пользователем)
    $save_result = $users->save_game_result([
        'user_id' => 999999, 
        'balance' => 100, 
        'bet_amount' => 10, 
        'win_amount' => 20, 
        'game_result' => 'win'
    ]);
    if (isset($save_result['error'])) {
        echo "✅ Метод save_game_result корректно обрабатывает несуществующего пользователя<br>";
    } else {
        echo "❌ Метод save_game_result работает некорректно<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Ошибка в методах Users: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Тест API маршрутов</h2>";
echo "Для тестирования API маршрутов используйте следующие URL:<br>";
echo "• GET /chicken-road/api/users/get_user_balance (POST с user_id)<br>";
echo "• POST /chicken-road/api/users/update_balance (POST с user_id и balance)<br>";
echo "• POST /chicken-road/api/users/save_game_result (POST с данными игры)<br>";

echo "<h2>Тестирование завершено!</h2>";
?>