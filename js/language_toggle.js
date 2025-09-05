document.addEventListener("DOMContentLoaded", function () {
  // Load translations from JSON file
  let translations = {};
  const currentPage = window.location.pathname.split("/").pop() || "index.php";

  fetch("../translations.json")
    .then((response) => response.json())
    .then((data) => {
      translations = data;

      // Проверяем сохраненный язык в localStorage
      const savedLang = localStorage.getItem("selectedLanguage");
      const userExplicitChoice =
        localStorage.getItem("userExplicitChoice") === "true";
      const savedFlag = localStorage.getItem("selectedFlag");

      // 1. Если пользователь явно выбирал язык - используем его
      if (userExplicitChoice && savedLang) {
        applyTranslations(currentPage, savedLang);
        updateLanguageDropdown(savedLang, savedFlag);
        return;
      }

      // 2. Если есть язык по стране (из PHP)
      if (typeof userDefaultLang !== "undefined") {
        setLanguageByCountry(currentPage, userDefaultLang);
      }
      // 3. Используем сохраненный язык или испанский по умолчанию
      else {
        const langToUse = savedLang || "es";
        applyTranslations(currentPage, langToUse);
        updateLanguageDropdown(langToUse, savedFlag);
      }
    })
    .catch((error) => console.error("Error loading translations:", error));

  // Функция для применения переводов
  function applyTranslations(page, lang) {
    // Сначала проверяем переводы для текущей страницы
    let pageTranslations = translations[page] || {};

    // Затем проверяем общие переводы (common)
    let commonTranslations = translations.common || {};

    // Объединяем переводы, при этом переводы для конкретной страницы имеют приоритет
    let combinedTranslations = {
      ...commonTranslations,
      ...pageTranslations,
    };

    // Обрабатываем все элементы с data-translate
    document.querySelectorAll("[data-translate]").forEach((element) => {
      const fullKey = element.getAttribute("data-translate");
      const [section, key] = fullKey.split(".");
      const translation = combinedTranslations[section]?.[key]?.[lang];

      if (!translation) return;

      // Обработка meta и title
      if (element.tagName === "META") {
        element.setAttribute("content", translation);
        return;
      }
      if (element.tagName === "TITLE") {
        element.textContent = translation;
        return;
      }

      // Для input элементов - обновляем placeholder
      if (element.tagName === "INPUT" && element.hasAttribute("placeholder")) {
        element.setAttribute("placeholder", translation);
        return;
      }

      // Для кнопок - специальная обработка
      if (element.tagName === "BUTTON") {
        const img = element.querySelector("img");
        if (img) {
          element.innerHTML = `${translation} ${img.outerHTML}`;
          return;
        }
      }

      // Для параграфов - сохраняем HTML-структуру
      if (element.tagName === "P") {
        element.innerHTML = translation.replace(/\n/g, "<br>");
        return;
      }

      // Стандартная обработка для других элементов
      const children = Array.from(element.childNodes);
      const firstTextNode = children.find(
        (node) => node.nodeType === Node.TEXT_NODE && node.textContent.trim()
      );

      if (firstTextNode) {
        firstTextNode.textContent = translation;
      } else {
        const fragment = document.createDocumentFragment();
        fragment.appendChild(document.createTextNode(translation));
        if (children.length > 0) {
          fragment.appendChild(document.createTextNode(" "));
        }
        element.insertBefore(fragment, element.firstChild);
      }
    });
  }

  // Функция для обновления переключателя языка
  function updateLanguageDropdown(lang, flag) {
    document.querySelectorAll(".language-dropdown").forEach((dropdown) => {
      const currentFlag = dropdown.querySelector(".language-dropdown-flag img");
      const currentLanguage = dropdown.querySelector(
        ".language-dropdown__single-value span"
      );
      const currentOption = dropdown.querySelector(
        `.language-dropdown__option[data-lang="${lang}"]`
      );

      if (currentFlag) currentFlag.src = flag;
      if (currentLanguage) {
        // Обновляем текст
        currentLanguage.textContent =
          translations.common?.footer?.[
            lang === "es" ? "spanish" : 
            lang === "pt" ? "portuguese" : 
            lang === "fr" ? "french" : 
            lang === "ar" ? "arabic" : 
            "english"
          ]?.[lang] ||
          (lang === "es" ? "Spanish" :
           lang === "pt" ? "Portuguese" :
           lang === "fr" ? "Français" :
           lang === "ar" ? "العربية" :
           "English");

        // Обновляем data-translate в зависимости от выбранного языка
        currentLanguage.setAttribute(
          "data-translate",
          `footer.${
            lang === "es" ? "spanish" : 
            lang === "pt" ? "portuguese" : 
            lang === "fr" ? "french" : 
            lang === "ar" ? "arabic" : 
            "english"
          }`
        );
      }
    });
  }

  // Функция для установки языка по стране
  function setLanguageByCountry(page, lang) {
    const flagMap = {
      es: "../images/es.svg",
      pt: "../images/port.svg",
      en: "../images/eng.svg",
      fr: "../images/fr.svg",
      ar: "../images/ar.svg"
    };

    localStorage.setItem("selectedLanguage", lang);
    localStorage.setItem("selectedFlag", flagMap[lang]);
    localStorage.setItem("userExplicitChoice", "true");

    updateLanguageDropdown(lang, flagMap[lang]);
    applyTranslations(page, lang);
  }

  // Инициализация языкового переключателя
  document.querySelectorAll(".language-dropdown").forEach((dropdown) => {
    const control = dropdown.querySelector(".language-dropdown__control");
    const menu = dropdown.querySelector(".language-dropdown__menu");
    const options = dropdown.querySelectorAll(".language-dropdown__option");

    // Обработчик клика по переключателю
    control?.addEventListener("click", function (e) {
      e.stopPropagation();
      const isOpen = dropdown.classList.contains("language-dropdown--open");

      // Закрываем все открытые меню
      document.querySelectorAll(".language-dropdown").forEach((d) => {
        d.classList.remove("language-dropdown--open");
        const dMenu = d.querySelector(".language-dropdown__menu");
        dMenu.style.display = "none";
        
        // Сохраняем стили высоты даже при закрытии
        dMenu.style.maxHeight = "400px";
        dMenu.style.height = "auto";
        
        const dMenuList = dMenu.querySelector(".language-dropdown__menu-list");
        if (dMenuList) {
          dMenuList.style.maxHeight = "400px";
          dMenuList.style.overflowY = "auto";
          dMenuList.style.height = "auto";
        }
      });

      // Открываем текущее меню если оно было закрыто
      if (!isOpen) {
        dropdown.classList.add("language-dropdown--open");
        menu.style.display = "block";
        menu.style.maxHeight = "400px";
        menu.style.height = "auto";
        
        // Устанавливаем стили для menu-list
        const menuList = menu.querySelector(".language-dropdown__menu-list");
        if (menuList) {
          menuList.style.maxHeight = "400px";
          menuList.style.overflowY = "auto";
          menuList.style.height = "auto";
        }
      }
    });

    // Обработчики для вариантов языка
    options.forEach((option) => {
      option.addEventListener("click", function () {
        const lang = this.getAttribute("data-lang");
        const flag = this.getAttribute("data-flag");

        localStorage.setItem("selectedLanguage", lang);
        localStorage.setItem("selectedFlag", flag);
        localStorage.setItem("userExplicitChoice", "true");

        updateLanguageDropdown(lang, flag);
        applyTranslations(currentPage, lang);
      });
    });
  });

  // Закрываем меню при клике вне его
  document.addEventListener("click", function () {
    document.querySelectorAll(".language-dropdown").forEach((dropdown) => {
      dropdown.classList.remove("language-dropdown--open");
      const menu = dropdown.querySelector(".language-dropdown__menu");
      menu.style.display = "none";
      
      // Сохраняем стили высоты
      menu.style.maxHeight = "400px";
      menu.style.height = "auto";
      
      const menuList = menu.querySelector(".language-dropdown__menu-list");
      if (menuList) {
        menuList.style.maxHeight = "400px";
        menuList.style.overflowY = "auto";
        menuList.style.height = "auto";
      }
    });
  });
});
