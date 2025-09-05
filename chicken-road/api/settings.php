<?php
session_start();
require_once "../init.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $play_sounds = isset($_POST['play_sounds']) ? (int)$_POST['play_sounds'] : 0;
    $play_music = isset($_POST['play_music']) ? (int)$_POST['play_music'] : 0;
    
    // Save settings to session (or database if needed)
    $_SESSION['play_sounds'] = $play_sounds;
    $_SESSION['play_music'] = $play_music;
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>
