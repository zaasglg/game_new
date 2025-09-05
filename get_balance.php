<?php
    require_once 'auth_check.php';

    $stmt = $conn->prepare("SELECT email, deposit, country, bonificaciones FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode( $user ); 
