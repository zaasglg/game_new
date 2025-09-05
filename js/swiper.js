document.addEventListener("DOMContentLoaded", function () {
  // Находим все контейнеры слайдеров
  const sliderContainers = document.querySelectorAll("._slider_14oe3_17");

  // Если нет ни одного слайдера, выходим
  if (!sliderContainers.length) return;

  // Инициализируем каждый слайдер
  sliderContainers.forEach((container) => {
    initSlider(container);
  });

  function initSlider(sliderContainer) {
    const swiper = sliderContainer.querySelector(".swiper");
    if (!swiper) return;

    const swiperWrapper = swiper.querySelector(".swiper-wrapper");
    const slides = swiper.querySelectorAll(".swiper-slide");
    const buttonsContainer = swiper.querySelector("._buttons_18u6m_1");
    if (!swiperWrapper || !slides.length || !buttonsContainer) return;

    const nextButton = buttonsContainer.querySelector("._button_next_18u6m_48");
    const prevButton = buttonsContainer.querySelector(
      "._button_18u6m_1:not(._button_next_18u6m_48)"
    );
    if (!nextButton || !prevButton) return;

    // Настройки слайдера
    const slideWidth =
      slides[0].offsetWidth + parseInt(getComputedStyle(slides[0]).marginRight);
    const slidesPerScroll = 1; // Теперь листаем по 1 слайду
    let currentIndex = 0;
    let isDragging = false;
    let startPosX = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let animationID = 0;

    // Рассчитываем максимальный индекс с учетом видимой области
    const visibleSlides = Math.floor(swiper.offsetWidth / slideWidth);
    const maxIndex = Math.max(0, slides.length - visibleSlides);

    // Устанавливаем ширину wrapper
    swiperWrapper.style.width = `${slideWidth * slides.length}px`;

    // Обработчики событий
    slides.forEach((slide) => {
      slide.addEventListener("dragstart", (e) => e.preventDefault());
      ["touchstart", "mousedown"].forEach((e) =>
        slide.addEventListener(e, touchStart)
      );
      ["touchend", "mouseup", "mouseleave"].forEach((e) =>
        slide.addEventListener(e, touchEnd)
      );
      ["touchmove", "mousemove"].forEach((e) =>
        slide.addEventListener(e, touchMove)
      );
    });

    nextButton.addEventListener("click", () =>
      moveToSlide(currentIndex + slidesPerScroll)
    );
    prevButton.addEventListener("click", () =>
      moveToSlide(currentIndex - slidesPerScroll)
    );
    swiper.addEventListener("contextmenu", (e) => e.preventDefault());

    // Обработчик ресайза окна
    window.addEventListener("resize", handleResize);

    updateButtons();

    function touchStart(e) {
      if (e.type === "mousedown") e.preventDefault();
      isDragging = true;
      startPosX = getPositionX(e);
      prevTranslate = -currentIndex * slideWidth;
      swiperWrapper.style.transition = "none";
      animationID = requestAnimationFrame(animation);
    }

    function touchEnd() {
      if (!isDragging) return;
      isDragging = false;
      cancelAnimationFrame(animationID);

      const movedBy = currentTranslate - prevTranslate;
      if (Math.abs(movedBy) > 30) {
        // Минимальное расстояние для срабатывания свайпа
        if (movedBy < 0) {
          moveToSlide(currentIndex + 1);
        } else {
          moveToSlide(currentIndex - 1);
        }
      } else {
        moveToSlide(currentIndex); // Возврат на текущую позицию
      }
    }

    function touchMove(e) {
      if (isDragging) {
        const currentPosX = getPositionX(e);
        currentTranslate = prevTranslate + currentPosX - startPosX;

        // Ограничиваем перемещение за границы
        const maxTranslate = 0;
        const minTranslate = -((slides.length - 1) * slideWidth);
        currentTranslate = Math.min(
          maxTranslate,
          Math.max(minTranslate, currentTranslate)
        );
      }
    }

    function animation() {
      setSliderPosition();
      if (isDragging) requestAnimationFrame(animation);
    }

    function setSliderPosition() {
      swiperWrapper.style.transform = `translateX(${currentTranslate}px)`;
    }

    function getPositionX(e) {
      return e.type.includes("mouse") ? e.pageX : e.touches[0].clientX;
    }

    function moveToSlide(newIndex) {
      // Корректируем индекс, чтобы не выйти за границы
      newIndex = Math.max(0, Math.min(maxIndex, newIndex));

      if (newIndex !== currentIndex) {
        currentIndex = newIndex;
        currentTranslate = -currentIndex * slideWidth;
        smoothTransition();
        updateButtons();
      }
    }

    function smoothTransition() {
      swiperWrapper.style.transition =
        "transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      setSliderPosition();

      swiperWrapper.addEventListener(
        "transitionend",
        () => {
          swiperWrapper.style.transition = "none";
        },
        { once: true }
      );
    }

    function updateButtons() {
      prevButton.disabled = currentIndex <= 0;
      nextButton.disabled = currentIndex >= maxIndex;

      if (prevButton.disabled) {
        prevButton.classList.add("_button_hidden_18u6m_51");
      } else {
        prevButton.classList.remove("_button_hidden_18u6m_51");
      }

      if (nextButton.disabled) {
        nextButton.classList.add("_button_hidden_18u6m_51");
      } else {
        nextButton.classList.remove("_button_hidden_18u6m_51");
      }
    }

    function handleResize() {
      // Пересчитываем максимальный индекс при изменении размера окна
      const newVisibleSlides = Math.floor(swiper.offsetWidth / slideWidth);
      const newMaxIndex = Math.max(0, slides.length - newVisibleSlides);

      // Если текущий индекс стал больше нового максимума, корректируем его
      if (currentIndex > newMaxIndex) {
        currentIndex = newMaxIndex;
        currentTranslate = -currentIndex * slideWidth;
        setSliderPosition();
      }

      // Обновляем кнопки
      updateButtons();
    }
  }
});
