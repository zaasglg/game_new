<?php
require_once 'db.php';
require_once 'stage_balance_updater.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Простое логирование
file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " Received: " . $input . "\n", FILE_APPEND);

if (!$update || !isset($update['message'])) {
    exit;
}

$message = $update['message'];
$text = $message['text'] ?? '';
$chatId = $message['chat']['id'];

if ($text === '+' || $text === '-') {
    $replyToMessage = $message['reply_to_message'] ?? null;
    
    if ($replyToMessage && isset($replyToMessage['caption'])) {
        $caption = $replyToMessage['caption'];
        
        if (preg_match('/N° Transacción:\s*(№\d+)/', $caption, $matches)) {
            $transactionNumber = $matches[1];
            $newStatus = ($text === '+') ? 'completed' : 'declined';
            
            try {
                // Получаем данные транзакции
                $txStmt = $conn->prepare("SELECT user_id, amount_usd FROM historial WHERE transacción_number = ?");
                $txStmt->execute([$transactionNumber]);
                $transaction = $txStmt->fetch(PDO::FETCH_ASSOC);
                
                // Обновляем статус
                $stmt = $conn->prepare("UPDATE historial SET estado = ? WHERE transacción_number = ?");
                $result = $stmt->execute([$newStatus, $transactionNumber]);
                
                if ($result && $stmt->rowCount() > 0) {
                    $confirmText = ($text === '+') ? "✅ Транзакция $transactionNumber одобрена" : "❌ Транзакция $transactionNumber отклонена";
                    
                    // Если одобрено и транзакция найдена
                    if ($text === '+' && $transaction) {
                        $userId = $transaction['user_id'];
                        
                        // Получаем полные данные транзакции
                        $txStmt = $conn->prepare("SELECT transacciones_monto, currency FROM historial WHERE transacción_number = ?");
                        $txStmt->execute([$transactionNumber]);
                        $txData = $txStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($txData) {
                            $amount = $txData['transacciones_monto'];
                            $currency = $txData['currency'];
                            
                            // Пополняем баланс в оригинальной валюте
                            $balanceStmt = $conn->prepare("UPDATE users SET deposit = deposit + ? WHERE user_id = ?");
                            $balanceUpdated = $balanceStmt->execute([$amount, $userId]);
                            
                            // Баланс пополнен, но не показываем сумму в Telegram
                        }
                    }
                    
                    sendTelegramMessage($chatId, $confirmText);
                } else {
                    sendTelegramMessage($chatId, "Транзакция $transactionNumber не найдена");
                }
            } catch (Exception $e) {
                sendTelegramMessage($chatId, "Ошибка: " . $e->getMessage());
            }
        }
    }
}

function sendTelegramMessage($chatId, $text) {
    $botToken = '8468171708:AAFKFJtEGUb-RW2DdiMiU8hNZ_pkffVZSPI';
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    
    $data = ['chat_id' => $chatId, 'text' => $text];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>