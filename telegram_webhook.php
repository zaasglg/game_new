<?php
require_once 'db.php';
require_once 'stage_balance_updater.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);

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
                // Получаем данные транзакции для пополнения баланса
                $txStmt = $conn->prepare("SELECT user_id, amount_usd FROM historial WHERE transacción_number = ?");
                $txStmt->execute([$transactionNumber]);
                $transaction = $txStmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $conn->prepare("UPDATE historial SET estado = ? WHERE transacción_number = ?");
                $result = $stmt->execute([$newStatus, $transactionNumber]);
                
                if ($result) {
                    $confirmText = ($text === '+') ? "✅ Транзакция $transactionNumber одобрена" : "❌ Транзакция $transactionNumber отклонена";
                    
                    // Если транзакция одобрена, пополняем баланс пользователя
                    if ($text === '+' && $transaction) {
                        $userId = $transaction['user_id'];
                        $amount = $transaction['amount_usd'];
                        
                        // Пополняем deposit пользователя
                        $balanceStmt = $conn->prepare("UPDATE users SET deposit = deposit + ? WHERE user_id = ?");
                        $balanceUpdated = $balanceStmt->execute([$amount, $userId]);
                        
                        if ($balanceUpdated) {
                            $confirmText .= "\n💰 Баланс пользователя $userId пополнен на $$amount";
                            
                            // Запускаем обновление stage balance
                            $updater = new StageBalanceUpdater($conn);
                            $updater->updateForUser($userId);
                        }
                    }
                    
                    sendTelegramMessage($chatId, $confirmText);
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