<?php
// Тестовый файл для проверки конвертации валют

require_once 'auth_check.php';

echo "<h2>Test Currency Conversion</h2>";
echo "<p>User ID: " . (defined('UID') ? UID : 'Not defined') . "</p>";
echo "<p>Balance: " . (defined('SYS_BALANCE') ? SYS_BALANCE : 'Not defined') . "</p>";
echo "<p>Currency: " . (defined('SYS_CURRENCY') ? SYS_CURRENCY : 'Not defined') . "</p>";
echo "<p>Country: " . (defined('SYS_COUNTRY') ? SYS_COUNTRY : 'Not defined') . "</p>";
echo "<p>Auth: " . (defined('AUTH') && AUTH ? 'Yes' : 'No') . "</p>";

if(defined('UID') && UID && defined('SYS_BALANCE') && defined('SYS_CURRENCY')) {
    echo "<hr>";
    echo "<h3>Testing conversion API call</h3>";
    
    $post_data = json_encode([
        'user_id' => UID,
        'balance' => SYS_BALANCE,
        'currency' => SYS_CURRENCY
    ]);
    
    echo "<p>Sending data: " . htmlspecialchars($post_data) . "</p>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/valorgames/chicken-road/update_balance.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post_data)
    ]);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Code: $http_code</p>";
    echo "<p>Response: " . htmlspecialchars($result) . "</p>";
    
    if($result) {
        $response = json_decode($result, true);
        if($response) {
            echo "<h4>Conversion Result:</h4>";
            echo "<ul>";
            foreach($response as $key => $value) {
                echo "<li><strong>$key:</strong> " . (is_array($value) ? json_encode($value) : $value) . "</li>";
            }
            echo "</ul>";
        }
    }
}
?>
