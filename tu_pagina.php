<?php
require_once 'db.php';

// Загружаем конфигурацию из JSON-файла
$configFile = 'deposit_config.json';
if (!file_exists($configFile)) {
    die("Ошибка: Конфигурационный файл не найден");
}

$configContent = file_get_contents($configFile);
$config = json_decode($configContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Ошибка при чтении конфигурационного файла: " . json_last_error_msg());
}

if (!isset($config['depositConfig'])) {
    die("Ошибка: Неверный формат конфигурационного файла");
}

$depositConfig = $config['depositConfig'];

// Проверяем параметры транзакции
if (isset($_GET['transaccion']) && isset($_GET['estado'])) {
    $transaccion_number = $_GET['transaccion'];
    $estado = $_GET['estado'];
    
    if (!in_array($estado, ['esperando', 'completed', 'declined'])) {
        die('Error: Estado inválido');
    }
    
    try {
        $conn->beginTransaction();
        
        // Проверяем, не обрабатывалась ли уже эта транзакция с таким статусом
        $stmt = $conn->prepare("SELECT estado FROM historial WHERE transacción_number = ?");
        $stmt->execute([$transaccion_number]);
        $current_status = $stmt->fetchColumn();
        
        // Если статус уже такой же, как в запросе, просто выходим
        if ($current_status === $estado) {
            $conn->commit();
            echo "Транзакция {$transaccion_number} уже имеет статус: {$estado}";
            exit;
        }
        
        // Получаем данные транзакции и пользователя
        $stmt = $conn->prepare("SELECT h.id, h.transacciones_monto, u.country, u.deposit 
                               FROM historial h
                               JOIN users u ON h.id = u.id
                               WHERE h.transacción_number = ?");
        $stmt->execute([$transaccion_number]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new Exception("Transacción no encontrada");
        }

        $id = $data['id'];
        $monto = $data['transacciones_monto'];
        $country = $data['country'];
        $current_deposit = $data['deposit'];

        // Проверяем, первая ли это успешная транзакция пользователя
        $stmt = $conn->prepare("SELECT COUNT(*) FROM historial 
                               WHERE id = ? AND estado = 'completed'");
        $stmt->execute([$id]);
        $isFirstDeposit = ($stmt->fetchColumn() == 0);
        
        // Обновляем статус транзакции
        $stmt = $conn->prepare("UPDATE historial SET estado = ? 
                               WHERE transacción_number = ?");
        $stmt->execute([$estado, $transaccion_number]);
        
        if ($estado === 'completed') {
            $bonusPercent = 0;
            $bonusAmount = 0;
            
            // Если первое пополнение, вычисляем бонус
            if ($isFirstDeposit) {
                $countryConfig = $depositConfig[$country] ?? $depositConfig['default'];
                
                // Ищем сумму в массиве для определения бонуса
                $index = array_search($monto, $countryConfig['amounts']);
                if ($index !== false && isset($countryConfig['bonuses'][$index])) {
                    $bonusPercent = $countryConfig['bonuses'][$index];
                    $bonusAmount = $monto * ($bonusPercent / 100);
                }
            }
            
            // Обновляем баланс пользователя (основная сумма + бонус)
            $new_deposit = $current_deposit + $monto + $bonusAmount;
            $stmt = $conn->prepare("UPDATE users SET deposit = ? WHERE user_id = ?");
            $stmt->execute([$new_deposit, $user_id]);
            $stmt2 = $conn->prepare("SELECT ref FROM users WHERE user_id = :userId");
            $stmt2->execute([':userId' => $user_id]);
            $user_info = $stmt2->fetch(PDO::FETCH_ASSOC);
            $ref = $user_info['ref'] ?? '';
            if (strlen($ref) > 3) {
                $custom_url = "https://app.chatterfy.ai/api/bots/webhooks/7c29d89d-5a76-4763-8a6b-c285cb17f976/updateDialog?chatId=$ref&fields.dep_status=1&stepId=f3073ccd-06de-42e7-a81b-a732fb7b3625"; // Замените на нужный URL
                $custom_response = file_get_contents($custom_url);
            }
            // Формируем сообщение о бонусе
            $bonusMsg = $bonusPercent > 0 ? 
                " (+{$bonusPercent}% бонус: {$bonusAmount})" : "";
        }
        
        $conn->commit();
        
        echo "Транзакция {$transaccion_number} обновлена: {$estado}";
        if ($estado === 'completed') {
            echo ". Баланс пополнен на: {$monto}{$bonusMsg}. Новый баланс: {$new_deposit}";
        }
        
    } catch(PDOException $e) {
        $conn->rollBack();
        echo "Ошибка базы данных: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    } catch(Exception $e) {
        $conn->rollBack();
        echo "Ошибка: " . $e->getMessage();
        error_log("Error: " . $e->getMessage());
    }
} else {
    echo "Необходимы параметры: transaccion и estado";
}
?>