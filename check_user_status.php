<?php
// check_user_status.php

/**
 * Проверяет статус пользователя и завершает сессию, если пользователь забанен
 * 
 * @param PDO $conn Объект подключения к базе данных
 * @param array $session Ссылка на массив сессии
 * @return bool Возвращает false если пользователь забанен, true если активен или не авторизован
 * @throws PDOException Если произошла ошибка при запросе к базе данных
 */
function checkUserStatus(PDO $conn, array &$session): bool {
    // Если пользователь не авторизован, пропускаем проверку
    if (!isset($session['id'])) {
        return true;
    }

    try {
        // Получаем статус пользователя из базы данных
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = :id");
    $stmt->bindValue(':id', $session['id'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если пользователь найден и его статус 'banned'
        if ($user && isset($user['status']) && strtolower($user['status']) === 'banned') {
            // Очищаем и уничтожаем сессию
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_unset();
                session_destroy();
            }
            
            // Перенаправляем на страницу входа с сообщением о блокировке
            if (!headers_sent()) {
                header('Location: /index.php?error=account_banned');
                exit();
            }
            return false;
        }

        return true;
    } catch (PDOException $e) {
        // Логируем ошибку и разрешаем продолжить работу
        error_log("Ошибка при проверке статуса пользователя: " . $e->getMessage());
        return true;
    }
}

// Автоматическая проверка при подключении файла
// Требуем, чтобы перед подключением этого файла были выполнены:
// 1. session_start()
// 2. Подключение к БД ($conn)

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Проверяем, существует ли подключение к БД
if (!isset($conn) || !($conn instanceof PDO)) {
    throw new RuntimeException('Для работы check_user_status.php требуется подключение к БД (PDO $conn)');
}

// Выполняем проверку статуса
if (!checkUserStatus($conn, $_SESSION)) {
    exit;
}
?>