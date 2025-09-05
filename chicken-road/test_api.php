<?php
include_once 'init.php';

header('Content-Type: application/json');

try {
    $cfs = Cfs::getInstance();
    $result = $cfs->load(['full' => 1]);
    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
?>
