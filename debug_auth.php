<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Auth</title>
</head>
<body>
    <h1>Debug Information</h1>
    <p><strong>Авторизован:</strong> <?= AUTH ? 'Да' : 'Нет' ?></p>
    <p><strong>USER ID:</strong> <?= UID ? UID : 'Не установлен' ?></p>
    <p><strong>Баланс:</strong> <?= defined('SYS_BALANCE') ? SYS_BALANCE : 'Не определен' ?></p>
    <p><strong>Валюта:</strong> <?= defined('SYS_CURRENCY') ? SYS_CURRENCY : 'Не определена' ?></p>
    <p><strong>Страна:</strong> <?= defined('SYS_COUNTRY') ? SYS_COUNTRY : 'Не определена' ?></p>
    
    <?php if(!AUTH): ?>
    <p style="color: red;">Вы не авторизованы. <a href="/">Войти в систему</a></p>
    <?php else: ?>
    <p style="color: green;">Вы авторизованы! <a href="/all_games.php?game=chicken_road">Открыть игру</a></p>
    <?php endif; ?>
</body>
</html>
