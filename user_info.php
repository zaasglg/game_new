<?php
// Включаем вывод ошибок (можно отключить в продакшене)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаемся к базе данных
require 'db.php';

// Обработка изменения данных пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    // DEBUG
    echo "<pre>POST: " . print_r($_POST, true) . "</pre>";
    echo "<p>ID: $id</p>";
    
    try {
        // Подготовка данных для обновления
        $update_fields = [];
        $params = [':id' => $id];
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'user_id' && $key !== 'update_user' && !in_array($key, ['id', 'email', 'dirección'])) {
                $update_fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        echo "<p>Fields: " . implode(', ', $update_fields) . "</p>";
        
        if (!empty($update_fields)) {
            $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE user_id = :id";
            echo "<p>SQL: $sql</p>";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);
            
            echo "<p>Rows affected: " . $stmt->rowCount() . "</p>";
            
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✅ Actualizado</p>";
            } else {
                echo "<p style='color: red;'>❌ No actualizado</p>";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
        echo "<p style='color: red;'>$error_message</p>";
    }
}

// Получаем ID пользователя из параметра URL
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Если ID не передан, показываем форму для ввода
if (!$id) {
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Buscar usuario</title>
        <style>
            :root {
                --primary-color: #4361ee;
                --secondary-color: #3f37c9;
                --text-color: #333;
                --light-gray: #f8f9fa;
                --medium-gray: #e9ecef;
                --dark-gray: #6c757d;
                --white: #ffffff;
                --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                --edit-color: #fd7e14;
            }
            
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                line-height: 1.6;
                color: var(--text-color);
                background-color: var(--light-gray);
                padding: 20px;
            }
            
            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: var(--white);
                border-radius: 8px;
                box-shadow: var(--box-shadow);
            }
            
            h1, h2 {
                color: var(--primary-color);
                margin-bottom: 20px;
                text-align: center;
            }
            
            .search-form {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 30px;
            }
            
            .form-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            label {
                font-weight: 600;
                color: var(--dark-gray);
            }
            
            input, button, select {
                padding: 12px 15px;
                border: 1px solid var(--medium-gray);
                border-radius: 4px;
                font-size: 16px;
                transition: all 0.3s ease;
            }
            
            input:focus, select:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            }
            
            button {
                color: var(--white);
                font-weight: 600;
                cursor: pointer;
                border: none;
            }
            
            .user-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                overflow-x: auto;
            }
            
            .user-table th, .user-table td {
                padding: 12px 15px;
                text-align: left;
                border-bottom: 1px solid var(--medium-gray);
            }
            
            .user-table th {
                background-color: var(--primary-color);
                color: var(--white);
                position: sticky;
                top: 0;
            }
            
            .user-table tr:nth-child(even) {
                background-color: var(--light-gray);
            }
            
            .user-table tr:hover {
                background-color: #f1f3ff;
            }
            
            .back-link {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background-color: var(--primary-color);
                color: var(--white);
                text-decoration: none;
                border-radius: 4px;
                font-weight: 600;
                text-align: center;
            }
            
            .back-link:hover {
                background-color: var(--secondary-color);
            }
            
            .null-value {
                color: var(--dark-gray);
                font-style: italic;
            }
            
            .binary-data {
                color: #6c757d;
                font-style: italic;
            }
            
            .error-message {
                color: #dc3545;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            
            .success-message {
                color: #28a745;
                background-color: #d4edda;
                border: 1px solid #c3e6cb;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            
            .edit-button {
                background-color: var(--edit-color);
                padding: 8px 12px;
                font-size: 14px;
                border-radius: 4px;
                margin-left: 10px;
            }
            
            .edit-button:hover {
                background-color: #e8590c;
            }
            
            .edit-controls {
                display: flex;
                gap: 10px;
                margin-top: 10px;
            }
            
            .save-button {
                background-color: #28a745;
                padding: 8px 15px;
            }
            
            .save-button:hover {
                background-color: #218838;
            }
            
            .cancel-button {
                background-color: #dc3545;
                padding: 8px 15px;
            }
            
            .cancel-button:hover {
                background-color: #c82333;
            }
            
            .edit-input {
                width: 100%;
                padding: 8px 12px;
                border: 2px solid var(--primary-color);
                border-radius: 4px;
                font-size: inherit;
            }
            
            .field-container {
                display: flex;
                align-items: center;
            }
            
            .field-value {
                flex-grow: 1;
            }
            
            @media (max-width: 768px) {
                .container {
                    padding: 15px;
                }
                
                .user-table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }
                
                .field-container {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .edit-button {
                    margin-left: 0;
                    margin-top: 5px;
                    width: 100%;
                }
            }
            
            @media (max-width: 480px) {
                body {
                    padding: 10px;
                }
                
                .user-table th, .user-table td {
                    padding: 8px 10px;
                    font-size: 14px;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Buscar Usuario</h1>
            <form class="search-form" method="GET" action="">
                <div class="form-group">
                    <label for="id">ID del Usuario</label>
                    <input type="number" id="id" name="id" placeholder="Ingrese el ID del usuario" required>
                </div>
                <button type="submit">Buscar Usuario</button>
            </form>
        </div>
    </body>
    </html>
    ';
    exit;
}


try {
    // Получаем все данные о пользователе
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '
        <div class="container">
            <div class="error-message">Usuario con ID ' . htmlspecialchars($id) . ' no encontrado.</div>
            <a href="user_info.php" class="back-link">← Volver a buscar</a>
        </div>
        ';
        exit;
    }

    // Выводим информацию
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuario #' . htmlspecialchars($id) . '</title>
        <style>
            :root {
                --primary-color: #4361ee;
                --secondary-color: #3f37c9;
                --text-color: #333;
                --light-gray: #f8f9fa;
                --medium-gray: #e9ecef;
                --dark-gray: #6c757d;
                --white: #ffffff;
                --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                --edit-color: #fd7e14;
            }
            
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                line-height: 1.6;
                color: var(--text-color);
                background-color: var(--light-gray);
                padding: 20px;
            }
            
            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: var(--white);
                border-radius: 8px;
                box-shadow: var(--box-shadow);
            }
            
            h1, h2 {
                color: var(--primary-color);
                margin-bottom: 20px;
                text-align: center;
            }
            
            .user-info {
                margin-bottom: 30px;
            }
            
            .user-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                overflow-x: auto;
            }
            
            .user-table th, .user-table td {
                padding: 12px 15px;
                text-align: left;
                border-bottom: 1px solid var(--medium-gray);
            }
            
            .user-table th {
                background-color: var(--primary-color);
                color: var(--white);
                position: sticky;
                top: 0;
            }
            
            .user-table tr:nth-child(even) {
                background-color: var(--light-gray);
            }
            
            .user-table tr:hover {
                background-color: #f1f3ff;
            }
            
            .back-link {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background-color: var(--primary-color);
                color: var(--white);
                text-decoration: none;
                border-radius: 4px;
                font-weight: 600;
                text-align: center;
            }
            
            .back-link:hover {
                background-color: var(--secondary-color);
            }
            
            .null-value {
                color: var(--dark-gray);
                font-style: italic;
            }
            
            .binary-data {
                color: #6c757d;
                font-style: italic;
            }
            
            .error-message {
                color: #dc3545;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            
            .success-message {
                color: #28a745;
                background-color: #d4edda;
                border: 1px solid #c3e6cb;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            
            .edit-button {
                background-color: var(--edit-color);
                padding: 8px 12px;
                font-size: 14px;
                border-radius: 4px;
                margin-left: 10px;
            }
            
            .edit-button:hover {
                background-color: #e8590c;
            }
            
            .edit-controls {
                display: flex;
                gap: 10px;
                margin-top: 10px;
            }
            
            .save-button {
                background-color: #28a745;
                padding: 8px 15px;
            }
            
            .save-button:hover {
                background-color: #218838;
            }
            
            .cancel-button {
                background-color: #dc3545;
                padding: 8px 15px;
            }
            
            .cancel-button:hover {
                background-color: #c82333;
            }
            
            .edit-input {
                width: 100%;
                padding: 8px 12px;
                border: 2px solid var(--primary-color);
                border-radius: 4px;
                font-size: inherit;
            }
            
            .field-container {
                display: flex;
                align-items: center;
            }
            
            .field-value {
                flex-grow: 1;
            }
            
            @media (max-width: 768px) {
                .container {
                    padding: 15px;
                }
                
                .user-table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }
                
                .field-container {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .edit-button {
                    margin-left: 0;
                    margin-top: 5px;
                    width: 100%;
                }
            }
            
            @media (max-width: 480px) {
                body {
                    padding: 10px;
                }
                
                .user-table th, .user-table td {
                    padding: 8px 10px;
                    font-size: 14px;
                }
            }
        </style>
        <script>
            function enableEdit(fieldName, currentValue) {
                const cell = document.querySelector(`td[data-field="${fieldName}"]`);
                
                // Create input field or select depending on field type
                let inputField;
                
                if (fieldName === "sexo") {
                    inputField = document.createElement("select");
                    inputField.className = "edit-input";
                    inputField.id = `edit-${fieldName}`;
                    
                    const options = ["masculino", "femenino"];
                    options.forEach(option => {
                        const optElement = document.createElement("option");
                        optElement.value = option;
                        optElement.textContent = option;
                        if (currentValue === option) optElement.selected = true;
                        inputField.appendChild(optElement);
                    });
                } 
                else if (fieldName === "status") {
                    inputField = document.createElement("select");
                    inputField.className = "edit-input";
                    inputField.id = `edit-${fieldName}`;
                    
                    const options = ["active", "banned"];
                    options.forEach(option => {
                        const optElement = document.createElement("option");
                        optElement.value = option;
                        optElement.textContent = option;
                        if (currentValue === option) optElement.selected = true;
                        inputField.appendChild(optElement);
                    });
                }
                else if (fieldName === "stage") {
                    inputField = document.createElement("select");
                    inputField.className = "edit-input";
                    inputField.id = `edit-${fieldName}`;
                    
                    const options = ["normal", "verif", "verif2", "supp", "meet"];
                    options.forEach(option => {
                        const optElement = document.createElement("option");
                        optElement.value = option;
                        optElement.textContent = option;
                        if (currentValue === option) optElement.selected = true;
                        inputField.appendChild(optElement);
                    });
                }
                else {
                    inputField = document.createElement("input");
                    inputField.type = "text";
                    inputField.className = "edit-input";
                    inputField.value = currentValue === "NULL" ? "" : currentValue;
                    inputField.id = `edit-${fieldName}`;
                }
                
                // Create controls container
                const controlsDiv = document.createElement("div");
                controlsDiv.className = "edit-controls";
                
                // Create save button
                const saveButton = document.createElement("button");
                saveButton.type = "button";
                saveButton.className = "save-button";
                saveButton.textContent = "Guardar";
                saveButton.onclick = function() {
                    const inputElement = document.getElementById(`edit-${fieldName}`);
                    saveEdit(fieldName, inputElement.value);
                };
                
                // Create cancel button
                const cancelButton = document.createElement("button");
                cancelButton.type = "button";
                cancelButton.className = "cancel-button";
                cancelButton.textContent = "Cancelar";
                cancelButton.onclick = function() {
                    cancelEdit(fieldName, currentValue);
                };
                
                // Append controls
                controlsDiv.appendChild(saveButton);
                controlsDiv.appendChild(cancelButton);
                
                // Replace cell content
                cell.innerHTML = "";
                cell.appendChild(inputField);
                cell.appendChild(controlsDiv);
                
                // Focus input
                inputField.focus();
            }
            
            function saveEdit(fieldName, newValue) {
                const form = document.createElement("form");
                form.method = "POST";
                form.style.display = "none";
                
                const userIdInput = document.createElement("input");
                userIdInput.type = "hidden";
                userIdInput.name = "user_id";
                userIdInput.value = "' . htmlspecialchars($id) . '";
                
                const fieldInput = document.createElement("input");
                fieldInput.type = "hidden";
                fieldInput.name = fieldName;
                fieldInput.value = newValue;
                
                const submitInput = document.createElement("input");
                submitInput.type = "hidden";
                submitInput.name = "update_user";
                submitInput.value = "1";
                
                form.appendChild(userIdInput);
                form.appendChild(fieldInput);
                form.appendChild(submitInput);
                
                document.body.appendChild(form);
                form.submit();
            }
            
            function cancelEdit(fieldName, originalValue) {
                const cell = document.querySelector(`td[data-field="${fieldName}"]`);
                
                // Recreate original content
                const container = document.createElement("div");
                container.className = "field-container";
                
                const valueDiv = document.createElement("div");
                valueDiv.className = "field-value";
                
                if (originalValue === "NULL") {
                    valueDiv.innerHTML = \'<span class="null-value">NULL</span>\';
                } else if (originalValue === "[Datos binarios]") {
                    valueDiv.innerHTML = \'<span class="binary-data">[Datos binarios]</span>\';
                } else {
                    valueDiv.textContent = originalValue;
                }
                
                // Only add edit button if field is editable
                if (!["user_id", "id", "email", "dirección"].includes(fieldName)) {
                    const editButton = document.createElement("button");
                    editButton.type = "button";
                    editButton.className = "edit-button";
                    editButton.textContent = "Editar";
                    editButton.onclick = function() {
                        enableEdit(fieldName, originalValue);
                    };
                    container.appendChild(editButton);
                }
                
                cell.innerHTML = "";
                cell.appendChild(container);
            }
        </script>
    </head>
    <body>
        <div class="container">
            <h1>Información del Usuario #' . htmlspecialchars($id) . '</h1>';
            
    if (isset($error_message)) {
        echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
    }
    
    echo '
            <div class="user-info">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
    ';

    foreach ($user as $key => $value) {
        // Форматируем специальные типы данных
        if ($value === null) {
            $display_value = '<span class="null-value">NULL</span>';
            $raw_value = "NULL";
        } elseif (is_bool($value)) {
            $display_value = $value ? 'true' : 'false';
            $raw_value = $value ? 'true' : 'false';
        } elseif (is_string($value) && preg_match('/[^\x20-\x7E]/', $value)) {
            $display_value = '<span class="binary-data">[Datos binarios]</span>';
            $raw_value = "[Datos binarios]";
        } else {
            $display_value = htmlspecialchars($value);
            $raw_value = htmlspecialchars($value);
        }
        
        echo '
                        <tr>
                            <td><strong>' . htmlspecialchars($key) . '</strong></td>
                            <td data-field="' . htmlspecialchars($key) . '">
                                <div class="field-container">
                                    <div class="field-value">' . $display_value . '</div>';
        
        // Не показываем кнопку редактирования для запрещенных полей
        if (!in_array($key, ['user_id', 'id', 'email', 'dirección'])) {
            echo '          <button type="button" class="edit-button" onclick="enableEdit(\'' . htmlspecialchars($key) . '\', \'' . str_replace("'", "\\'", $raw_value) . '\')">
                                        Editar
                                    </button>';
        }
        
        echo '          </div>
                            </td>
                        </tr>
        ';
    }

    echo '
                    </tbody>
                </table>
            </div>
            
            <a href="user_info.php" class="back-link">← Buscar otro usuario</a>
        </div>
    </body>
    </html>
    ';

} catch (PDOException $e) {
    echo '
    <div class="container">
        <div class="error-message">Error de base de datos: ' . htmlspecialchars($e->getMessage()) . '</div>
        <a href="user_info.php" class="back-link">← Volver a buscar</a>
    </div>
    ';
}