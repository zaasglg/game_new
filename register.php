<?php
session_start(); // Запуск сессии

// Подключение к базе данных
require 'db.php';
// Подключаем файл с функциями UTM
require_once 'utm_r_tracker.php';

// Получаем UTM-метку r
$utm_r = getUtmR();
$ref = htmlspecialchars($utm_r);
// Функция для генерации user_id
function generateUserId($last_id) {
    // Получение последнего ID
    
    // Генерация случайного числа от 2 до 50
    $random_number = rand(2, 20);
    
    // Новый ID
    $new_id = $last_id + $random_number;
    return $new_id;
}

// Получение данных из формы
if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['country'])) {
    echo json_encode(["success" => false, "message" => "Не все данные были отправлены."]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password']; // Хранится в открытом виде
$country = $_POST['country']; // Получаем выбранную страну

try {
    // Проверка, существует ли пользователь с таким email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Если пользователь существует, возвращаем ошибку
        echo json_encode(["success" => false, "message" => "Ya existe un usuario con este email. Inicia sesión."]);
    } else {
        // Генерация уникального user_id
        do {
            $stmt = $conn->prepare("SELECT MAX(user_id) AS max_id FROM users");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $last_id = $row['max_id'] ? $row['max_id'] : 0;
            
            $user_id = generateUserId($last_id); // Генерация user_id
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        } while ($existing_user); // Повторяем, пока не найдем уникальный user_id

        // Если пользователь не существует, создаем нового
        $stmt = $conn->prepare("INSERT INTO users (user_id, email, password, country, ref) VALUES (:user_id, :email, :password, :country, :ref)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':email' => $email,
            ':password' => $password, // Пароль без хеширования (ОПАСНО)
            ':country' => $country, 
            ':ref' => $ref, 
        ]);
        $botToken = '8076543915:AAHb5upyRzmAL5kEeE833wKg4HLFNouROzc'; // Токен вашего бота
        $chatId = '-1002585150746'; // ID чата или пользователя
        $country_flags = [
            'Paraguay' => '🇵🇾',
            'Colombia' => '🇨🇴',
            'Ecuador' => '🇪🇨',
            'Argentina' => '🇦🇷',
            'Bolivia' => '🇧🇴',
            'Brazil' => '🇧🇷',
            'Chile' => '🇨🇱',
            'Costa Rica' => '🇨🇷',
            'Cuba' => '🇨🇺',
            'Dominican Republic' => '🇩🇴',
            'El Salvador' => '🇸🇻',
            'Guatemala' => '🇬🇹',
            'Honduras' => '🇭🇳',
            'Mexico' => '🇲🇽',
            'Nicaragua' => '🇳🇮',
            'Panama' => '🇵🇦',
            'Peru' => '🇵🇪',
            'Uruguay' => '🇺🇾',
            'Venezuela' => '🇻🇪'
        ];
        
        // Определяем эмодзи для страны (по умолчанию 🌍, если страна не в списке)
        $flag = isset($country_flags[$country]) ? $country_flags[$country] : '🌍';
        $message = "✅ Рег: $user_id\n$flag Страна: $country\n\n👤 Реф: $ref";

        $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
        
       


       


        // Отправляем запрос
        $response2 = file_get_contents($url);

        $custom_url = "https://app.chatterfy.ai/api/bots/webhooks/30bbfa2b-4f08-447a-b13f-14221c7c99e8/updateDialog?chatId=$ref&fields.reg_status=1&fields.user_id=$user_id&fields.country=$country&stepId=e27913f8-081c-4927-8578-1d529339d5e0"; // Замените на нужный URL
        $custom_response = file_get_contents($custom_url);


        // Успешная регистрация
        $_SESSION['user_id'] = $user_id; // Сохраняем user_id в сессии
        $_SESSION['email'] = $email; // Сохраняем email в сессии
        $_SESSION['country'] = $country; // Сохраняем страну в сессии (опционально)
        echo json_encode(["success" => true, "message" => "Inscripción realizada con éxito"]);
    }
} catch (PDOException $e) {
    // Ошибка сервера
    echo json_encode(["success" => false, "message" => "Ошибка сервера: " . $e->getMessage()]);
}
?>
