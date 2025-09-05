document.addEventListener("DOMContentLoaded", function () {
  // Находим все блоки с классом 'select'
  const selectBlocks = document.querySelectorAll(".select");

  // Перебираем все найденные блоки
  selectBlocks.forEach((block) => {
    // Добавляем обработчик клика на каждый блок
    block.addEventListener("click", function () {
      // Проверяем, есть ли у текущего блока активный класс
      const isActive = this.classList.contains("select--active");

      // Удаляем активный класс у всех блоков
      selectBlocks.forEach((b) => b.classList.remove("select--active"));

      // Если у кликнутого блока не было активного класса - добавляем
      if (!isActive) {
        this.classList.add("select--active");
      }
    });
  });
});