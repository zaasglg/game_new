<?php
// auth_check.php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1209600);
    session_set_cookie_params([
        'lifetime' => 1209600,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
if (isset($_SESSION['language'])) {
  echo '<script>window.userDefaultLang = "' . $_SESSION['language'] . '";</script>';
}

if (isset($_SESSION['last_activity'])) {
    $session_duration = 1209600;
    $session_lifetime = time() - $_SESSION['last_activity'];
    
    if ($session_lifetime > $session_duration) {
        session_unset();
        session_destroy();
        $is_logged_in = false;
    } else {
        $_SESSION['last_activity'] = time();
    }
}

if (!isset($_SESSION['user_id'])) {
    $is_logged_in = false;
    $default_lang = 'es'; // По умолчанию испанский для неавторизованных пользователей
} else {
    $is_logged_in = true;
    $body_class = $is_logged_in ? 'user-logged-in' : '';
    
    require 'db.php';
    
    try {
        $stmt = $conn->prepare("SELECT user_id, email, deposit, country, bonificaciones, status FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Проверяем статус пользователя
            if (isset($user['status']) && $user['status'] === 'banned') {
                session_unset();
                session_destroy();
                $is_logged_in = false;
                header("Location: index.php?banned=1&id=" . $user['user_id']);
                exit;
            }
            
            $email = $user['email'];
            $deposit = $user['deposit'];
            $country = $user['country'];
            $bonificaciones = $user['bonificaciones'] ?? 0;
            $is_admin = ($email === 'admin');
	    
	    define('UID', $user['user_id']);

            // Маппинг стран к языкам
            $country_lang_map = [
                'Argentina' => 'es',
                'Bolivia' => 'es',
                'Brazil' => 'pt',
                'Chile' => 'es',
                'Colombia' => 'es',
                'Costa Rica' => 'es',
                'Cuba' => 'es',
                'Dominican Republic' => 'es',
                'Ecuador' => 'es',
                'El Salvador' => 'es',
                'Guatemala' => 'es',
                'Haiti' => 'en',
                'Honduras' => 'es',
                'Mexico' => 'es',
                'Nicaragua' => 'es',
                'Panama' => 'es',
                'Paraguay' => 'es',
                'Peru' => 'es',
                'Puerto Rico' => 'en',
                'Uruguay' => 'es',
                'Venezuela' => 'es',
            ];

            // Устанавливаем язык по умолчанию в зависимости от страны
            $default_lang = $country_lang_map[$country] ?? 'es'; // По умолчанию испанский    

            $currency_map = [
                'Argentina' => 'ARS',
                'Bolivia' => 'BOB',
                'Brazil' => 'BRL',
                'Chile' => 'CLP',
                'Colombia' => 'COP',
                'Costa Rica' => 'CRC',
                'Cuba' => 'CUP',
                'Dominican Republic' => 'DOP',
                'Ecuador' => 'USD',
                'El Salvador' => 'USD',
                'Guatemala' => 'Q',
                'Haiti' => 'HTG',
                'Honduras' => 'HNL',
                'Mexico' => 'MXN',
                'Nicaragua' => 'NIO',
                'Panama' => 'USD',
                'Paraguay' => 'PYG',
                'Peru' => 'PEN',
                'Puerto Rico' => 'USD',
                'Uruguay' => 'UYU',
                'Venezuela' => 'VES',
            ];
            $currency = $currency_map[$country] ?? 'USD';
        }
    } catch (PDOException $e) {
        die("Error al recuperar los datos del usuario: " . $e->getMessage());
    }
}

if ($is_logged_in) {
    $_SESSION['last_activity'] = time();
}


if( !defined('UID') ){
    define('UID', '');
}
// авторизован ли юзер
define('AUTH', isset( $is_logged_in ) ? $is_logged_in : false );
// баланс пользователя 
define('SYS_BALANCE', isset( $deposit ) ? $deposit : 0 ); 
// системная валюта
define('SYS_CURRENCY', isset( $currency ) ? $currency : 'USD' ); 
// бонусы 
define('SYS_BONUS', isset( $bonificaciones ) ? $bonificaciones : 0 ); 
// страна юзера
define('SYS_COUNTRY', isset( $country ) ? $country : '' ); 
// 
define('SYS_STAGE', isset( $stage ) ? $stage : '' ); 




// Передаем язык по умолчанию в JavaScript
echo "<script>var userDefaultLang = '" . ($default_lang ?? 'es') . "';</script>";
?>