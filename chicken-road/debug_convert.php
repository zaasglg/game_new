<?php
// Простой тест конвертации
require_once 'currency.php';

$test_usd = 6.555;
$test_country = 'Argentina';

echo "Тест конвертации:<br>";
echo "USD: $test_usd<br>";
echo "Страна: $test_country<br>";
echo "Курс: " . getCurrencyRate($test_country) . "<br>";

$national = convertFromUSD($test_usd, $test_country);
echo "Национальная валюта: $national<br>";

$back_to_usd = convertToUSD($national, $test_country);
echo "Обратно в USD: $back_to_usd<br>";

// Проверяем функции напрямую
echo "<br>Прямая проверка:<br>";
echo "6.555 * 1400 = " . (6.555 * 1400) . "<br>";
echo "convertFromUSD(6.555, 'Argentina') = " . convertFromUSD(6.555, 'Argentina') . "<br>";
?>