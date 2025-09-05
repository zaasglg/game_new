<?php
session_start(); // Запуск сессии

// Подключение к базе данных
require 'db.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Conéctese"]);
    exit();
}

// Получение данных из формы
$current_password = $_POST['current_password'];
$new_password = $_POST['password'];
$confirm_password = $_POST['password_confirmation'];

// Проверка, что новый пароль и подтверждение совпадают
if ($new_password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "La nueva contraseña y la confirmación no coinciden"]);
    exit();
}

try {
    // Получаем текущего пользователя
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
        exit();
    }

    // Проверка текущего пароля
    if ($current_password !== $user['password']) { // В реальных приложениях используйте password_verify()
        echo json_encode(["success" => false, "message" => "La contraseña actual es incorrecta"]);
        exit();
    }

    // Обновление пароля
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->execute([
        ':password' => $new_password, // В реальных приложениях используйте password_hash()
        ':id' => $_SESSION['id']
    ]);

    // Успешное обновление пароля
    echo json_encode(["success" => true, "message" => "Contraseña modificada correctamente"]);
} catch (PDOException $e) {
    // Ошибка сервера
    echo json_encode(["success" => false, "message" => "Ошибка сервера: " . $e->getMessage()]);
}
?>