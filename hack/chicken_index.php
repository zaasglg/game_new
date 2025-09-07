<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>🐔 Chicken Road Hack Bot - Autorización</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="./css/reset.css?v=1.0">
    <link rel="stylesheet" href="./css/normalize.css?v=1.0">
    <link rel="stylesheet" href="./css/style.css?v=1.0">
    <link rel="icon" href="./images/authorization.png" />

    <!-- jQuery (обязательно первым) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- notify.js (старая версия с $.notify) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
</head>
<body style="background: #000;">

    <div class="main__wrapper">
        <!-- Картинки (как в оригинале) -->
        <img class="money__top--left" src="./images/money_top_left.webp" alt="money">
        <img class="money__top--right" src="./images/money_top_right.webp" alt="money">
        <img class="money__left--center" src="./images/money_left_center.webp" alt="money">
        <img class="money__right--center" src="./images/money_right_center.webp" alt="money">
        
        <div class="main">
            <h1 class="translate" data-key="welcome">🐔 ¡Bienvenido al Chicken Road Hack Bot!</h1>   

            <!-- Форма -->
            <form id="chickenLoginForm" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <input class="translate-placeholder" 
                       data-key="input_id" 
                       style="margin-bottom: 10px;" 
                       type="text" 
                       name="user_id" 
                       id="user_id" 
                       placeholder="Introduce tu ID de usuario" 
                       required>
                <button style="background: #FFD900" 
                        class="btn translate" 
                        type="submit" 
                        data-key="sign_in">Acceder al Hack Bot</button>
            </form>
            
            <p id="errorMessage" style="color: red; display: none;"></p>
            
            <!-- Скрипт -->
            <script>
                $(document).ready(function() {
                    console.log("jQuery version:", $.fn.jquery);
                    console.log("Notify loaded?", typeof $.notify !== "undefined");

                    $("#chickenLoginForm").submit(function(event) {
                        event.preventDefault();

                        let user_id = $("#user_id").val();

                        // Проверка заполненности
                        if (!user_id || isNaN(user_id)) {
                            $.notify("Fill in the account ID", "error"); // сообщение на английском
                            return;
                        }

                        let formData = new FormData();
                        formData.append("user_id", user_id);

                        fetch("login.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $.notify("¡Inicio de sesión exitoso!", "success");
                                setTimeout(() => {
                                    window.location.href = `chicken_road.php?user_id=${user_id}`;
                                }, 1000);
                            } else {
                                $.notify(data.message || "Error de inicio de sesión", "error");
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            $.notify("Error del servidor. Inténtalo más tarde.", "error");
                        });
                    });
                });
            </script>

            <!-- Переключатель языка -->
            <label class="switch">
                <p class="es">ES</p>
                <input type="checkbox" class="toggle">
                <span class="slider round"></span>
                <p class="eng">ENG</p>
            </label>
        </div>
    </div>

    <!-- Скрипты -->
    <script src="./js/toggle.js?v=1.0"></script>
    <script src="./js/script.js?v=1.0"></script>
    <script src="./js/lang.js?v=1.0"></script>
</body>
</html>
