<?php
// Prevent any output before JSON
ob_start();
// Disable PHP warnings to avoid breaking JSON
error_reporting(0);

$targetDir = "urls/";
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "urls/";

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["file"])) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = $_FILES["file"]["type"];
    $fileSize = $_FILES["file"]["size"];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($fileType, $allowedTypes)) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(["error" => "Only JPG, JPEG, or PNG files are allowed."]);
        exit;
    }

    if ($fileSize > $maxSize) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(["error" => "File is too large. Max size is 5MB."]);
        exit;
    }

    $filename = uniqid() . '-' . basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $filename;

    if (file_exists($targetFilePath)) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(["error" => "File already exists."]);
        exit;
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        $fileUrl = $baseUrl . $filename;
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 200 OK');
        echo json_encode(["url" => $fileUrl]);
    } else {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(["error" => "Failed to upload file."]);
    }
} else {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "No file uploaded."]);
}
ob_end_flush();
?>