<?php
session_start();

// Включение отображения всех ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Конфигурация безопасности
define('ADMIN_PASSWORD', 'Admin Login'); // В реальном проекте используйте хешированный пароль

// Проверка авторизации администратора
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_last_activity'] = time();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            showLoginForm("Неверный пароль");
            exit;
        }
    } else {
        showLoginForm();
        exit;
    }
}

// Проверка времени бездействия (30 минут)
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    showLoginForm("Сессия истекла. Пожалуйста, войдите снова.");
    exit;
}
$_SESSION['admin_last_activity'] = time();

// Функция отображения формы входа
function showLoginForm($error = null) {
    $errorHtml = $error ? '<div class="error-message">'.$error.'</div>' : '';
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/admin_payment.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        {$errorHtml}
        <form method="POST">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
HTML;
    exit;
}

// Путь к JSON-файлу (абсолютный путь)
$jsonFile = __DIR__ . '/payment_config.json';

// Проверяем и создаем файл если не существует
if (!file_exists($jsonFile)) {
    $defaultConfig = [
        "universalPaymentMethods" => [],
        "paymentButtonsByCountry" => []
    ];
    $result = file_put_contents($jsonFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
    if ($result === false) {
        die("Не удалось создать файл конфигурации. Проверьте права доступа к папке.");
    }
}

// Проверяем права на запись в файл
if (!is_writable($jsonFile)) {
    die("Файл конфигурации недоступен для записи. Проверьте права доступа.");
}

// Чтение данных из JSON
$config = json_decode(file_get_contents($jsonFile), true);
if ($config === null) {
    die("Ошибка при чтении файла JSON. Проверьте его содержимое.");
}

// Создаем папку для загрузки изображений, если ее нет
$uploadDir = __DIR__ . '/images/payments/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die("Не удалось создать папку для изображений. Проверьте права доступа.");
    }
}

// Проверяем права на запись в папку для изображений
if (!is_writable($uploadDir)) {
    die("Папка для изображений недоступна для записи. Проверьте права доступа.");
}

// Функция для загрузки изображения (упрощенная версия)
function uploadImage($file, $uploadDir) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Проверяем расширение файла
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return 'images/payments/' . $fileName;
            }
        }
    }
    return null;
}

