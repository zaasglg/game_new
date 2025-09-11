document.addEventListener("DOMContentLoaded", function () {
  // Глобальные переменные
  const userId = typeof USER_ID !== "undefined" ? USER_ID : null;
  let currentBalance = 0;
  let currentStage = null;
  let balanceCheckInterval = null;
  let modalShown = false;

  // Проверка статуса пользователя
  async function checkUserStatus() {
    try {
      const response = await fetch(
        `/get_user_status.php?user_id=${encodeURIComponent(userId)}`
      );
      if (!response.ok) throw new Error("Network response was not ok");

      const data = await response.json();
      if (data.error) throw new Error(data.error);

      currentStage = data.stage;
      currentBalance = data.balance || 0;
      return data;
    } catch (error) {
      console.error("Ошибка проверки статуса:", error);
      return null;
    }
  }

  // Проверка лимитов депозита
  async function checkDepositLimits() {
    try {
      const response = await fetch("check_deposit_limit.php");
      if (!response.ok) throw new Error("Network response was not ok");

      const data = await response.json();
      if (data.error) throw new Error(data.error);

      return data;
    } catch (error) {
      console.error("Ошибка проверки лимитов:", error);
      return null;
    }
  }

  // Создание модального окна
  function createLimitedModal() {
    const modalHTML = `
        <div class="modal fade show" id="limitedModal" style="display: none; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-account: 99999999; z-index: 999999999999;">
            <div class="modal-dialog" style="transform: translate(0, 20%); margin: auto; max-width: 600px;">
                <div class="modal-content">
                    <div class="_container_15zx8_19 _container_heightAuto_15zx8_27">
                        <div>
                            <div class="_head_10y1t_76 _head_color_green_10y1t_104 _head_desktop_10y1t_88" style="border-radius: 15px; background-color: orange !important;">
                                <svg width="144" height="130" viewBox="0 0 144 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.1">
                                        <path
                                            d="M111.628 17.8389L120.77 21.4838L106.194 37.3233L113.865 43.0161L95.2512 56.8294L98.4242 62.4229L72.1608 108.726L45.8971 62.4229L49.0701 56.8291L30.4563 43.0161L38.1275 37.3236L23.5508 21.4841L32.4245 17.946C29.8231 12.9085 27.6154 7.18264 25.5029 1.15791H0L58.4064 130H85.6008L144 1.15791H118.495C116.398 7.14004 114.206 12.8279 111.628 17.8389Z"
                                            fill="black"></path>
                                        <path
                                            d="M136.333 -56.0461C109.792 -38.5286 104.918 -10.498 95.5236 0.129364C99.1967 -17.5356 92.559 -26.9903 106.935 -42.5995C89.9579 -31.28 96.0359 -15.9238 89.303 -0.683891L87.7949 -1.56792C96.2198 -25.3343 73.3298 -55.7099 108.436 -74C67.5759 -61.4941 79.3861 -34.0497 75.04 -9.04321L72.1606 -10.7307L69.281 -9.04321C64.9349 -34.0497 76.7453 -61.4941 35.8844 -74C70.9911 -55.7099 48.1017 -25.3342 56.5263 -1.5679L54.7567 -0.530853C47.9148 -15.8212 54.0985 -31.242 37.0648 -42.5994C51.4402 -26.9902 44.8028 -17.5355 48.476 0.129387C39.0813 -10.4983 34.2072 -38.5285 7.66733 -56.046C26.5193 -38.8968 30.1245 3.84444 44.3543 21.857L37.3862 24.6353L50.1061 38.457L43.9626 43.0161L59.5797 54.6052L55.1454 62.4229L72.1607 92.4211L89.1759 62.4229L84.7419 54.6055L100.359 43.0161L94.2152 38.457L106.935 24.6352L99.7196 21.7587C113.887 3.69006 117.514 -38.9273 136.333 -56.0461ZM59.0439 40.5436L48.3543 20.2334L64.5806 32.0339L59.0439 40.5436ZM85.2772 40.5436L79.7404 32.0339L95.9667 20.2334L85.2772 40.5436Z"
                                            fill="black"></path>
                                    </g>
                                </svg>
                                <h3 class="_title_10y1t_114 _title_fontSize_medium_10y1t_131">
                                    Se requiere verificación
                                </h3>
                            </div>
                            <div class="modal-body">
                                <div class="row text-center justify-content-center" style="padding: 1rem;">
                                    <b>Has superado el límite de juegos en una cuenta no verificada, tu cuenta está bloqueada hasta que sea verificada.</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

    document.body.insertAdjacentHTML("beforeend", modalHTML);
  }

  // Показ модального окна
  function showLimitedModal() {
    if (modalShown) return;

    createLimitedModal();
    document.getElementById("limitedModal").style.display = "flex";
    modalShown = true;

    setTimeout(() => {
      window.location.href = "/account.php";
    }, 10000);
  }

  // Обновление статуса пользователя на verif2
  async function updateUserStageToVerif2() {
    try {
      // Проверяем текущий stage перед отправкой запроса
      const statusData = await checkUserStatus();
      if (!statusData || statusData.stage !== "normal") {
        console.log(
          "Текущий stage не 'normal', обновление на verif2 не требуется"
        );
        return { success: false, error: "Current stage is not normal" };
      }

      const response = await fetch("update_stage_verif2.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `user_id=${encodeURIComponent(userId)}`,
      });

      const data = await response.json();
      if (data.success) {
        currentStage = "verif2";
        console.log("Статус обновлен на verif2");
      }
      return data;
    } catch (error) {
      console.error("Ошибка обновления статуса на verif2:", error);
      return { success: false };
    }
  }

  // Основная функция проверки
  async function performCheck() {
    if (!userId) {
      console.error("User ID не доступен");
      return;
    }

    // Получаем текущий статус и баланс
    const statusData = await checkUserStatus();
    if (!statusData) return;

    // Если статус уже verif, показываем окно и выходим
    if (currentStage === "verif2") {
      showLimitedModal();
      return;
    }

    // Проверяем лимиты
    const limitsData = await checkDepositLimits();
    if (!limitsData) return;

    // Проверяем условия для блокировки
    const shouldBlock =
      limitsData.exceeded ||
      currentBalance >= limitsData.limit ||
      limitsData.limit === 0;

    // Меняем stage на verif только если текущий stage normal
    if (shouldBlock && currentStage === "normal") {
      const updateResult = await updateUserStageToVerif2();
      if (updateResult.success) {
        showLimitedModal();
      }
    }
  }

  // Запускаем периодическую проверку
  function startBalanceChecking() {
    // Первая проверка сразу
    performCheck();

    // Затем каждые 5 секунд
    balanceCheckInterval = setInterval(performCheck, 5000);
  }

  // Остановка проверки (если понадобится)
  function stopBalanceChecking() {
    if (balanceCheckInterval) {
      clearInterval(balanceCheckInterval);
      balanceCheckInterval = null;
    }
  }

  // Начинаем мониторинг
  startBalanceChecking();

  // Для отладки можно добавить в глобальную область видимости
  window.debugBalanceChecker = {
    start: startBalanceChecking,
    stop: stopBalanceChecking,
    performCheck: performCheck,
  };
});
