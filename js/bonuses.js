document.addEventListener("DOMContentLoaded", function () {
  // Конфигурация модальных окон
  const modalsConfig = [
    {
      openButtonAttr: "button_open_bonuses",
      modalClass: "_modal_15zx8_13",
      openClass: "_modal--open_15zx8_313",
      closeButtonAttr: "close-modal-button-bonuses",
      closeOnEsc: true, // Флаг для закрытия по ESC
    },
    {
      openButtonAttr: "button_open_bonuses_two",
      modalClass: "_modal_15zx8_14",
      openClass: "_modal--open_15zx8_314",
      closeButtonAttr: "close-modal-button-bonuses-two",
      closeOnEsc: false, // Отключаем закрытие по ESC для этого окна
    },
  ];

  const overlay = document.querySelector("._overlay_hi522_1");

  // Проверяем наличие оверлея
  if (!overlay) {
    console.warn("Не найден элемент оверлея");
    return;
  }

  // Инициализация каждого модального окна
  modalsConfig.forEach((config) => {
    const modal = document.querySelector(`.${config.modalClass}`);
    const closeButton = document.querySelector(
      `[data-cy="${config.closeButtonAttr}"]`
    );

    // Пропускаем если нет необходимых элементов
    if (!modal || !closeButton) {
      console.warn(
        `Не найдены элементы для модального окна ${config.modalClass}`
      );
      return;
    }

    // Функция открытия
    function openModal() {
      modal.classList.add(config.openClass);
      overlay.classList.add("_overlay--active_hi522_13");
    }

    // Функция закрытия
    function closeModal() {
      modal.classList.remove(config.openClass);

      // Проверяем, есть ли другие открытые модальные окна
      const anyModalOpen = modalsConfig.some((c) =>
        document
          .querySelector(`.${c.modalClass}`)
          ?.classList.contains(c.openClass)
      );

      if (!anyModalOpen) {
        overlay.classList.remove("_overlay--active_hi522_13");
      }
    }

    // Обработчик открытия
    document.addEventListener("click", function (event) {
      if (event.target.closest(`[data-cy="${config.openButtonAttr}"]`)) {
        openModal();
      }
    });

    // Обработчики закрытия
    closeButton.addEventListener("click", closeModal);

    // Общие обработчики
    overlay.addEventListener("click", function () {
      modalsConfig.forEach((c) => {
        const m = document.querySelector(`.${c.modalClass}`);
        if (m) m.classList.remove(c.openClass);
      });
      overlay.classList.remove("_overlay--active_hi522_13");
    });
  });

  // Обработчик ESC только для окон, где closeOnEsc: true
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      modalsConfig.forEach((c) => {
        if (c.closeOnEsc) {
          const m = document.querySelector(`.${c.modalClass}`);
          if (m && m.classList.contains(c.openClass)) {
            m.classList.remove(c.openClass);

            // Проверяем, есть ли другие открытые модальные окна
            const anyModalOpen = modalsConfig.some((cfg) =>
              document
                .querySelector(`.${cfg.modalClass}`)
                ?.classList.contains(cfg.openClass)
            );

            if (!anyModalOpen) {
              overlay.classList.remove("_overlay--active_hi522_13");
            }
          }
        }
      });
    }
  });
});