// Обработка форм
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_method'])) {
        $country = $_POST['country'] === 'universal' ? 'universal' : $_POST['country'];
        
        // Обработка загрузки изображения
        $imagePath = $_POST['src'] ?? ''; // По умолчанию используем URL из поля src

        // Если выбрана загрузка файла и файл загружен
        if (isset($_POST['image_source']) && $_POST['image_source'] === 'upload' && 
            isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['image_upload'], $uploadDir);
            if ($uploadedPath) {
                $imagePath = $uploadedPath;
            } else {
                $_SESSION['message'] = "Ошибка при загрузке изображения";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit;
            }
        }
        
        $methodData = [
            'alt' => $_POST['alt'] ?? '',
            'src' => $imagePath,
            'label' => $_POST['label'] ?? '',
            'numero_de_cuenta' => $_POST['numero_de_cuenta'] ?? '',
            'nombre' => $_POST['nombre'] ?? '',
            'banco' => $_POST['banco'] ?? '',
            'ci' => $_POST['ci'] ?? '',
            'cryptoId' => $_POST['cryptoId'] ?? '',
            'cuit' => $_POST['cuit'] ?? '',
            'cbu' => $_POST['cbu'] ?? '',
            'nro' => $_POST['nro'] ?? '',
            'sinpe' => $_POST['sinpe'] ?? '',
            'qr' => $_POST['qr'] ?? '',
            'bac' => $_POST['bac'] ?? '',
            'cta' => $_POST['cta'] ?? '',
            'cedula' => $_POST['cedula'] ?? '',
            'IBAN' => $_POST['IBAN'] ?? '',
            'Identificación' => $_POST['Identificación'] ?? '',
            'Rut' => $_POST['Rut'] ?? '',
            'status' => $_POST['status'] ?? 'active' // Добавлен параметр status
        ];

        if ($country === 'universal') {
            $config['universalPaymentMethods'][] = $methodData;
        } else {
            if (!isset($config['paymentButtonsByCountry'][$country])) {
                $config['paymentButtonsByCountry'][$country] = [];
            }
            $config['paymentButtonsByCountry'][$country][] = $methodData;
        }

        // Сохраняем изменения с проверкой
        $jsonData = json_encode($config, JSON_PRETTY_PRINT);
        if ($jsonData === false) {
            $_SESSION['message'] = "Ошибка при формировании JSON данных: " . json_last_error_msg();
        } else {
            $result = file_put_contents($jsonFile, $jsonData);
            if ($result === false) {
                $_SESSION['message'] = "Ошибка при записи в файл. Проверьте права доступа.";
            } else {
                $_SESSION['message'] = "Метод оплаты успешно добавлен";
            }
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['update_method'])) {
        $type = $_POST['type'];
        $index = (int)$_POST['index'];
        
        // Обработка загрузки изображения
        $imagePath = $_POST['src'] ?? ''; // По умолчанию используем старое значение
        
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['image_upload'], $uploadDir);
            if ($uploadedPath) {
                $imagePath = $uploadedPath;
            } else {
                $_SESSION['message'] = "Ошибка при загрузке изображения";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit;
            }
        }
        
        $updatedMethod = [
            'alt' => $_POST['alt'] ?? '',
            'src' => $imagePath,
            'label' => $_POST['label'] ?? '',
            'numero_de_cuenta' => $_POST['numero_de_cuenta'] ?? '',
            'nombre' => $_POST['nombre'] ?? '',
            'banco' => $_POST['banco'] ?? '',
            'ci' => $_POST['ci'] ?? '',
            'cryptoId' => $_POST['cryptoId'] ?? '',
            'cuit' => $_POST['cuit'] ?? '',
            'cbu' => $_POST['cbu'] ?? '',
            'nro' => $_POST['nro'] ?? '',
            'sinpe' => $_POST['sinpe'] ?? '',
            'qr' => $_POST['qr'] ?? '',
            'bac' => $_POST['bac'] ?? '',
            'cta' => $_POST['cta'] ?? '',
            'cedula' => $_POST['cedula'] ?? '',
            'IBAN' => $_POST['IBAN'] ?? '',
            'Identificación' => $_POST['Identificación'] ?? '',
            'Rut' => $_POST['Rut'] ?? '',
            'status' => $_POST['status'] ?? 'active' // Добавлен параметр status
        ];

        if ($type === 'universal') {
            if (isset($config['universalPaymentMethods'][$index])) {
                $config['universalPaymentMethods'][$index] = $updatedMethod;
            }
        } else {
            $country = $_POST['country'] ?? '';
            if (isset($config['paymentButtonsByCountry'][$country][$index])) {
                $config['paymentButtonsByCountry'][$country][$index] = $updatedMethod;
            }
        }

        // Сохраняем изменения с проверкой
        $jsonData = json_encode($config, JSON_PRETTY_PRINT);
        if ($jsonData === false) {
            $_SESSION['message'] = "Ошибка при формировании JSON данных: " . json_last_error_msg();
        } else {
            $result = file_put_contents($jsonFile, $jsonData);
            if ($result === false) {
                $_SESSION['message'] = "Ошибка при записи в файл. Проверьте права доступа.";
            } else {
                $_SESSION['message'] = "Метод оплаты успешно обновлен";
            }
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_method'])) {
        $type = $_POST['type'];
        if ($type === 'universal') {
            $index = (int)$_POST['index'];
            if (isset($config['universalPaymentMethods'][$index])) {
                array_splice($config['universalPaymentMethods'], $index, 1);
            }
        } else {
            $country = $_POST['country'] ?? '';
            $index = (int)$_POST['index'];
            if (isset($config['paymentButtonsByCountry'][$country][$index])) {
                array_splice($config['paymentButtonsByCountry'][$country], $index, 1);
            }
        }
        
        // Сохраняем изменения с проверкой
        $jsonData = json_encode($config, JSON_PRETTY_PRINT);
        if ($jsonData === false) {
            $_SESSION['message'] = "Ошибка при формировании JSON данных: " . json_last_error_msg();
        } else {
            $result = file_put_contents($jsonFile, $jsonData);
            if ($result === false) {
                $_SESSION['message'] = "Ошибка при записи в файл. Проверьте права доступа.";
            } else {
                $_SESSION['message'] = "Метод оплаты успешно удален";
            }
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Получаем список всех стран
$allCountries = array_keys($config['paymentButtonsByCountry']);
sort($allCountries);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payment Methods</title>
    <link rel="stylesheet" href="./css/admin_payment_two.css">
    <style>
        .edit-form {
            display: none;
            width: 97%;
            background: #f9f9f9;
            padding: 20px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            clear: both;
        }
        
        .edit-form .form-group {
            margin-bottom: 15px;
        }
        .form-group input {
            width: 98%;
        }

        .edit-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .edit-form input[type="text"],
        .edit-form input[type="file"],
        .edit-form select {
            width: 98%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .edit-form .action-buttons {
            margin-top: 20px;
            text-align: right;
        }
        
        .current-image {
            max-width: 200px;
            max-height: 100px;
            display: block;
            margin-bottom: 10px;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 100px;
            margin-top: 10px;
        }
        
        .upload-options {
            margin: 15px 0;
        }
        
        .upload-option {
            margin-bottom: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 100% !important; margin: 0 !important;">
        <h1>Управление платежными методами</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab active" onclick="openTab('methods')">Платежные методы</div>
            <div class="tab" onclick="openTab('add')">Добавить метод</div>
        </div>

        <div id="methods" class="tab-content active">
            <h2>Универсальные платежные методы</h2>
            <?php if (empty($config['universalPaymentMethods'])): ?>
                <p>Нет универсальных методов оплаты</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>alt</th>
                            <th>src</th>
                            <th>label</th>
                            <th>numero_de_cuenta</th>
                            <th>Status</th>
                            <th>Дополнительные данные</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($config['universalPaymentMethods'] as $index => $method): ?>
                        <tr>
                            <td><?= htmlspecialchars($method['alt'] ?? '') ?></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($method['src'] ?? '') ?>
                            </td>
                            <td><?= htmlspecialchars($method['label'] ?? '') ?></td>
                            <td><?= htmlspecialchars($method['numero_de_cuenta'] ?? '') ?></td>
                            <td><?= htmlspecialchars($method['status'] ?? 'active') ?></td>
                            <td>
                                <ul class="details-list">
                                    <?php if (!empty($method['nombre'])): ?>
                                    <li><strong>nombre:</strong> <?= htmlspecialchars($method['nombre']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['banco'])): ?>
                                    <li><strong>banco:</strong> <?= htmlspecialchars($method['banco']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['ci'])): ?>
                                    <li><strong>ci:</strong> <?= htmlspecialchars($method['ci']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['cryptoId'])): ?>
                                    <li><strong>cryptoId:</strong> <?= htmlspecialchars($method['cryptoId']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['cuit'])): ?>
                                    <li><strong>cuit:</strong> <?= htmlspecialchars($method['cuit']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['cbu'])): ?>
                                    <li><strong>cbu:</strong> <?= htmlspecialchars($method['cbu']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['nro'])): ?>
                                    <li><strong>nro:</strong> <?= htmlspecialchars($method['nro']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['sinpe'])): ?>
                                    <li><strong>sinpe:</strong> <?= htmlspecialchars($method['sinpe']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['qr'])): ?>
                                    <li><strong>qr:</strong> <?= htmlspecialchars($method['qr']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['bac'])): ?>
                                    <li><strong>bac:</strong> <?= htmlspecialchars($method['bac']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['cta'])): ?>
                                    <li><strong>cta:</strong> <?= htmlspecialchars($method['cta']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['cedula'])): ?>
                                    <li><strong>cedula:</strong> <?= htmlspecialchars($method['cedula']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['IBAN'])): ?>
                                    <li><strong>IBAN:</strong> <?= htmlspecialchars($method['IBAN']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['Identificación'])): ?>
                                    <li><strong>Identificación:</strong> <?= htmlspecialchars($method['Identificación']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($method['Rut'])): ?>
                                    <li><strong>Rut:</strong> <?= htmlspecialchars($method['Rut']) ?></li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td>
                                <button onclick="showEditForm('universal', <?= $index ?>)" class="btn btn-edit">Изменить</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="type" value="universal">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    <button type="submit" name="delete_method" class="btn btn-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Форма редактирования теперь после строки таблицы -->
                        <div id="edit-form-universal-<?= $index ?>" class="edit-form">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="type" value="universal">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                
                                <div class="form-group">
                                    <label>alt:</label>
                                    <input type="text" name="alt" value="<?= htmlspecialchars($method['alt'] ?? '') ?>" required>
                                </div>
                                
                                <div class="image-upload-container">
                                    <label class="image-upload-label">Текущее изображение:</label>
                                    <?php if (!empty($method['src'])): ?>
                                        <img src="<?= htmlspecialchars($method['src']) ?>" alt="Current image" class="current-image">
                                    <?php else: ?>
                                        <p>Изображение отсутствует</p>
                                    <?php endif; ?>
                                    
                                    <div class="upload-options">
                                        <div class="upload-option">
                                            <label>
                                                <input type="radio" name="image_source" value="upload" checked> Загрузить изображение
                                            </label>
                                            <input type="file" name="image_upload" accept="image/*">
                                        </div>
                                        <div class="upload-option">
                                            <label>
                                                <input type="radio" name="image_source" value="url"> Указать URL изображения
                                            </label>
                                            <input type="text" name="src" value="<?= htmlspecialchars($method['src'] ?? '') ?>" placeholder="https://example.com/image.jpg">
                                        </div>
                                    </div>
                                    <img id="image-preview-universal-<?= $index ?>" class="image-preview" style="display:none;">
                                </div>
                                
                                <div class="form-group">
                                    <label>label:</label>
                                    <input type="text" name="label" value="<?= htmlspecialchars($method['label'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>numero_de_cuenta:</label>
                                    <input type="text" name="numero_de_cuenta" value="<?= htmlspecialchars($method['numero_de_cuenta'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>status:</label>
                                    <select name="status">
                                        <option value="active" <?= ($method['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="pause" <?= ($method['status'] ?? 'active') === 'pause' ? 'selected' : '' ?>>Pause</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>nombre:</label>
                                    <input type="text" name="nombre" value="<?= htmlspecialchars($method['nombre'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>banco:</label>
                                    <input type="text" name="banco" value="<?= htmlspecialchars($method['banco'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>ci:</label>
                                    <input type="text" name="ci" value="<?= htmlspecialchars($method['ci'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>cryptoId:</label>
                                    <input type="text" name="cryptoId" value="<?= htmlspecialchars($method['cryptoId'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>cuit:</label>
                                    <input type="text" name="cuit" value="<?= htmlspecialchars($method['cuit'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>cbu:</label>
                                    <input type="text" name="cbu" value="<?= htmlspecialchars($method['cbu'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>nro:</label>
                                    <input type="text" name="nro" value="<?= htmlspecialchars($method['nro'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>sinpe:</label>
                                    <input type="text" name="sinpe" value="<?= htmlspecialchars($method['sinpe'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>qr:</label>
                                    <input type="text" name="qr" value="<?= htmlspecialchars($method['qr'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>bac:</label>
                                    <input type="text" name="bac" value="<?= htmlspecialchars($method['bac'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>cta:</label>
                                    <input type="text" name="cta" value="<?= htmlspecialchars($method['cta'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>cedula:</label>
                                    <input type="text" name="cedula" value="<?= htmlspecialchars($method['cedula'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>IBAN:</label>
                                    <input type="text" name="IBAN" value="<?= htmlspecialchars($method['IBAN'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Identificación:</label>
                                    <input type="text" name="Identificación" value="<?= htmlspecialchars($method['Identificación'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Rut:</label>
                                    <input type="text" name="Rut" value="<?= htmlspecialchars($method['Rut'] ?? '') ?>">
                                </div>
                                
                                <div class="action-buttons">
                                    <button type="submit" name="update_method" class="btn btn-success">Сохранить</button>
                                    <button type="button" onclick="hideEditForm('universal', <?= $index ?>)" class="btn btn-danger">Отмена</button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php foreach ($config['paymentButtonsByCountry'] as $country => $methods): ?>
                <h2><?= htmlspecialchars($country) ?></h2>
                <?php if (empty($methods)): ?>
                    <p>Нет методов оплаты для этой страны</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>alt</th>
                                <th>src</th>
                                <th>label</th>
                                <th>numero_de_cuenta</th>
                                <th>Status</th>
                                <th>Дополнительные данные</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($methods as $index => $method): ?>
                            <tr>
                                <td><?= htmlspecialchars($method['alt'] ?? '') ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($method['src'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($method['label'] ?? '') ?></td>
                                <td><?= htmlspecialchars($method['numero_de_cuenta'] ?? '') ?></td>
                                <td><?= htmlspecialchars($method['status'] ?? 'active') ?></td>
                                <td>
                                    <ul class="details-list">
                                        <?php if (!empty($method['nombre'])): ?>
                                        <li><strong>nombre:</strong> <?= htmlspecialchars($method['nombre']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['banco'])): ?>
                                        <li><strong>banco:</strong> <?= htmlspecialchars($method['banco']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['ci'])): ?>
                                        <li><strong>ci:</strong> <?= htmlspecialchars($method['ci']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['cryptoId'])): ?>
                                        <li><strong>cryptoId:</strong> <?= htmlspecialchars($method['cryptoId']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['cuit'])): ?>
                                        <li><strong>cuit:</strong> <?= htmlspecialchars($method['cuit']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['cbu'])): ?>
                                        <li><strong>cbu:</strong> <?= htmlspecialchars($method['cbu']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['nro'])): ?>
                                        <li><strong>nro:</strong> <?= htmlspecialchars($method['nro']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['sinpe'])): ?>
                                        <li><strong>sinpe:</strong> <?= htmlspecialchars($method['sinpe']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['qr'])): ?>
                                        <li><strong>qr:</strong> <?= htmlspecialchars($method['qr']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['bac'])): ?>
                                        <li><strong>bac:</strong> <?= htmlspecialchars($method['bac']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['cta'])): ?>
                                        <li><strong>cta:</strong> <?= htmlspecialchars($method['cta']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['cedula'])): ?>
                                        <li><strong>cedula:</strong> <?= htmlspecialchars($method['cedula']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['IBAN'])): ?>
                                        <li><strong>IBAN:</strong> <?= htmlspecialchars($method['IBAN']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['Identificación'])): ?>
                                        <li><strong>Identificación:</strong> <?= htmlspecialchars($method['Identificación']) ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($method['Rut'])): ?>
                                        <li><strong>Rut:</strong> <?= htmlspecialchars($method['Rut']) ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                                <td>
                                    <button onclick="showEditForm('<?= $country ?>', <?= $index ?>)" class="btn btn-edit">Изменить</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="type" value="country">
                                        <input type="hidden" name="country" value="<?= $country ?>">
                                        <input type="hidden" name="index" value="<?= $index ?>">
                                        <button type="submit" name="delete_method" class="btn btn-danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Форма редактирования теперь после строки таблицы -->
                            <div id="edit-form-<?= $country ?>-<?= $index ?>" class="edit-form">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="country">
                                    <input type="hidden" name="country" value="<?= $country ?>">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    
                                    <div class="form-group">
                                        <label>alt:</label>
                                        <input type="text" name="alt" value="<?= htmlspecialchars($method['alt'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="image-upload-container">
                                        <label class="image-upload-label">Текущее изображение:</label>
                                        <?php if (!empty($method['src'])): ?>
                                            <img src="<?= htmlspecialchars($method['src']) ?>" alt="Current image" class="current-image">
                                        <?php else: ?>
                                            <p>Изображение отсутствует</p>
                                        <?php endif; ?>
                                        
                                        <div class="upload-options">
                                            <div class="upload-option">
                                                <label>
                                                    <input type="radio" name="image_source" value="upload" checked> Загрузить изображение
                                                </label>
                                                <input type="file" name="image_upload" accept="image/*">
                                            </div>
                                            <div class="upload-option">
                                                <label>
                                                    <input type="radio" name="image_source" value="url"> Указать URL изображения
                                                </label>
                                                <input type="text" name="src" value="<?= htmlspecialchars($method['src'] ?? '') ?>" placeholder="https://example.com/image.jpg">
                                            </div>
                                        </div>
                                        <img id="image-preview-<?= $country ?>-<?= $index ?>" class="image-preview" style="display:none;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>label:</label>
                                        <input type="text" name="label" value="<?= htmlspecialchars($method['label'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>numero_de_cuenta:</label>
                                        <input type="text" name="numero_de_cuenta" value="<?= htmlspecialchars($method['numero_de_cuenta'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>status:</label>
                                        <select name="status">
                                            <option value="active" <?= ($method['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="pause" <?= ($method['status'] ?? 'active') === 'pause' ? 'selected' : '' ?>>Pause</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>nombre:</label>
                                        <input type="text" name="nombre" value="<?= htmlspecialchars($method['nombre'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>banco:</label>
                                        <input type="text" name="banco" value="<?= htmlspecialchars($method['banco'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>ci:</label>
                                        <input type="text" name="ci" value="<?= htmlspecialchars($method['ci'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>cryptoId:</label>
                                        <input type="text" name="cryptoId" value="<?= htmlspecialchars($method['cryptoId'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>cuit:</label>
                                        <input type="text" name="cuit" value="<?= htmlspecialchars($method['cuit'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>cbu:</label>
                                        <input type="text" name="cbu" value="<?= htmlspecialchars($method['cbu'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>nro:</label>
                                        <input type="text" name="nro" value="<?= htmlspecialchars($method['nro'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>sinpe:</label>
                                        <input type="text" name="sinpe" value="<?= htmlspecialchars($method['sinpe'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>qr:</label>
                                        <input type="text" name="qr" value="<?= htmlspecialchars($method['qr'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>bac:</label>
                                        <input type="text" name="bac" value="<?= htmlspecialchars($method['bac'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>cta:</label>
                                        <input type="text" name="cta" value="<?= htmlspecialchars($method['cta'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>cedula:</label>
                                        <input type="text" name="cedula" value="<?= htmlspecialchars($method['cedula'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>IBAN:</label>
                                        <input type="text" name="IBAN" value="<?= htmlspecialchars($method['IBAN'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Identificación:</label>
                                        <input type="text" name="Identificación" value="<?= htmlspecialchars($method['Identificación'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Rut:</label>
                                        <input type="text" name="Rut" value="<?= htmlspecialchars($method['Rut'] ?? '') ?>">
                                    </div>
                                    <div class="action-buttons">
                                        <button type="submit" name="update_method" class="btn btn-success">Сохранить</button>
                                        <button type="button" onclick="hideEditForm('<?= $country ?>', <?= $index ?>)" class="btn btn-danger">Отмена</button>
                                    </div>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div id="add" class="tab-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="country">Тип метода:</label>
                    <select id="country" name="country" required>
                        <option value="">Выберите тип</option>
                        <option value="universal">Универсальный метод</option>
                        <?php foreach ($allCountries as $country): ?>
                            <option value="<?= htmlspecialchars($country) ?>"><?= htmlspecialchars($country) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="alt">alt:</label>
                    <input type="text" id="alt" name="alt" required>
                </div>
                
                <div class="upload-options">
                    <div class="upload-option">
                        <label>
                            <input type="radio" name="image_source" value="upload" checked> Загрузить изображение
                        </label>
                        <input type="file" id="image_upload" name="image_upload" accept="image/*">
                        <img id="image-preview-add" class="image-preview" style="display:none;">
                    </div>
                    <div class="upload-option">
                        <label>
                            <input type="radio" name="image_source" value="url"> Указать URL изображения
                        </label>
                        <input type="text" id="image_url" name="src" placeholder="https://example.com/image.jpg" style="display:none;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>label:</label>
                    <input type="text" name="label" required>
                </div>
                
                <div class="form-group">
                    <label>numero_de_cuenta:</label>
                    <input type="text" name="numero_de_cuenta" required>
                </div>
                
                <div class="form-group">
                    <label>status:</label>
                    <select name="status">
                        <option value="active" selected>Active</option>
                        <option value="pause">Pause</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>nombre:</label>
                    <input type="text" name="nombre">
                </div>
                
                <div class="form-group">
                    <label>banco:</label>
                    <input type="text" name="banco">
                </div>
                
                <div class="form-group">
                    <label>ci:</label>
                    <input type="text" name="ci">
                </div>
                
                <div class="form-group">
                    <label>cryptoId:</label>
                    <input type="text" name="cryptoId">
                </div>
                
                <div class="form-group">
                    <label>cuit:</label>
                    <input type="text" name="cuit">
                </div>
                
                <div class="form-group">
                    <label>cbu:</label>
                    <input type="text" name="cbu">
                </div>
                
                <div class="form-group">
                    <label>nro:</label>
                    <input type="text" name="nro">
                </div>
                
                <div class="form-group">
                    <label>sinpe:</label>
                    <input type="text" name="sinpe">
                </div>
                <div class="form-group">
                    <label>qr:</label>
                    <input type="text" name="qr">
                </div>
                <div class="form-group">
                    <label>bac:</label>
                    <input type="text" name="bac">
                </div>
                
                <div class="form-group">
                    <label>cta:</label>
                    <input type="text" name="cta">
                </div>
                
                <div class="form-group">
                    <label>cedula:</label>
                    <input type="text" name="cedula">
                </div>
                <div class="form-group">
                    <label>IBAN:</label>
                    <input type="text" name="IBAN">
                </div>
                <div class="form-group">
                    <label>Identificación:</label>
                    <input type="text" name="Identificación">
                </div>
                <div class="form-group">
                    <label>Rut:</label>
                    <input type="text" name="Rut">
                </div>
                
                <button type="submit" name="add_method" class="btn btn-success">Добавить метод</button>
            </form>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
                tab.classList.remove('active');
            });
            document.getElementById(tabName).style.display = 'block';
            document.getElementById(tabName).classList.add('active');
            
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }

        function showEditForm(country, index) {
            // Сначала скрываем все формы редактирования
            document.querySelectorAll('.edit-form').forEach(form => {
                form.style.display = 'none';
            });
            
            // Показываем нужную форму
            const formId = country === 'universal' 
                ? `edit-form-universal-${index}`
                : `edit-form-${country}-${index}`;
            
            const form = document.getElementById(formId);
            form.style.display = 'block';
            
            // Прокручиваем к форме
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideEditForm(country, index) {
            const formId = country === 'universal' 
                ? `edit-form-universal-${index}`
                : `edit-form-${country}-${index}`;
            
            document.getElementById(formId).style.display = 'none';
        }
        
        // Обработчики для предпросмотра изображений
        document.addEventListener('DOMContentLoaded', function() {
            // Для универсальных методов
            <?php foreach ($config['universalPaymentMethods'] as $index => $method): ?>
                setupImagePreview(`image-upload-universal-${<?= $index ?>}`, `image-preview-universal-${<?= $index ?>}`);
            <?php endforeach; ?>
            
            // Для методов по странам
            <?php foreach ($config['paymentButtonsByCountry'] as $country => $methods): ?>
                <?php foreach ($methods as $index => $method): ?>
                    setupImagePreview(`image-upload-${'<?= $country ?>'}-${<?= $index ?>}`, `image-preview-${'<?= $country ?>'}-${<?= $index ?>}`);
                <?php endforeach; ?>
            <?php endforeach; ?>
            
            // Для формы добавления
            setupImagePreview('image_upload', 'image-preview-add');
            
            // Переключение между загрузкой и URL
            const imageSourceRadios = document.querySelectorAll('input[name="image_source"]');
            const imageUpload = document.getElementById('image_upload');
            const imageUrl = document.getElementById('image_url');
            
            imageSourceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'upload') {
                        imageUpload.style.display = 'block';
                        imageUrl.style.display = 'none';
                        imageUrl.removeAttribute('required');
                        imageUpload.setAttribute('required', '');
                    } else {
                        imageUpload.style.display = 'none';
                        imageUrl.style.display = 'block';
                        imageUpload.removeAttribute('required');
                        imageUrl.setAttribute('required', '');
                    }
                });
            });
            
            // Инициализация состояния
            if (document.querySelector('input[name="image_source"]:checked').value === 'upload') {
                imageUpload.style.display = 'block';
                imageUrl.style.display = 'none';
                imageUpload.setAttribute('required', '');
            } else {
                imageUpload.style.display = 'none';
                imageUrl.style.display = 'block';
                imageUrl.setAttribute('required', '');
            }
        });
        
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                    }
                });
            }
        }
        
        // Обработчики для переключения между загрузкой и URL
        document.querySelectorAll('.edit-form').forEach(form => {
            const uploadRadios = form.querySelectorAll('input[name="image_source"]');
            const fileInput = form.querySelector('input[type="file"]');
            const urlInput = form.querySelector('input[name="src"]:not([type="hidden"])');
            
            if (uploadRadios && fileInput && urlInput) {
                uploadRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'upload') {
                            fileInput.style.display = 'block';
                            urlInput.style.display = 'none';
                        } else {
                            fileInput.style.display = 'none';
                            urlInput.style.display = 'block';
                        }
                    });
                });
                
                // Инициализация состояния
                const checkedRadio = form.querySelector('input[name="image_source"]:checked');
                if (checkedRadio) {
                    if (checkedRadio.value === 'upload') {
                        fileInput.style.display = 'block';
                        urlInput.style.display = 'none';
                    } else {
                        fileInput.style.display = 'none';
                        urlInput.style.display = 'block';
                    }
                }
            }
        });
    </script>
</body>
</html>