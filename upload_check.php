<?php
// Включим подробное логирование ошибок
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Буферизация вывода для предотвращения случайных данных перед заголовками
ob_start();

require_once 'db.php';



function getExchangeRates() {
    // Заданные вручную курсы валют
    $manualRates = [
        'ARS' => 1200.50,  // Аргентинский песо
        'BOB' => 6.91,     // Боливийский боливиано
        'BRL' => 5.45,     // Бразильский реал
        'CLP' => 933.20,   // Чилийский песо
        'COP' => 4500,     // Колумбийский песо
        'CRC' => 550,      // Костариканский колон
        'CUP' => 24.00,    // Кубинский песо
        'DOP' => 67.25,    // Доминиканский песо
        'GTQ' => 7.75,     // Гватемальский кетсаль
        'HTG' => 132.40,   // Гаитянский гурд
        'HNL' => 28.10,    // Гондурасская лемпира
        'MXN' => 19.85,    // Мексиканский песо
        'NIO' => 36.80,    // Никарагуанская кордоба
        'PYG' => 8500.60,  // Парагвайский гуарани
        'PEN' => 3.80,     // Перуанский соль
        'UYU' => 40.50,    // Уругвайский песо
        'VES' => 147.25    // Венесуэльский боливар
    ];

    // Список нужных валют
    $neededCurrencies = [
        'ARS', 'BOB', 'BRL', 'CLP', 'COP', 'CRC', 'CUP', 'DOP',
        'GTQ', 'HTG', 'HNL', 'MXN', 'NIO', 'PYG', 'PEN', 'UYU', 'VES'
    ];

    try {
        // Фильтруем только нужные валюты
        $filteredRates = array_intersect_key($manualRates, array_flip($neededCurrencies));
        
        // Формируем массив в том же формате
        return [
            'rates' => $filteredRates,
            'last_update' => date('Y-m-d H:i:s'),
            'base_currency' => 'USD'
        ];
    } catch (Exception $e) {
        return ['error' => 'Processing error: ' . $e->getMessage()];
    }
}





// Проверка подключения к БД
if (!$conn) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Убедимся, что заголовки еще не отправлены
if (headers_sent()) {
    error_log("Headers already sent, output started somewhere");
    die(json_encode(['success' => false, 'error' => 'Headers already sent']));
}

// Устанавливаем заголовок JSON
header('Content-Type: application/json; charset=utf-8');

class TelegramBot {
    private $botToken;
    private $chatId;
    
    public function __construct($botToken, $chatId) {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }
    
    public function sendPhotoWithText($photoPath, $text, $currency) {
        $url = "https://api.telegram.org/bot" . $this->botToken . "/sendPhoto";
        if ($currency == 'Q' || $currency == 'COP' || $currency == 'PYG'){
            $chat_id_co = '-1002779136482';
            $data = [
                'chat_id' => $chat_id_co,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
        }
        else if($currency == 'CLP' || $currency == 'USD' || $currency == 'HNL'){
            $chat_id_co = '-1002887508076';
            $data = [
                'chat_id' => $chat_id_co,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
        }
        else{
            $data = [
                'chat_id' => $this->chatId,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
        }
        
        
        return $this->sendRequest($url, $data);
    }
    
    private function sendRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Telegram API error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Configuration
$botToken = '8468171708:AAFKFJtEGUb-RW2DdiMiU8hNZ_pkffVZSPI';
$chatId = '-1002909289551';
$targetDir = __DIR__ . '/../images/checks/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];




try {

    // Проверка загруженного файла
    if (!isset($_FILES['check_image'])) {
        throw new Exception('No file uploaded');
    }

    // Получаем данные из POST
    $monto = floatval($_POST['amount'] ?? 0);
    $userId = $_POST['user_id'] ?? 'anonimo';
    
    $currency = $_POST['currency'] ?? 'USD';
    $payment_method = $_POST['payment_method'] ?? 'Transferencia bancaria';
    
    // Валидация суммы
    if ($monto <= 0) {
        throw new Exception('Invalid amount');
    }

    // Получаем курсы валют
   // $exchangeRates = @json_decode(file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/api/getExchangeRates.php'), true);
    //if (!$exchangeRates || isset($exchangeRates['error'])) {
     //   throw new Exception('Failed to get exchange rates: ' . ($exchangeRates['error'] ?? 'Invalid response'));
    //}
    $exchangeRates = getExchangeRates();
    if (isset($exchangeRates['error'])) {
        throw new Exception('Failed to get exchange rates: ' . $exchangeRates['error']);
    }

    // Расчет суммы в USD
    $exchangeRate = $exchangeRates['rates'][$currency] ?? 1.0;
    $amount_usd = $monto / $exchangeRate;
    $amount_usd = round($amount_usd, 2);

    if ($amount_usd <= 0) {
        throw new Exception("Invalid USD amount calculation: $amount_usd");
    }

    // Обработка файла
    $file = $_FILES['check_image'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type: ' . $file['type']);
    }

    // Создаем директорию, если не существует
    if (!file_exists($targetDir) && !mkdir($targetDir, 0777, true)) {
        throw new Exception('Failed to create directory');
    }

    // Генерируем имя файла
    $filename = $_SERVER['HTTP_X_FILENAME'] ?? 
               $userId . '_' . date('Y-m-d_H-i-s') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $filename;

    // Сохраняем файл
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        throw new Exception('Failed to save file. Check permissions.');
    }

    // Генерация номера транзакции
    $numeroTransaccion = "№" . (100000000 + random_int(0, 899999999));

    $stmt2 = $conn->prepare("SELECT ref FROM users WHERE user_id = :userId");
    $stmt2->execute([':userId' => $userId]);
    $user_info = $stmt2->fetch(PDO::FETCH_ASSOC);
    $ref = $user_info['ref'] ?? '';

    // Подготовка сообщения для Telegram (закомментировано)
    $message = "🆕 <b>Nuevo cheque subido</b>\n";
    $message .= "👤 <b>Usuario:</b> {$userId}\n";
    $message .= "💰 <b>Monto:</b> {$monto} {$currency}\n";
    $message .= "🔢 <b>N° Transacción:</b> {$numeroTransaccion}\n";
    $message .= "📅 <b>Fecha:</b> " . date('d.m.Y H:i:s') . "\n";
    $message .= "📁 <b>Archivo:</b> {$filename}". "\n";
    $message .= "🧩 <b>Chat_id:</b> {$ref}";
    
    
    
    // Отправка в Telegram - ЗАКОММЕНТИРОВАНО
    $bot = new TelegramBot($botToken, $chatId);
    $telegramResult = $bot->sendPhotoWithText($targetFile, $message,$currency);
    
    // Создание транзакции в базе данных
    $conn->beginTransaction();
    try {
        $sql = "INSERT INTO historial (
            user_id, 
            transacciones_data, 
            transacciones_monto, 
            estado, 
            transacción_number, 
            método_de_pago,
            amount_usd
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $userId,
            date('Y-m-d H:i:s'),
            $monto,
            'esperando',
            $numeroTransaccion,
            $payment_method,
            $amount_usd
        ]);
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

    // Формируем ответ
    $response = [
        'success' => true,
        'filename' => $filename,
        'amount_usd' => $amount_usd
    ];

    // Очищаем буфер и отправляем JSON
    ob_end_clean();
    echo json_encode($response);

} catch (Exception $e) {
    // Очищаем буфер перед отправкой ошибки
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>