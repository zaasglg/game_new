document.addEventListener("DOMContentLoaded", function () {
  // --- Обработка блока с кнопками сумм ---
  const buttonsContainer = document.querySelector("._buttons_qbycw_1");
  const input = document.querySelector("._input_1j8fn_72");

  if (buttonsContainer && input) {
    // Функция для извлечения числового значения из текста кнопки
    function getValueFromButton(button) {
      const text = button.querySelector("._value_p2ini_18").textContent;
      const match = text.match(/\d+/);
      return match ? parseInt(match[0]) : null;
    }

    // Функция для проверки и обновления состояния ошибки input
    function updateInputErrorState() {
      if (!input.value || input.value.trim() === "") {
        input.classList.add("_input_error_1j8fn_92");
      } else {
        input.classList.remove("_input_error_1j8fn_92");
      }
    }

    // Обработчик клика по кнопкам сумм
    buttonsContainer.addEventListener("click", function (e) {
      const button = e.target.closest("._item_p2ini_1");
      if (!button) return;

      // Удаляем активный класс у всех кнопок
      document.querySelectorAll("._item_p2ini_1").forEach((btn) => {
        btn.classList.remove("");
      });

      // Добавляем активный класс к выбранной кнопке
      button.classList.add("_item_active_p2ini_35");

      // Устанавливаем значение в input и обновляем состояние ошибки
      const value = getValueFromButton(button);
      if (value) {
        input.value = value;
        updateInputErrorState();
      }
    });

    // Обработчик изменения input
    input.addEventListener("input", function () {
      const inputValue = parseInt(input.value) || 0;
      let foundMatch = false;

      // Проверяем все кнопки на соответствие значению input
      document.querySelectorAll("._item_p2ini_1").forEach((button) => {
        const buttonValue = getValueFromButton(button);

        if (buttonValue === inputValue) {
          button.classList.add("_item_active_p2ini_35");
          foundMatch = true;
        } else {
          button.classList.remove("._item_active_p2ini_35");
        }
      });

      // Если не нашли совпадения, убедимся что все кнопки неактивны
      if (!foundMatch) {
        document.querySelectorAll("._item_p2ini_1").forEach((btn) => {
          btn.classList.remove("._item_active_p2ini_35");
        });
      }

      // Обновляем состояние ошибки input
      updateInputErrorState();
    });

    // Обработчик потери фокуса input
    input.addEventListener("blur", function () {
      updateInputErrorState();
    });

    // Инициализация при загрузке
    const initialInputValue = parseInt(input.value) || 0;
    document.querySelectorAll("._item_p2ini_1").forEach((button) => {
      const buttonValue = getValueFromButton(button);
      if (buttonValue === initialInputValue) {
        button.classList.add("_item_active_p2ini_35");
      } else {
        button.classList.remove("._item_active_p2ini_35");
      }
    });

    // Проверяем начальное состояние input
    updateInputErrorState();
  }

  // --- Обработка блока с методами оплаты ---
  const paymentMethodsContainer = document.querySelector("._plate_121bb_1");

  if (paymentMethodsContainer) {
    // Обработчик клика по методам оплаты
    paymentMethodsContainer.addEventListener("click", function (e) {
      const button = e.target.closest("._button_45oih_1");
      if (!button) return;

      // Удаляем активные классы у всех кнопок и их внутренних div
      document.querySelectorAll("._button_45oih_1").forEach((btn) => {
        btn.classList.remove("._button_active_45oih_22");
        const paymentDiv = btn.querySelector("._payment_45oih_25");
        if (paymentDiv) {
          paymentDiv.classList.remove("._payment_active_45oih_66");
        }
      });

      // Добавляем активные классы к выбранной кнопке
      button.classList.add("._button_active_45oih_22");
      const paymentDiv = button.querySelector("._payment_45oih_25");
      if (paymentDiv) {
        paymentDiv.classList.add("._payment_active_45oih_66");
      }
    });

    // Инициализация при загрузке - активируем кнопку, которая уже активна
    const element = document.querySelector(
      "._button_45oih_1._button_active_45oih_22, ._payment_45oih_25._payment_active_45oih_66"
    );
    const initialActiveButton = element ? element.closest("._button_45oih_1") : null;
    if (initialActiveButton) {
      initialActiveButton.classList.add("._button_active_45oih_22");
      const paymentDiv =
        initialActiveButton.querySelector("._payment_45oih_25");
      if (paymentDiv) {
        paymentDiv.classList.add("._payment_active_45oih_66");
      }
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Находим элементы
  const container = document.querySelector(
    'div[style*="transform-origin: center top"]'
  );
  const scrollContainer = container.querySelector(
    "._plate_121bb_1._plate_121bb_133"
  );
  const scrollLeftBtn = container.querySelector("._scrollButton_left_1604q_30");
  const scrollRightBtn = container.querySelector(
    "._scrollButton_right_1604q_34"
  );

  if (!scrollContainer || !scrollLeftBtn || !scrollRightBtn) return;

  // Добавляем необходимые стили для контейнера скролла
  scrollContainer.style.overflowX = "auto";
  scrollContainer.style.overflowY = "hidden";
  scrollContainer.style.whiteSpace = "nowrap";
  scrollContainer.style.display = "flex";
  scrollContainer.style.flexWrap = "nowrap";
  scrollContainer.style.scrollBehavior = "smooth";
  scrollContainer.style.gridTemplateColumns = "none"; // Убираем grid

  // Инициализация - скрываем левую кнопку при загрузке
  scrollLeftBtn.style.display = "none";

  // Функция для проверки позиции скролла
  function updateScrollButtons() {
    const scrollLeft = scrollContainer.scrollLeft;
    const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;

    // Левая кнопка
    if (scrollLeft > 10) {
      scrollLeftBtn.style.display = "flex";
    } else {
      scrollLeftBtn.style.display = "none";
    }

    // Правая кнопка
    if (scrollLeft >= maxScroll - 10) {
      scrollRightBtn.style.display = "none";
    } else {
      scrollRightBtn.style.display = "flex";
    }
  }

  // Обработчики кнопок
  scrollRightBtn.addEventListener("click", function () {
    scrollContainer.scrollBy({
      left: 100,
      behavior: "smooth",
    });
  });

  scrollLeftBtn.addEventListener("click", function () {
    scrollContainer.scrollBy({
      left: -100,
      behavior: "smooth",
    });
  });

  // Обработчик скролла
  scrollContainer.addEventListener("scroll", updateScrollButtons);

  // Обработчик колесика мыши
  scrollContainer.addEventListener("wheel", function (e) {
    e.preventDefault();
    scrollContainer.scrollLeft += e.deltaY;
  });

  // Проверяем нужно ли скрыть правую кнопку при загрузке
  setTimeout(updateScrollButtons, 100);

  // Обновляем при изменении размера
  window.addEventListener("resize", updateScrollButtons);
});
