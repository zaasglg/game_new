<?php

// Подключение к базе данных
require 'db.php';



$email = 'skkieeoss@mail.com';
$password = 'g949493'; 
$country = 'Ecuador'; 
// $id = '15971318';
$ref = '65465466';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("INSERT INTO users (id, email, password, country, ref) VALUES (:id, :email, :password, :country, :ref)");
        $stmt->execute([
            ':id' => $id,
            ':email' => $email,
            ':password' => $password, // Пароль без хеширования (ОПАСНО)
            ':country' => $country, 
            ':ref' => $ref, 
        ]);
?>
