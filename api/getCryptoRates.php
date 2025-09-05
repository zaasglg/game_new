<?php
header('Content-Type: application/json');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,tether&vs_currencies=usd');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response ?: json_encode(['error' => 'Failed to fetch crypto rates']);
?>