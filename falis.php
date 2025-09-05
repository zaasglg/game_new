<?php
function saveUtmR() {
    if (isset($_GET['r']) && !empty($_GET['r'])) {
        $result = setcookie('utm_r', $_GET['r'], [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        if ($result) {
            echo "Cookie установлен: utm_r = " . $_GET['r'];
        } else {
            echo "Ошибка при установке cookie";
        }
    } else {
        echo "GET-параметр r отсутствует или пустой";
    }
}

function getUtmR() {
    return isset($_COOKIE['utm_r']) ? $_COOKIE['utm_r'] : 'Cookie не найдена';
}

saveUtmR();
echo "<br>Текущее значение cookie: " . getUtmR();
?>