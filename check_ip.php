<?php
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Логирование ошибок в файл
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Устанавливаем заголовок для HTML
header('Content-Type: text/html; charset=UTF-8');

try {
    // Получаем IP-адрес клиента
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Для тестирования можно задать IP вручную (закомментируйте при продакшене)
    // $ip = '186.6.0.1'; // Пример IP для Доминиканской Республики
    // $ip = '113.160.0.1'; // Пример IP для Вьетнама

    // Запрос к API для определения страны
    $url = "http://ip-api.com/json/{$ip}?fields=status,message,countryCode";
    $response = @file_get_contents($url);
    
    if ($response === false) {
        throw new Exception('Failed to fetch IP data from API');
    }

    $data = json_decode($response, true);
    
    if ($data['status'] !== 'success') {
        throw new Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
    }

    $country_code = $data['countryCode'];

    // Определяем скрипт Tawk.to для включения
    $script_tag1 = <<<EOD
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/688a8df876e60e193577aeab/1j1ejl5c7';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
EOD;


$script_tag2 = <<<EOD
<script src="https://livechatv2.chat2desk.com/packs/ie-11-support.js"></script>
<script>
  window.chat24_token = "0f7f5dc5cec44ea9b6e7fe014e4f4af2";
  window.chat24_url = "https://livechatv2.chat2desk.com";
  window.chat24_socket_url ="wss://livechatv2.chat2desk.com/widget_ws_new";
  window.chat24_static_files_domain = "https://storage.chat2desk.com/";
  window.lang = "ru";
  window.fetch("".concat(window.chat24_url, "/packs/manifest.json?nocache=").concat(new Date().getTime())).then(function (res) {
    return res.json();
  }).then(function (data) {
    var chat24 = document.createElement("script");
    chat24.type = "text/javascript";
    chat24.async = true;
    chat24.src = "".concat(window.chat24_url).concat(data["application.js"]);
    document.body.appendChild(chat24);
  });
</script>
EOD;

    // Начинаем HTML-страницу
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IP Country Check</title>
        <?php
        // Проверяем, является ли страна Доминиканской Республикой (DO) или Вьетнамом (VN)
        if (in_array($country_code, ['DO', 'VN'])) {
            // Включаем скрипт для Доминиканской Республики или Вьетнама
            echo $script_tag1;
        } else {
            // Включаем скрипт для других стран
            echo $script_tag2;
        }
        ?>
    </head>
    <body>
        <?php
        if (in_array($country_code, ['DO', 'VN'])) {
            // HTML-контент для Доминиканской Республики или Вьетнама
            ?>
            <h1>Welcome from Dominican Republic or Vietnam!</h1>
            <p>Country Code: <?php echo htmlspecialchars($country_code); ?></p>
            <p>IP: <?php echo htmlspecialchars($ip); ?></p>
            <!-- ВСТАВЬТЕ ДОПОЛНИТЕЛЬНЫЙ HTML-КОД ЗДЕСЬ -->
            <?php
        } else {
            // HTML-контент для других стран
            ?>
            <h1>Welcome from another country!</h1>
            <p>Country Code: <?php echo htmlspecialchars($country_code); ?></p>
            <p>IP: <?php echo htmlspecialchars($ip); ?></p>
            <!-- ВСТАВЬТЕ ДОПОЛНИТЕЛЬНЫЙ HTML-КОД ЗДЕСЬ -->
            <?php
        }
        ?>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    // Выводим ошибку в HTML и записываем в лог
    $error_message = 'Error: ' . htmlspecialchars($e->getMessage()) . ' in ' . htmlspecialchars($e->getFile()) . ' on line ' . $e->getLine();
    error_log($error_message);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
    </head>
    <body>
        <h1>Error</h1>
        <p><?php echo $error_message; ?></p>
    </body>
    </html>
    <?php
}
?>