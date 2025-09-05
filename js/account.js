function validatePhoneNumber(input) {
  // Получаем максимальную длину и код страны из data-атрибутов
  const maxLength = parseInt(input.dataset.maxLength);
  const countryCode = input.dataset.countryCode;

  // Удаляем все нецифровые символы
  let value = input.value.replace(/\D/g, "");

  // Обрезаем до максимальной длины
  if (value.length > maxLength) {
    value = value.substring(0, maxLength);
  }

  // Форматируем номер в зависимости от кода страны
  let formattedValue = "";

  if (countryCode.startsWith("+1")) {
    // Североамериканский формат
    if (value.length > 0) {
      formattedValue = "(" + value.substring(0, 3);
    }
    if (value.length > 3) {
      formattedValue += ") " + value.substring(3, 6);
    }
    if (value.length > 6) {
      formattedValue += "-" + value.substring(6, 10);
    }
  } else {
    // Международный формат (большинство стран)
    if (value.length > 0) {
      formattedValue = "(" + value.substring(0, 3);
    }
    if (value.length > 3) {
      formattedValue += ") " + value.substring(3, 7);
    }
    if (value.length > 7) {
      formattedValue += "-" + value.substring(7);
    }
  }

  input.value = formattedValue;

  // Добавляем/удаляем класс для индикации валидности
  if (value.length === maxLength) {
    input.classList.add("valid");
    input.classList.remove("invalid");
  } else {
    input.classList.add("invalid");
    input.classList.remove("valid");
  }
}
