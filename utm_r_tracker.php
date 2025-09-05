<?php
// Запускаем сессию только если она еще не активна
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Функция для сохранения UTM-метки r
function saveUtmR() {
    if (isset($_GET['r']) && !empty($_GET['r'])) {
        $utm_r = htmlspecialchars($_GET['r'], ENT_QUOTES, 'UTF-8');
        
        // Пытаемся сохранить в cookie на 30 дней
        $cookie_set = setcookie('utm_r', $utm_r, [
            'expires' => time() + (30 * 24 * 60 * 60), // 30 дней
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Проверяем, установилась ли cookie (проверяем $_COOKIE после setcookie)
        // Если cookie не установилась, сохраняем в сессию на 1 день
        if (!$cookie_set || !isset($_COOKIE['utm_r'])) {
            $_SESSION['utm_r'] = $utm_r;
            $_SESSION['utm_r_expiry'] = time() + (24 * 60 * 60); // 1 день
        }
    }
}

// Функция для получения UTM-метки r
function getUtmR() {
    // Сначала проверяем cookie
    if (isset($_COOKIE['utm_r'])) {
        return htmlspecialchars($_COOKIE['utm_r'], ENT_QUOTES, 'UTF-8');
    }
    
    // Если cookie нет, проверяем сессию
    if (isset($_SESSION['utm_r']) && isset($_SESSION['utm_r_expiry'])) {
        // Проверяем, не истек ли срок действия сессии
        if ($_SESSION['utm_r_expiry'] >= time()) {
            return htmlspecialchars($_SESSION['utm_r'], ENT_QUOTES, 'UTF-8');
        } else {
            // Если срок действия истек, удаляем данные из сессии
            unset($_SESSION['utm_r']);
            unset($_SESSION['utm_r_expiry']);
        }
    }
    
    return '';
}

// Вызываем сохранение UTM-метки при наличии GET-параметра r
if (isset($_GET['r'])) {
    saveUtmR();
}

// Пример вывода для тестирования
$utm_r = getUtmR();
?>