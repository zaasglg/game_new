<?php
// Тест API для проверки данных пользователя
require_once 'auth_check.php';

header('Content-Type: application/json');

echo json_encode([
    'user_id' => defined('UID') ? UID : null,
    'balance' => defined('SYS_BALANCE') ? SYS_BALANCE : null,
    'currency' => defined('SYS_CURRENCY') ? SYS_CURRENCY : null,
    'country' => defined('SYS_COUNTRY') ? SYS_COUNTRY : null,
    'auth' => defined('AUTH') ? AUTH : null,
    'session' => $_SESSION ?? []
], JSON_PRETTY_PRINT);
?>
