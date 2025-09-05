document.addEventListener("DOMContentLoaded", function () {
  // Элементы модальных окон
  const loginModal = document.querySelector("._modal_15zx8_1");
  const registerModal = document.querySelector("._modal_15zx8_12");
  const overlay = document.querySelector("._overlay_hi522_1");

  // Кнопки закрытия модальных окон
  const closeLoginModalButton = document.querySelector(
    '[data-cy="close-modal-button-login"]'
  );
  const closeRegisterModalButton = document.querySelector(
    '[data-cy="close-modal-button"]'
  );

  // Кнопки для переключения между модальными окнами
  const switchToRegisterButton = document.querySelector(".open_login-modal");
  const switchToLoginButton = document.querySelector(".open_register-modal");

  // Функция для открытия модального окна логина
  function openLoginModal() {
    loginModal.classList.add("_modal--open_15zx8_31");
  }

  // Функция для закрытия модального окна логина
  function closeLoginModal() {
    loginModal.classList.remove("_modal--open_15zx8_31");
  }

  // Функция для открытия модального окна регистрации
  function openRegisterModal() {
    registerModal.classList.add("_modal--open_15zx8_31");
  }

  // Функция для закрытия модального окна регистрации
  function closeRegisterModal() {
    registerModal.classList.remove("_modal--open_15zx8_31");
  }

  // Делегирование событий для открытия модальных окон
  document.addEventListener("click", function (event) {
    const target = event.target;

    // Открытие модального окна логина
    if (target.closest('[data-cy="button_open_login"]')) {
      openLoginModal();
    }

    // Открытие модального окна регистрации
    if (target.closest('[data-cy="button_open_register"]')) {
      openRegisterModal();
    }
  });

  // Закрытие модального окна логина при клике на кнопку закрытия
  closeLoginModalButton.addEventListener("click", closeLoginModal);

  // Закрытие модального окна регистрации при клике на кнопку закрытия
  closeRegisterModalButton.addEventListener("click", closeRegisterModal);

  // Закрытие модальных окон при клике на оверлей
  overlay.addEventListener("click", function () {
    closeLoginModal();
    closeRegisterModal();
  });

  // Закрытие модальных окон при нажатии на клавишу Esc
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeLoginModal();
      closeRegisterModal();
    }
  });

  // Переключение с модального окна логина на регистрацию
  switchToRegisterButton.addEventListener("click", function () {
    closeLoginModal();
    openRegisterModal();
  });

  // Переключение с модального окна регистрации на логин
  switchToLoginButton.addEventListener("click", function () {
    closeRegisterModal();
    openLoginModal();
  });
});

document.querySelectorAll(".games__href").forEach((block) => {
  block.addEventListener("click", function () {
    window.location.href = "/all_games.php?game=aviator";
  });
});

// document.querySelectorAll("._gameItem_1jj5g_1").forEach((block) => {
//   block.addEventListener("click", function () {
//     window.location.href = "/all_games.php?categorías=all_games";
//   });
// });

document.addEventListener("DOMContentLoaded", function () {
  const clickableElements = document.querySelectorAll(
    "._card_r5hue_1, ._gameItem_jackpot_1jj5g_120"
  );
  const casinoContent = document.querySelector(".casino__content");
  const blockLoader = document.querySelector(".block-loader");

  if (clickableElements.length && casinoContent && blockLoader) {
    clickableElements.forEach((element) => {
      element.addEventListener("click", function () {
        // 1. Ждем 3 секунды перед началом
        setTimeout(() => {
          // 2. Начинаем анимацию скрытия контента
          casinoContent.style.transition = "opacity 0.3s ease";
          casinoContent.style.opacity = "0";

          // 3. После завершения анимации скрытия (0.3s)
          setTimeout(() => {
            casinoContent.style.display = "none";
            blockLoader.style.display = "flex";

            // 4. Показываем лоадер с анимацией
            setTimeout(() => {
              blockLoader.style.transition = "opacity 0.3s ease";
              blockLoader.style.opacity = "1";

              // 5. Через 3 секунды начинаем скрывать лоадер
              setTimeout(() => {
                blockLoader.style.opacity = "0";

                // 6. После анимации скрытия лоадера (0.3s)
                setTimeout(() => {
                  blockLoader.style.display = "none";
                  casinoContent.style.display = "block";

                  // 7. Показываем контент с анимацией
                  setTimeout(() => {
                    casinoContent.style.opacity = "1";
                  }, 10);
                }, 300);
              }, 3000);
            }, 10);
          }, 300);
        }, 1500); // Начальная задержка
      });
    });
  }
});
