<?php
if (isset($_GET['r']) && !empty($_GET['r'])) {
    $utm_r = htmlspecialchars($_GET['r'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        localStorage.setItem('utm_r', '$utm_r');
    </script>";
}
?>

<script>
    // Получение значения
    const utmR = localStorage.getItem('utm_r') || 'Не найдено';
    console.log('UTM r:', utmR);
</script>