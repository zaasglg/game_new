// Устанавливаем активный пункт меню при загрузке страницы
document.addEventListener("DOMContentLoaded", function () {
  const activeItem =
    document.querySelector(".politics-menu__dropdown-item.active") ||
    document.querySelector(".politics-sidebar__item.active");

  if (activeItem) {
    const selectedText = activeItem.textContent;
    document.querySelector(".politics-menu__selected").textContent =
      selectedText;

    // Также добавляем класс active к соответствующему пункту в выпадающем списке
    const dropdownItems = document.querySelectorAll(
      ".politics-menu__dropdown-item"
    );
    dropdownItems.forEach((item) => {
      if (item.textContent === selectedText) {
        item.classList.add("active");
      }
    });
  }
});

function toggleDropdown(element) {
  const menu = element.closest(".politics-menu");
  menu.classList.toggle("active");

  // Update selected item text
  if (event.target.classList.contains("politics-menu__dropdown-item")) {
    const selectedText = event.target.textContent;
    menu.querySelector(".politics-menu__selected").textContent = selectedText;
    menu.classList.remove("active");

    // Удаляем класс active у всех пунктов и добавляем его к выбранному
    const dropdownItems = menu.querySelectorAll(
      ".politics-menu__dropdown-item"
    );
    dropdownItems.forEach((item) => {
      item.classList.remove("active");
    });
    event.target.classList.add("active");
  }
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const menus = document.querySelectorAll(".politics-menu");
  menus.forEach((menu) => {
    if (!menu.contains(event.target)) {
      menu.classList.remove("active");
    }
  });
});

// Update selected text when an item is clicked
document.querySelectorAll(".politics-menu__dropdown-item").forEach((item) => {
  item.addEventListener("click", function () {
    const selectedText = this.textContent;
    this.closest(".politics-menu").querySelector(
      ".politics-menu__selected"
    ).textContent = selectedText;

    // Удаляем класс active у всех пунктов и добавляем его к выбранному
    const dropdownItems = document.querySelectorAll(
      ".politics-menu__dropdown-item"
    );
    dropdownItems.forEach((item) => {
      item.classList.remove("active");
    });
    this.classList.add("active");
  });
});

