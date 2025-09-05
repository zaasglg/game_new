<?php
// Подключаем файл с функциями UTM
require_once 'utm_r_tracker.php';

// Получаем UTM-метку r
$utm_r = getUtmR();

// Пример использования UTM-метки
echo "UTM R: " . htmlspecialchars($utm_r) . "<br>";

// Пример построения URL с сохраненной UTM-меткой
$base_url = "https://example.com/page";
$full_url = $utm_r ? $base_url . '?r=' . urlencode($utm_r) : $base_url;

echo "URL с UTM: " . htmlspecialchars($full_url);
?>