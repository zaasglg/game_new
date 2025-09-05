document.addEventListener("DOMContentLoaded", function () {
  // Находим все элементы с классом toggle-password-input-svg
  const toggleIcons = document.querySelectorAll(".toggle-password-input-svg");

  toggleIcons.forEach((icon) => {
    icon.addEventListener("click", function () {
      // Находим ближайший input
      const input = icon
        .closest("._inputContent_1rq38_22")
        .querySelector(".toggle-password-input");

      // Меняем тип input с password на text и наоборот
      if (input.type === "password") {
        input.type = "text";
        icon.src = "./images/each.svg";
      } else {
        input.type = "password";
        icon.src = "../images/uneach.svg";
      }
    });
  });
});
