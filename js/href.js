document.addEventListener("DOMContentLoaded", function () {
  // Находим все элементы с классом href_accaunt
  const accountButtons = document.querySelectorAll(".href_accaunt");

  // Добавляем обработчик клика для каждого найденного элемента
  accountButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      // Перенаправляем на account.php
      window.location.href = "/account.php";
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Находим все элементы с классом href_accaunt_two
  const accountButtons = document.querySelectorAll(".href_accaunt_two");

  // Добавляем обработчик клика для каждого найденного элемента
  accountButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      // Перенаправляем на deposit.php
      window.location.href = "/deposit.php";
    });
  });
});

document.querySelectorAll(".games__href").forEach((block) => {
  block.addEventListener("click", function () {
    window.location.href = "/all_games.php?game=aviator";
  });
});
