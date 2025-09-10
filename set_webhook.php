<?php
$botToken = '8468171708:AAFKFJtEGUb-RW2DdiMiU8hNZ_pkffVZSPI';
$webhookUrl = 'https://valor-games.com/telegram_webhook.php';

$url = "https://api.telegram.org/bot$botToken/setWebhook";
$data = ['url' => $webhookUrl];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Webhook response: " . $response;
?>