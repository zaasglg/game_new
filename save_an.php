<?php


// Настройка логирования
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Настройки подключения
$host = '111.90.156.60';
$dbname = 'panelhos_dbvalor';
$username = 'panelhos_root2';
$password = 'fO2~lJf=4]-H';
$port = 3306;
$socket = '/var/run/mysqld/mysqld.sock'; // Уточните путь на сервере
$report_file = '/tmp/report.txt'; // Абсолютный путь для надёжности


// Проверка и создание директории
$report_dir = dirname($report_file);
if (!is_dir($report_dir)) {
    if (!mkdir($report_dir, 0775, true)) {
        error_log("Не удалось создать директорию: $report_dir");
        die("Произошла ошибка: Не удалось создать директорию $report_dir");
    }
}

function connect_with_retry($host, $port, $socket, $dbname, $username, $password, $retries = 5, $delay = 10) {
    $attempt = 1;
    while ($attempt <= $retries) {
        try {
            // Пробуем через сокет
            $dsn = "mysql:unix_socket=$socket;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 60,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION wait_timeout = 28800'
            ];
            $conn = new PDO($dsn, $username, $password, $options);
            echo "Подключился к базе данных через сокет $socket\n";
            error_log("Connected to MySQL via socket $socket");
            return $conn;
        } catch (PDOException $e) {
            error_log("Socket attempt $attempt failed: " . $e->getMessage());
            // Пробуем через TCP/IP
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                $conn = new PDO($dsn, $username, $password, $options);
                echo "Подключился к базе данных по адресу $host:$port\n";
                error_log("Connected to MySQL at $host:$port");
                return $conn;
            } catch (PDOException $e) {
                error_log("TCP attempt $attempt failed: " . $e->getMessage());
            }
            echo "Попытка $attempt не удалась, повтор через $delay сек\n";
            if ($attempt == $retries) {
                throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
            }
            sleep($delay);
            $attempt++;
        }
    }
}

try {
    // Подключение
    $conn = connect_with_retry($host, $port, $socket, $dbname, $username, $password);

    // Получение пользователей
    echo "Получил список пользователей\n";
    error_log("Fetching users");
    $stmt = $conn->query("SELECT user_id, country, registration_date FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получение транзакций
    echo "Получил транзакции\n";
    error_log("Fetching transactions");
    $stmt = $conn->query("
        SELECT user_id, transacciones_data, amount_usd 
        FROM historial 
        WHERE estado = 'completed'
        ORDER BY user_id, transacciones_data
        LIMIT 2000
    ");
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Группировка транзакций по user_id
    $transactions_by_user = [];
    foreach ($transactions as $t) {
        $user_id = $t['user_id'];
        if (!isset($transactions_by_user[$user_id])) {
            $transactions_by_user[$user_id] = [];
        }
        $transactions_by_user[$user_id][] = [
            'date' => $t['transacciones_data'],
            'amount' => $t['amount_usd']
        ];
    }

    // Запись отчёта
    echo "Пошла запись\n";
    error_log("Writing report to file");
    $file = @fopen($report_file, 'w');
    if (!$file) {
        $error = error_get_last();
        error_log("Не удалось открыть $report_file для записи: " . ($error['message'] ?? 'Неизвестная ошибка'));
        throw new Exception("Не удалось открыть $report_file для записи");
    }
    fwrite($file, "=== Отчёт по пользователям ===\n");
    foreach ($users as $user) {
        $user_id = $user['user_id'];
        fwrite($file, "user_id: $user_id\n");
        fwrite($file, "country: {$user['country']}\n");
        fwrite($file, "registration_date: {$user['registration_date']}\n");
        
        // Вывод до 4 транзакций
        $user_transactions = $transactions_by_user[$user_id] ?? [];
        foreach (array_slice($user_transactions, 0, 4) as $i => $t) {
            fwrite($file, sprintf("%d-ая транзакция: %s %s USD\n", $i + 1, $t['date'], $t['amount']));
        }
        fwrite($file, "\n");
    }

    // Статистика
    fwrite($file, "=== Статистика ===\n");
    $total_users = count($users);
    fwrite($file, "Всего юзеров: $total_users\n");

    // Подсчёт пользователей по количеству транзакций
    $transaction_counts = [1 => 0, 2 => 0, '3+' => 0];
    foreach ($transactions_by_user as $user_id => $trans) {
        $count = count($trans);
        if ($count == 1) {
            $transaction_counts[1]++;
        } elseif ($count == 2) {
            $transaction_counts[2]++;
        } else {
            $transaction_counts['3+']++;
        }
    }
    fwrite($file, "Юзер имеет 1 транзакцию: {$transaction_counts[1]}\n");
    fwrite($file, "Юзер имеет 2 транзакции: {$transaction_counts[2]}\n");
    fwrite($file, "Юзер имеет 3 транзакции и больше: {$transaction_counts['3+']}\n");

    // Подсчёт транзакций по дням
    $days_transactions = array_fill(1, 30, 0);
    foreach ($users as $user) {
        $user_id = $user['user_id'];
        $reg_date = new DateTime($user['registration_date']);
        foreach ($transactions_by_user[$user_id] ?? [] as $t) {
            $trans_date = new DateTime($t['date']);
            $days_diff = $trans_date->diff($reg_date)->days + 1;
            if ($days_diff >= 1 && $days_diff <= 30) {
                $days_transactions[$days_diff]++;
            }
        }
    }
    for ($day = 1; $day <= 30; $day++) {
        fwrite($file, "Человек сделал транзакцию на $day-ой день: {$days_transactions[$day]} человек\n");
    }
    fclose($file);
    error_log("Report successfully written to $report_file");

} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    echo "Произошла ошибка: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn = null;
        echo "Соединение с базой данных закрыто\n";
        error_log("Connection closed");
    }
}


?>