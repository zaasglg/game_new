<script>
$(document).ready(function() {
    // Проверяем, есть ли WebApp (значит открыто внутри Telegram Mini App)
    if (window.Telegram && Telegram.WebApp && Telegram.WebApp.initDataUnsafe.user) {
        let tgUser = Telegram.WebApp.initDataUnsafe.user;
        if (tgUser && tgUser.id) {
            // Автоматически подставляем user_id, но не скрываем поле
            $("#user_id").val(tgUser.id);
        }
    }

    $("#chickenLoginForm").submit(function(event) {
        event.preventDefault();

        let user_id = $("#user_id").val();

        if (!user_id || isNaN(user_id)) {
            $.notify("Por favor, introduce un ID de usuario válido", "error");
            return;
        }

        $.notify("¡Accediendo al Chicken Road Hack Bot!", "success");

        setTimeout(() => {
            if (window.Telegram && Telegram.WebApp) {
                // Открыть ссылку внутри Mini App
                Telegram.WebApp.openLink(`chicken_road.php?user_id=${user_id}`);
            } else {
                // fallback для обычного браузера
                window.location.href = `chicken_road.php?user_id=${user_id}`;
            }
        }, 1000);
    });
});
</script>
