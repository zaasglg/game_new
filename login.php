<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Проверяем статус пользователя
        if (isset($user['status']) && $user['status'] === 'banned') {
            echo json_encode([
                "success" => false, 
                "message" => "ID: " . $user['user_id'] . "\nFue bloqueada. Puedes consultar los motivos en el chat con el soporte."
            ]);
            exit;
        }
        
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            echo json_encode(["success" => true, "message" => "Autorización concedida."]);
        } else {
            echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No se ha encontrado ningún usuario con esta dirección de correo electrónico"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error del servidor. Vuelva a intentarlo más tarde."]);
}
?>