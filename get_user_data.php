<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'auth_check.php';

$response = [
    'success' => false,
    'data' => []
];

if(AUTH && defined('UID') && UID) {
    $response = [
        'success' => true,
        'data' => [
            'user_id' => UID,
            'balance' => SYS_BALANCE,
            'currency' => SYS_CURRENCY,
            'country' => SYS_COUNTRY,
            'is_auth' => true
        ]
    ];
} else {
    $response = [
        'success' => true,
        'data' => [
            'user_id' => 'demo',
            'balance' => 500,
            'currency' => 'USD',
            'country' => '',
            'is_auth' => false
        ]
    ];
}

echo json_encode($response);
?>
