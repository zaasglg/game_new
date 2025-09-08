<?php
require_once 'db.php';

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
                $stmt = $conn->prepare("UPDATE historial SET estado = ? WHERE transacción_number = ?");
                $result = $stmt->execute([$newStatus, $transactionNumber]);
                
                if ($result) {
                    $confirmText = ($text === '+') ? "✅ Транзакция $transactionNumber одобрена" : "❌ Транзакция $transactionNumber отклонена";
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