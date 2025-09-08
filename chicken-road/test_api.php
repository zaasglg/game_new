<?php
// Простой тест API
require_once 'init.php';

echo "Testing API...<br>";

try {
    // Тест подключения к основной базе
    $db = DB2::getInstance();
    $user = $db->get("SELECT user_id, country, deposit FROM users WHERE user_id = 12770156");
    
    if ($user) {
        echo "✅ Main DB connection OK<br>";
        echo "User data: " . json_encode($user) . "<br>";
        
        // Тест конвертации
        require_once 'currency.php';
        $rate = getCurrencyRate($user['country']);
        echo "Currency rate for {$user['country']}: $rate<br>";
        
        $usd = convertToUSD($user['deposit'], $user['country']);
        echo "Balance: {$user['deposit']} → $usd USD<br>";
        
    } else {
        echo "❌ User not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Тест Users класса
try {
    $users = Users::getInstance();
    $result = $users->get_user_balance(['user_id' => 12770156]);
    echo "Users::get_user_balance result: " . json_encode($result) . "<br>";
} catch (Exception $e) {
    echo "❌ Users error: " . $e->getMessage() . "<br>";
}
?>