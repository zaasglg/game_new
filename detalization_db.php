<?php
// session_start();
require_once 'db.php';

// Проверяем, что запрос пришел через AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    try {
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_id = (int)$user_id;
        
        // Конфигурация для таблицы historial
        $config = [
            'table' => 'historial',
            'date_field' => 'transacciones_data',
            'id_field' => 'transacción_number',
            'amount_field' => 'transacciones_monto',
            'method_field' => 'método_de_pago',
            'status_field' => 'estado',
            'output_key' => 'transactions'
        ];
        
        $stmt = $conn->prepare("SELECT * FROM {$config['table']} WHERE user_id = ? ORDER BY {$config['date_field']} DESC");
        $stmt->execute([$user_id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Преобразуем данные для удобства обработки в JS
        foreach ($data as &$item) {
            $item[$config['date_field']] = date('m.d.Y H:i', strtotime($item[$config['date_field']]));
        }
        
        echo json_encode([
            'success' => true,
            $config['output_key'] => $data,
            'fields' => $config
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
        ]);
        error_log("Database error: " . $e->getMessage());
    }
    exit;
}
?>