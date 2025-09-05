<?php
// API для обновления баланса пользователя с конвертацией в USD
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if( !defined('BASE_DIR') ){ 
    define('BASE_DIR', dirname(__FILE__)."/"); 
}
include_once BASE_DIR ."init.php"; 

$response = ['success' => false, 'message' => 'Invalid request'];

// Курсы валют к доллару (можно обновлять из внешнего API)
$currency_rates = [
    'USD' => 1,
    'ARS' => 0.0011,    // Аргентинский песо
    'COP' => 0.00024,   // Колумбийский песо  
    'CLP' => 0.0011,    // Чилийский песо
    'PEN' => 0.27,      // Перуанский соль
    'MXN' => 0.059,     // Мексиканский песо
    'BRL' => 0.18,      // Бразильский реал
    'EUR' => 1.10,      // Евро
    'BOB' => 0.14,      // Боливийский боливиано
    'CRC' => 0.0019,    // Костариканский колон
    'DOP' => 0.017,     // Доминиканский песо
    'GTQ' => 0.13,      // Гватемальский кетцаль
    'HNL' => 0.040,     // Гондурасская лемпира
    'NIO' => 0.027,     // Никарагуанская кордова
    'PYG' => 0.00013,   // Парагвайский гуарани
    'UYU' => 0.025,     // Уругвайский песо
    'VES' => 0.000027,  // Венесуэльский боливар
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Логируем входящие данные
    error_log("Update balance request: " . json_encode($input));
    
    if(isset($input['user_id']) && isset($input['balance'])) {
        $user_id = (int)$input['user_id'];
        $original_balance = (float)$input['balance'];
        $currency = isset($input['currency']) ? $input['currency'] : 'USD';
        
        error_log("Processing: user_id=$user_id, balance=$original_balance, currency=$currency");
        
        // Конвертируем баланс в USD
        $rate = isset($currency_rates[$currency]) ? $currency_rates[$currency] : 1;
        $usd_balance = $original_balance * $rate;
        
        error_log("Conversion: rate=$rate, usd_balance=$usd_balance");
        
        // Находим пользователя по host_id
        $user = Users::GI()->get(['host_id' => $user_id]);
        
        if($user) {
            error_log("User found: " . json_encode($user));
            // Обновляем баланс в USD
            $result = Users::GI()->edit(['uid' => $user['uid'], 'balance' => $usd_balance]);
            if($result) {
                $response = [
                    'success' => true, 
                    'message' => 'Balance updated and converted to USD', 
                    'original_balance' => $original_balance,
                    'original_currency' => $currency,
                    'conversion_rate' => $rate,
                    'usd_balance' => $usd_balance
                ];
                error_log("Success: " . json_encode($response));
            } else {
                $response = ['success' => false, 'message' => 'Failed to update balance'];
                error_log("Failed to update balance in database");
            }
        } else {
            $response = ['success' => false, 'message' => 'User not found'];
            error_log("User not found for host_id: $user_id");
        }
    } else {
        $response = ['success' => false, 'message' => 'Missing required parameters'];
        error_log("Missing parameters: " . json_encode($input));
    }
}

echo json_encode($response);
?>
