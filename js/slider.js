document.addEventListener('DOMContentLoaded', function() {
  let currentSlide = 0;
  const slides = document.querySelectorAll(".slide");
  const totalSlides = slides.length;
  let startX = 0;
  let isDragging = false;
  
  // Check if elements exist
  if (totalSlides === 0) return;
  
  const slider = document.querySelector(".slider");
  const slidesContainer = document.querySelector(".slides");
  
  if (!slider || !slidesContainer) return;

  function showSlide(index) {
    const slideWidth = slides[0].clientWidth;
    slidesContainer.style.transform = `translateX(${
      -slideWidth * index
    }px)`;
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
  }

  // Автолистание каждые 5 секунд
  setInterval(nextSlide, 5000);

  // Свайп мышкой или пальцем
  slider.addEventListener("mousedown", (e) => {
    isDragging = true;
    startX = e.pageX;
  });

  slider.addEventListener("mousemove", (e) => {
    if (!isDragging) return;
    const currentX = e.pageX;
    const diffX = startX - currentX;
    if (diffX > 50) {
      nextSlide();
      isDragging = false;
    } else if (diffX < -50) {
      prevSlide();
      isDragging = false;
    }
  });

  slider.addEventListener("mouseup", () => {
    isDragging = false;
  });

  slider.addEventListener("mouseleave", () => {
    isDragging = false;
  });

  // Для сенсорных устройств
  slider.addEventListener("touchstart", (e) => {
    isDragging = true;
    startX = e.touches[0].clientX;
  });

  slider.addEventListener("touchmove", (e) => {
    if (!isDragging) return;
    const currentX = e.touches[0].clientX;
    const diffX = startX - currentX;
    if (diffX > 50) {
      nextSlide();
      isDragging = false;
    } else if (diffX < -50) {
      prevSlide();
      isDragging = false;
    }
  });

  slider.addEventListener("touchend", () => {
    isDragging = false;
  });

  // Инициализация первого слайда
  showSlide(currentSlide);
});
