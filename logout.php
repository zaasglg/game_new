<?php
session_start(); // Запуск сессии
session_destroy(); // Уничтожение сессии
header("Location: index.php"); // Перенаправление на страницу входа
exit();
?>