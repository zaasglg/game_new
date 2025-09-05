// Функция для выполнения AJAX-запроса
function checkOldPayments() {
  fetch("decline_old_payments.php")
    .then((response) => response.text())
    .then((data) => {
      console.log("Payments processed:", data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// Запускаем при загрузке страницы
document.addEventListener("DOMContentLoaded", function () {
  checkOldPayments();
});

// Запускаем каждые 5 минут (300000 миллисекунд)
setInterval(checkOldPayments, 300000);
