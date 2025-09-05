document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const minesApp = document.querySelector("#app.mines");

  // Проверяем параметр game в URL
  if (urlParams.has("game")) {
    const gameName = urlParams.get("game");

    // Показываем mines только если game=mines
    if (minesApp) {
      minesApp.style.display = gameName === "mines" ? "block" : "none";
    }

    // Остальная логика обработки игр
    const casinoContent = document.querySelector(".casino__content");
    const casinoMobContent = document.querySelector(".casino-mobile");
    const blockLoader = document.querySelector(".block-loader");
    const blockDeskLoader = document.querySelector(".block-loader-desktop");
    const blockMobLoader = document.querySelector(".block-loader-mob");
    const footer = document.querySelector("._footer_19h5r_12");
    const casinoContainer = document.querySelector(".casino");

    if (gameName === "mines") {
      // Проверяем мобильное устройство
      const isMobile = window.matchMedia("(max-width: 768px)").matches;

      // Для десктопа
      if (!isMobile && casinoContent && blockDeskLoader) {
        casinoContent.style.display = "none";
        blockDeskLoader.style.display = "none";
      }
      // Для мобильной версии
      else if (isMobile && casinoMobContent && blockMobLoader) {
        casinoMobContent.style.display = "none";
        footer.style.display = "none";
        blockMobLoader.style.display = "none";

        // Перемещаем блок игры после блока казино
        if (casinoContainer && casinoContainer.parentNode) {
          casinoContainer.parentNode.insertBefore(
            minesApp,
            casinoContainer.nextSibling
          );
        }
      }
    } else {
      // Обработка для других игр
      if (casinoContent && blockLoader) {
        casinoContent.style.display = "none";
        blockDeskLoader.style.display = "flex";
      }
      if (casinoMobContent && blockLoader) {
        casinoMobContent.style.display = "none";
        footer.style.display = "none";
        blockMobLoader.style.display = "flex";
      }
    }
  } else {
    // Если параметра game нет, скрываем mines
    if (minesApp) {
      minesApp.style.display = "none";
    }
  }
  // Остальной код остается без изменений
  // Функция для обработки клика на игровую карточку
  function handleGameCardClick(card) {
    const container = card.querySelector(
      "._container_tin0x_1, ._loading_r5hue_115"
    );
    const gameName =
      card.querySelector("._name_r5hue_27, [alt]")?.alt ||
      card.closest('[aria-hidden="true"]')?.querySelector("[alt]")?.alt;

    if (gameName) {
      if (container) {
        container.style.display = "flex";
      }

      setTimeout(() => {
        const formattedName = gameName
          .trim()
          .toLowerCase()
          .replace(/\s+/g, "_");
        window.location.href = `/all_games.php?game=${encodeURIComponent(
          formattedName
        )}`;
      }, 3000);
    }
  }

  // Обработка кликов на все типы игровых карточек
  const gameCardSelectors = [
    "._card_r5hue_1",
    "._gameItem_jackpot_1jj5g_120",
    'div[aria-hidden="true"] .lazyload-wrapper',
    'div[data-testid="game-item"]',
  ];

  gameCardSelectors.forEach((selector) => {
    document.querySelectorAll(selector).forEach((card) => {
      card.addEventListener("click", function () {
        handleGameCardClick(this);
      });
    });
  });

  // Проверка live категории в URL
  if (urlParams.has("categorías") && urlParams.get("categorías") === "live") {
    // Удаляем активный класс со всех элементов
    document.querySelectorAll("._link_active_p19s5_42").forEach((el) => {
      el.classList.remove("_link_active_p19s5_42");
    });

    // Добавляем активный класс для десктопной версии
    const desktopLiveGamesLink = document.querySelector(
      'a._link_p19s5_1[href="/all_games.php?categorías=live"]:not(._link_mobile_p19s5_45)'
    );
    if (desktopLiveGamesLink) {
      desktopLiveGamesLink.classList.add("_link_active_p19s5_42");
    }

    // Добавляем активный класс для мобильной версии
    const mobileLiveGamesLink = document.querySelector(
      'a._link_mobile_p19s5_45[href="/all_games.php?categorías=live"]'
    );
    if (mobileLiveGamesLink) {
      mobileLiveGamesLink.classList.add("_link_active_p19s5_42");
    }
  }

  // Находим элемент "All Games" по умолчанию
  const itemTitleElement = document.querySelector("span._itemTitle_1rs5q_52");
  const defaultActiveItem = itemTitleElement && itemTitleElement.textContent === "All Games"
    ? itemTitleElement.closest("a._item_1rs5q_42")
    : null;

  // Функция для удаления активного класса со всех элементов
  function clearAllActiveClasses() {
    document.querySelectorAll("a._item_active_1rs5q_69").forEach((item) => {
      item.classList.remove("_item_active_1rs5q_69");
    });
  }

  // Устанавливаем активный элемент по умолчанию
  function setDefaultActive() {
    clearAllActiveClasses();
    if (defaultActiveItem) {
      defaultActiveItem.classList.add("_item_active_1rs5q_69");
    }
  }

  // Обработчик кликов для всех списков
  document.querySelectorAll("div._list_1rs5q_18").forEach((list) => {
    list.addEventListener("click", function (event) {
      const clickedItem = event.target.closest("a._item_1rs5q_42");
      if (!clickedItem) return;

      // Определяем тип параметра
      const section = list.closest("div._section_1rs5q_1");
      const sectionTitle =
        section
          ?.querySelector("span._title_1rs5q_9")
          ?.textContent.trim()
          .toLowerCase() || "";

      let paramName = "";
      if (sectionTitle.includes("categorías")) {
        paramName = "categorías";
      } else if (sectionTitle.includes("proveedores")) {
        paramName = "proveedores";
      }

      // Удаляем все активные классы
      clearAllActiveClasses();

      // Добавляем класс только к кликнутому элементу
      clickedItem.classList.add("_item_active_1rs5q_69");

      // Получаем текст элемента
      const itemText =
        clickedItem
          .querySelector("span._itemTitle_1rs5q_52")
          ?.textContent.trim() || "";
      const paramValue = itemText.toLowerCase().replace(/\s+/g, "_");

      // Обновляем URL
      updateURL(paramName, paramValue);

      // Обработка live категории
      if (paramName === "categorías") {
        // Удаляем активный класс со всех элементов
        document.querySelectorAll("._link_active_p19s5_42").forEach((el) => {
          el.classList.remove("_link_active_p19s5_42");
        });

        // Если это live категория, активируем соответствующие ссылки
        if (paramValue === "live") {
          // Десктопная версия
          const desktopLiveGamesLink = document.querySelector(
            'a._link_p19s5_1[href="/all_games.php?categorías=live"]:not(._link_mobile_p19s5_45)'
          );
          if (desktopLiveGamesLink) {
            desktopLiveGamesLink.classList.add("_link_active_p19s5_42");
          }

          // Мобильная версия
          const mobileLiveGamesLink = document.querySelector(
            'a._link_mobile_p19s5_45[href="/all_games.php?categorías=live"]'
          );
          if (mobileLiveGamesLink) {
            mobileLiveGamesLink.classList.add("_link_active_p19s5_42");
          }
        }
      }
    });
  });

  // Обработчик для выпадающего списка proveedores
  document
    .querySelectorAll(".select:not(.select--second) .select__list-item")
    .forEach((item) => {
      item.addEventListener("click", function () {
        const itemName = this.querySelector(
          ".select__list-item-name"
        ).textContent.trim();
        const paramValue = itemName.toLowerCase().replace(/\s+/g, "_");

        // Обновляем URL
        updateURL("proveedores", paramValue);

        // Обновляем выбранное значение в контроле
        const controlValue = this.closest(".select").querySelector(
          ".select__control-value"
        );
        if (controlValue) {
          controlValue.textContent = itemName;
        }

        // Удаляем все активные классы
        clearAllActiveClasses();
      });
    });

  // Обработчик для выпадающего списка categorías
  document
    .querySelectorAll(".select.select--second .select__list-item")
    .forEach((item) => {
      item.addEventListener("click", function () {
        const itemName = this.querySelector(
          ".select__list-item-name"
        ).textContent.trim();
        const paramValue = itemName.toLowerCase().replace(/\s+/g, "_");

        // Обновляем URL
        updateURL("categorías", paramValue);

        // Обновляем выбранное значение в контроле
        const controlValue = this.closest(".select").querySelector(
          ".select__control-value"
        );
        if (controlValue) {
          controlValue.textContent = itemName;
        }

        // Удаляем все активные классы
        clearAllActiveClasses();

        // Обработка live категории
        if (paramValue === "live") {
          // Удаляем активный класс со всех элементов
          document.querySelectorAll("._link_active_p19s5_42").forEach((el) => {
            el.classList.remove("_link_active_p19s5_42");
          });

          // Десктопная версия
          const desktopLiveGamesLink = document.querySelector(
            'a._link_p19s5_1[href="/all_games.php?categorías=live"]:not(._link_mobile_p19s5_45)'
          );
          if (desktopLiveGamesLink) {
            desktopLiveGamesLink.classList.add("_link_active_p19s5_42");
          }

          // Мобильная версия
          const mobileLiveGamesLink = document.querySelector(
            'a._link_mobile_p19s5_45[href="/all_games.php?categorías=live"]'
          );
          if (mobileLiveGamesLink) {
            mobileLiveGamesLink.classList.add("_link_active_p19s5_42");
          }
        }
      });
    });

  // Функция обновления URL
  function updateURL(paramName, paramValue) {
    const url = new URL(window.location.href);

    // Очищаем параметры перед установкой нового
    url.searchParams.delete("categorías");
    url.searchParams.delete("proveedores");

    if (paramName && paramValue) {
      url.searchParams.set(paramName, paramValue);
    }

    history.pushState({}, "", url);
  }

  // Активация элемента по параметру
  function activateItem(paramName, paramValue) {
    const sections = document.querySelectorAll("div._section_1rs5q_1");

    for (const section of sections) {
      const title = section
        .querySelector("span._title_1rs5q_9")
        ?.textContent.trim()
        .toLowerCase();

      if (
        (paramName === "categorías" && title?.includes("categorías")) ||
        (paramName === "proveedores" && title?.includes("proveedores"))
      ) {
        const items = section.querySelectorAll("a._item_1rs5q_42");
        for (const item of items) {
          const itemTitle = item.querySelector("span._itemTitle_1rs5q_52");
          const itemText = itemTitle
            ? itemTitle.textContent.trim().toLowerCase().replace(/\s+/g, "_")
            : "";
          if (itemText === paramValue.toLowerCase()) {
            item.classList.add("_item_active_1rs5q_69");
            return;
          }
        }
      }
    }

    // Если не нашли подходящий элемент - активируем "All Games"
    setDefaultActive();
  }

  // Инициализация при загрузке
  function init() {
    const url = new URL(window.location.href);

    // Если в URL нет параметров - устанавливаем активным "All Games"
    if (
      !url.searchParams.has("categorías") &&
      !url.searchParams.has("proveedores")
    ) {
      setDefaultActive();
      return;
    }

    // Если есть параметры - активируем соответствующий элемент
    clearAllActiveClasses();

    if (url.searchParams.has("categorías")) {
      const value = url.searchParams.get("categorías");
      activateItem("categorías", value);

      // Особый обработчик для live категории
      if (value === "live") {
        document.querySelectorAll("._link_active_p19s5_42").forEach((el) => {
          el.classList.remove("_link_active_p19s5_42");
        });

        // Десктопная версия
        const desktopLiveGamesLink = document.querySelector(
          'a._link_p19s5_1[href="/all_games.php?categorías=live"]:not(._link_mobile_p19s5_45)'
        );
        if (desktopLiveGamesLink) {
          desktopLiveGamesLink.classList.add("_link_active_p19s5_42");
        }

        // Мобильная версия
        const mobileLiveGamesLink = document.querySelector(
          'a._link_mobile_p19s5_45[href="/all_games.php?categorías=live"]'
        );
        if (mobileLiveGamesLink) {
          mobileLiveGamesLink.classList.add("_link_active_p19s5_42");
        }
      }

      // Обновляем выбранное значение в выпадающем списке
      const categoriasSelect = document.querySelector(
        ".select.select--second .select__control-value"
      );
      if (categoriasSelect) {
        categoriasSelect.textContent = value.replace(/_/g, " ");
      }
    } else if (url.searchParams.has("proveedores")) {
      const value = url.searchParams.get("proveedores");
      activateItem("proveedores", value);

      // Обновляем выбранное значение в выпадающем списке
      const proveedoresSelect = document.querySelector(
        ".select:not(.select--second) .select__control-value"
      );
      if (proveedoresSelect) {
        proveedoresSelect.textContent = value.replace(/_/g, " ");
      }
    }
  }

  // Запускаем инициализацию
  init();
});
