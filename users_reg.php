<?php
// Параметры подключения к базе данных
$host = '111.90.156.60';
$dbname = 'panelhos_dbvalor';
$username = 'panelhos_root2';
$password = 'fO2~lJf=4]-H';
$port = 3306;

$socket = '/var/run/mysqld/mysqld.sock'; // Уточните путь на сервере
$report_file = '/tmp/stats_reg.txt'; // Абсолютный путь для надёжности

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
            error_log("Подключено к MySQL через сокет $socket");
            return $conn;
        } catch (PDOException $e) {
            error_log("Попытка подключения через сокет $attempt не удалась: " . $e->getMessage());
            // Пробуем через TCP/IP
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                $conn = new PDO($dsn, $username, $password, $options);
                error_log("Подключено к MySQL по адресу $host:$port");
                return $conn;
            } catch (PDOException $e) {
                error_log("Попытка подключения через TCP $attempt не удалась: " . $e->getMessage());
            }
            echo "Попытка $attempt не удалась, повтор через $delay секунд\n";
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

    // Запрос для получения id и даты из таблицы users, группировка по дате
    $sql_users = "
        SELECT u.id, DATE(u.registration_date) as reg_date
        FROM users u
        WHERE u.deposit > 0 
        AND u.nombre IS NOT NULL 
        AND u.nombre != ''
    ";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->execute();
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    $total_deposit_by_date = [];

    foreach ($users as $user) {
        $id = $user['id'];
        $reg_date = $user['reg_date'];

        // Запрос для суммирования amount_usd из таблицы historial
        $sql_history = "
            SELECT SUM(amount_usd) as total_amount
            FROM historial 
            WHERE user_id = :user_id 
            AND estado = 'completed'
        ";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bindParam(':user_id', $user_id);
        $stmt_history->execute();
        $result = $stmt_history->fetch(PDO::FETCH_ASSOC);

        $total_amount = $result['total_amount'] ?: 0; // Если сумма пустая, ставим 0

        // Группировка по дате регистрации
        if (!isset($total_deposit_by_date[$reg_date])) {
            $total_deposit_by_date[$reg_date] = 0;
        }
        $total_deposit_by_date[$reg_date] += $total_amount;
    }

    // Вывод депозитов по датам
    echo "Депозиты\n";
    $report_content = "Депозиты\n";
    foreach ($total_deposit_by_date as $date => $amount) {
        // Форматирование суммы с двумя десятичными знаками
        $formatted_amount = number_format($amount, 2, ',', ' ') . ' USD';
        echo "$date - $formatted_amount\n";
        $report_content .= "$date - $formatted_amount\n";
    }

    // Запись в файл отчёта
    file_put_contents($report_file, $report_content);

} catch (Exception $e) {
    error_log("Ошибка: " . $e->getMessage());
    echo "Произошла ошибка: " . $e->getMessage();
}

// Закрытие подключения
$conn = null;
?>