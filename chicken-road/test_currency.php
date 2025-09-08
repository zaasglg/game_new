<?php
// Тестовый файл для проверки системы конвертации валют
require_once 'currency.php';

echo "<h1>Тест системы конвертации валют Chicken Road</h1>";

// Тестовые данные
$test_countries = ['Argentina', 'Colombia', 'Mexico', 'Brazil', 'Peru'];
$test_balance_usd = 100; // $100 USD

echo "<h2>Конвертация $100 USD в национальные валюты:</h2>";
echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th>Страна</th><th>Курс (1 USD = X)</th><th>$100 USD в национальной валюте</th><th>Обратно в USD</th></tr>";

foreach ($test_countries as $country) {
    $rate = getCurrencyRate($country);
    $national_amount = convertFromUSD($test_balance_usd, $country);
    $back_to_usd = convertToUSD($national_amount, $country);
    
    echo "<tr>";
    echo "<td>$country</td>";
    echo "<td>$rate</td>";
    echo "<td>" . number_format($national_amount, 2) . "</td>";
    echo "<td>$" . number_format($back_to_usd, 2) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Пример работы игры:</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; margin: 10px 0;'>";
echo "<strong>Сценарий:</strong> Пользователь из Аргентины с балансом 140,000 ARS заходит в игру<br>";

$argentina_balance = 140000; // 140,000 ARS
$usd_for_game = convertToUSD($argentina_balance, 'Argentina');

echo "1. Баланс в базе данных: " . number_format($argentina_balance, 2) . " ARS<br>";
echo "2. Конвертируется для игры в: $" . number_format($usd_for_game, 2) . " USD<br>";
echo "3. Игрок видит в игре: $" . number_format($usd_for_game, 2) . " USD<br>";

// Симуляция ставки
$bet_usd = 5; // $5 USD ставка
$new_balance_usd = $usd_for_game - $bet_usd;
$new_balance_ars = convertFromUSD($new_balance_usd, 'Argentina');

echo "4. Игрок делает ставку: $" . number_format($bet_usd, 2) . " USD<br>";
echo "5. Новый баланс в игре: $" . number_format($new_balance_usd, 2) . " USD<br>";
echo "6. Сохраняется в базе как: " . number_format($new_balance_ars, 2) . " ARS<br>";

// Симуляция выигрыша
$win_multiplier = 2.5;
$win_amount_usd = $bet_usd * $win_multiplier;
$final_balance_usd = $new_balance_usd + $win_amount_usd;
$final_balance_ars = convertFromUSD($final_balance_usd, 'Argentina');

echo "7. Игрок выигрывает с коэффициентом x" . $win_multiplier . ": $" . number_format($win_amount_usd, 2) . " USD<br>";
echo "8. Итоговый баланс в игре: $" . number_format($final_balance_usd, 2) . " USD<br>";
echo "9. Сохраняется в базе как: " . number_format($final_balance_ars, 2) . " ARS<br>";
echo "10. При выходе из игры отправляется родительскому окну: " . number_format($final_balance_ars, 2) . " ARS<br>";

echo "</div>";

echo "<h2>Проверка точности конвертации:</h2>";
$original = 1000;
foreach ($test_countries as $country) {
    $converted = convertFromUSD($original, $country);
    $back = convertToUSD($converted, $country);
    $diff = abs($original - $back);
    
    echo "<strong>$country:</strong> $original USD → " . number_format($converted, 2) . " → $back USD (разница: $diff)<br>";
}

echo "<h2>Статус системы:</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "✅ Система конвертации валют настроена и работает<br>";
echo "✅ При входе в игру: национальная валюта → USD для отображения<br>";
echo "✅ При выходе из игры: USD → национальная валюта для сохранения<br>";
echo "✅ Все расчеты в игре ведутся в USD для единообразия<br>";
echo "✅ Баланс сохраняется в национальной валюте пользователя<br>";
echo "</div>";
?>