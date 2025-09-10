<?php
// Включим подробное логирование ошибок
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Буферизация вывода для предотвращения случайных данных перед заголовками
ob_start();

// Функция отладки
function debugLog($message, $data = null) {
    $logFile = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    if ($data !== null) {
        $logMessage .= ": " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    $logMessage .= "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

debugLog("=== Начало выполнения скрипта ===");
debugLog("REQUEST_METHOD", $_SERVER['REQUEST_METHOD']);
debugLog("POST данные", $_POST);
debugLog("FILES данные", array_map(function($file) {
    return [
        'name' => $file['name'],
        'type' => $file['type'],
        'size' => $file['size'],
        'error' => $file['error']
    ];
}, $_FILES));

require_once 'db.php';

function getExchangeRates() {
    debugLog("Получение курсов валют - начало");
    
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
        
        debugLog("Курсы валют успешно получены", $filteredRates);
        
        // Формируем массив в том же формате
        return [
            'rates' => $filteredRates,
            'last_update' => date('Y-m-d H:i:s'),
            'base_currency' => 'USD'
        ];
    } catch (Exception $e) {
        debugLog("Ошибка получения курсов валют", $e->getMessage());
        return ['error' => 'Processing error: ' . $e->getMessage()];
    }
}

// Проверка подключения к БД
debugLog("Проверка подключения к БД");
if (!$conn) {
    debugLog("ОШИБКА: Подключение к БД не удалось");
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}
debugLog("Подключение к БД успешно");

// Убедимся, что заголовки еще не отправлены
if (headers_sent()) {
    debugLog("ОШИБКА: Заголовки уже отправлены");
    error_log("Headers already sent, output started somewhere");
    die(json_encode(['success' => false, 'error' => 'Headers already sent']));
}

// Устанавливаем заголовок JSON
header('Content-Type: application/json; charset=utf-8');
debugLog("JSON заголовок установлен");

class TelegramBot {
    private $botToken;
    private $chatId;
    
    public function __construct($botToken, $chatId) {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        debugLog("TelegramBot инициализирован", ['chatId' => $chatId]);
    }
    
    public function sendPhotoWithText($photoPath, $text, $currency) {
        debugLog("Отправка фото в Telegram - начало", [
            'photoPath' => $photoPath,
            'currency' => $currency,
            'photoExists' => file_exists($photoPath)
        ]);
        
        $url = "https://api.telegram.org/bot" . $this->botToken . "/sendPhoto";
        
        if ($currency == 'Q' || $currency == 'COP' || $currency == 'PYG'){
            $chat_id_co = '-1002909289551';
            $data = [
                'chat_id' => $chat_id_co,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
            debugLog("Используется специальный chat_id для валюты", $currency);
        }
        else if($currency == 'CLP' || $currency == 'USD' || $currency == 'HNL'){
            $chat_id_co = '-1002909289551';
            $data = [
                'chat_id' => $chat_id_co,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
            debugLog("Используется специальный chat_id для валюты", $currency);
        }
        else{
            $data = [
                'chat_id' => $this->chatId,
                'photo' => new CURLFile(realpath($photoPath)),
                'caption' => $text,
                'parse_mode' => 'HTML'
            ];
            debugLog("Используется стандартный chat_id");
        }
        
        return $this->sendRequest($url, $data);
    }
    
    private function sendRequest($url, $data) {
        debugLog("Отправка запроса в Telegram API", ['url' => $url]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            debugLog("CURL ошибка", $error);
            curl_close($ch);
            throw new Exception('Telegram API error: ' . $error);
        }
        
        curl_close($ch);
        
        debugLog("Ответ от Telegram API", [
            'httpCode' => $httpCode,
            'response' => $response
        ]);
        
        return json_decode($response, true);
    }
}

// Configuration
$botToken = '8468171708:AAFKFJtEGUb-RW2DdiMiU8hNZ_pkffVZSPI';
$chatId = '-1002909289551';

debugLog("Конфигурация бота", ['botToken' => substr($botToken, 0, 10) . '...', 'chatId' => $chatId]);

// Improved directory handling
$baseDir = dirname(__DIR__); // Parent directory
$websiteRoot = $_SERVER['DOCUMENT_ROOT'] ?: '/var/www/valor-games.com';
$targetDir = $websiteRoot . '/images/checks/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];

debugLog("Пути директорий", [
    'baseDir' => $baseDir,
    'websiteRoot' => $websiteRoot,
    'targetDir' => $targetDir
]);

try {
    debugLog("=== Начало основной обработки ===");
    
    // Проверка загруженного файла
    if (!isset($_FILES['check_image'])) {
        debugLog("ОШИБКА: Файл не загружен");
        throw new Exception('No file uploaded');
    }
    debugLog("Файл найден в запросе");

    // Получаем данные из POSTs
    $monto = floatval($_POST['amount'] ?? 0);
    $userId = $_POST['user_id'] ?? 'anonimo';
    $currency = $_POST['currency'] ?? 'USD';
    $payment_method = $_POST['payment_method'] ?? 'Transferencia bancaria';
    
    debugLog("Данные из POST", [
        'monto' => $monto,
        'userId' => $userId,
        'currency' => $currency,
        'payment_method' => $payment_method
    ]);
    
    // Валидация суммы
    if ($monto <= 0) {
        debugLog("ОШИБКА: Некорректная сумма", $monto);
        throw new Exception('Invalid amount');
    }
    debugLog("Сумма валидна");

    // Получаем курсы валют
    debugLog("Получение курсов валют");
    $exchangeRates = getExchangeRates();
    if (isset($exchangeRates['error'])) {
        debugLog("ОШИБКА: Не удалось получить курсы валют", $exchangeRates['error']);
        throw new Exception('Failed to get exchange rates: ' . $exchangeRates['error']);
    }

    // Расчет суммы в USD
    $exchangeRate = $exchangeRates['rates'][$currency] ?? 1.0;
    $amount_usd = $monto / $exchangeRate;
    $amount_usd = round($amount_usd, 2);

    debugLog("Расчет USD", [
        'exchangeRate' => $exchangeRate,
        'amount_usd' => $amount_usd
    ]);

    if ($amount_usd <= 0) {
        debugLog("ОШИБКА: Некорректная сумма в USD", $amount_usd);
        throw new Exception("Invalid USD amount calculation: $amount_usd");
    }

    // Обработка файла
    $file = $_FILES['check_image'];
    debugLog("Проверка типа файла", [
        'fileType' => $file['type'],
        'allowedTypes' => $allowedTypes
    ]);
    
    if (!in_array($file['type'], $allowedTypes)) {
        debugLog("ОШИБКА: Недопустимый тип файла", $file['type']);
        throw new Exception('Invalid file type: ' . $file['type']);
    }

    // Improved directory creation with better error handling
    debugLog("Проверка существования целевой директории", $targetDir);
    if (!file_exists($targetDir)) {
        debugLog("Директория не существует, создаем");
        // Check if parent directory exists first
        $parentDir = dirname($targetDir);
        if (!file_exists($parentDir)) {
            debugLog("Создание родительской директории", $parentDir);
            if (!mkdir($parentDir, 0755, true)) {
                debugLog("ОШИБКА: Не удалось создать родительскую директорию");
                throw new Exception('Failed to create parent directory: ' . $parentDir . '. Error: ' . error_get_last()['message']);
            }
        }
        
        // Now create the target directory
        if (!mkdir($targetDir, 0755, true)) {
            debugLog("ОШИБКА: Не удалось создать целевую директорию");
            throw new Exception('Failed to create target directory: ' . $targetDir . '. Error: ' . error_get_last()['message']);
        }
        debugLog("Целевая директория успешно создана");
    } else {
        debugLog("Целевая директория уже существует");
    }

    // Check if directory is writable
    if (!is_writable($targetDir)) {
        debugLog("Директория не доступна для записи, попытка изменить права");
        // Try to make it writable
        if (!chmod($targetDir, 0755)) {
            debugLog("ОШИБКА: Не удалось сделать директорию доступной для записи");
            throw new Exception('Directory exists but is not writable: ' . $targetDir);
        }
        debugLog("Права на директорию успешно изменены");
    }

    // Генерируем имя файла
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        // Fallback to file type
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $extension = $mimeToExt[$file['type']] ?? 'jpg';
        debugLog("Расширение определено по MIME-типу", $extension);
    }
    
    $filename = $_SERVER['HTTP_X_FILENAME'] ?? 
               $userId . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
    $targetFile = $targetDir . $filename;

    debugLog("Информация о файле", [
        'originalName' => $file['name'],
        'extension' => $extension,
        'filename' => $filename,
        'targetFile' => $targetFile
    ]);

    // Additional check for file upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)', 
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        debugLog("ОШИБКА загрузки файла", [
            'errorCode' => $file['error'],
            'errorMessage' => $uploadErrors[$file['error']] ?? 'Unknown error'
        ]);
        throw new Exception('Upload error: ' . ($uploadErrors[$file['error']] ?? 'Unknown error'));
    }

    // Сохраняем файл
    debugLog("Сохранение файла", [
        'from' => $file['tmp_name'],
        'to' => $targetFile
    ]);
    
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        debugLog("ОШИБКА: Не удалось сохранить файл");
        throw new Exception('Failed to save file to: ' . $targetFile . '. Check permissions and disk space.');
    }

    // Verify file was actually saved
    if (!file_exists($targetFile)) {
        debugLog("ОШИБКА: Файл не был сохранен");
        throw new Exception('File was not saved properly: ' . $targetFile);
    }
    
    debugLog("Файл успешно сохранен", [
        'size' => filesize($targetFile),
        'permissions' => substr(sprintf('%o', fileperms($targetFile)), -4)
    ]);

    // Генерация номера транзакции
    $numeroTransaccion = "№" . (100000000 + random_int(0, 899999999));
    debugLog("Номер транзакции сгенерирован", $numeroTransaccion);

    debugLog("Получение информации о пользователе");
    $stmt2 = $conn->prepare("SELECT ref FROM users WHERE user_id = :userId");
    $stmt2->execute([':userId' => $userId]);
    $user_info = $stmt2->fetch(PDO::FETCH_ASSOC);
    $ref = $user_info['ref'] ?? '';
    debugLog("Информация о пользователе получена", ['ref' => $ref]);

    // Подготовка сообщения для Telegram
    $message = "🆕 <b>Nuevo cheque subido</b>\n";
    $message .= "👤 <b>Usuario:</b> {$userId}\n";
    $message .= "💰 <b>Monto:</b> {$monto} {$currency}\n";
    $message .= "🔢 <b>N° Transacción:</b> {$numeroTransaccion}\n";
    $message .= "📅 <b>Fecha:</b> " . date('d.m.Y H:i:s') . "\n";
    $message .= "📁 <b>Archivo:</b> {$filename}". "\n";
    $message .= "🧩 <b>Chat_id:</b> {$ref}\n\n";
    $message .= "Responde con + para aprobar o - para rechazar";
    
    debugLog("Сообщение для Telegram подготовлено", $message);
    
    // Отправка в Telegram
    debugLog("Отправка в Telegram");
    $bot = new TelegramBot($botToken, $chatId);
    $telegramResult = $bot->sendPhotoWithText($targetFile, $message, $currency);
    debugLog("Результат отправки в Telegram", $telegramResult);
    
    // Создание транзакции в базе данных
    debugLog("Начало транзакции в БД");
    $conn->beginTransaction();
    try {
        $sql = "INSERT INTO historial (
            user_id, 
            transacciones_data, 
            transacciones_monto, 
            estado, 
            transacción_number, 
            método_de_pago,
            amount_usd,
            stage_processed
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $params = [
            $userId,
            date('Y-m-d H:i:s'),
            $monto,
            'esperando',
            $numeroTransaccion,
            $payment_method,
            $amount_usd,
            0
        ];
        
        debugLog("Выполнение INSERT в historial", $params);
        $stmt->execute($params);
        
        $insertedId = $conn->lastInsertId();
        debugLog("Запись в БД успешно создана", ['id' => $insertedId]);
        
        $conn->commit();
        debugLog("Транзакция в БД завершена успешно");
    } catch (Exception $e) {
        debugLog("ОШИБКА в транзакции БД, откат", $e->getMessage());
        $conn->rollBack();
        throw $e;
    }

    // Формируем ответ
    $response = [
        'success' => true,
        'filename' => $filename,
        'amount_usd' => $amount_usd
    ];
    
    debugLog("Формирование успешного ответа", $response);

    // Очищаем буфер и отправляем JSON
    ob_end_clean();
    echo json_encode($response);
    debugLog("=== Скрипт завершен успешно ===");

} catch (Exception $e) {
    debugLog("ОШИБКА в основном блоке", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Очищаем буфер перед отправкой ошибки
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    debugLog("=== Скрипт завершен с ошибкой ===");
}
?>