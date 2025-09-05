<?php
session_start();
$_SESSION['user_id'] = 12770156; // Устанавливаем user_id для теста

echo "<h2>Тест API для Chicken Road</h2>";

// Тестируем получение коэффициента
echo "<h3>Тест получения коэффициента:</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/hack/pe/db-chicken-api.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=get_chicken_coefficient");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Response:</strong></p>";
echo "<pre>$response</pre>";

if ($response) {
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "<p style='color: green;'>✅ API работает! Коэффициент: " . $data['coefficient'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Ошибка API: " . ($data['message'] ?? 'Unknown error') . "</p>";
    }
}

// Тестируем обновление коэффициента
echo "<h3>Тест обновления коэффициента:</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/hack/pe/db-chicken-api.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=update_chicken_coefficient&coefficient=4.25");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode2</p>";
echo "<p><strong>Response:</strong></p>";
echo "<pre>$response2</pre>";

if ($response2) {
    $data2 = json_decode($response2, true);
    if ($data2 && $data2['success']) {
        echo "<p style='color: green;'>✅ Обновление работает! Новый коэффициент: " . $data2['coefficient'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Ошибка обновления: " . ($data2['message'] ?? 'Unknown error') . "</p>";
    }
}
?>
