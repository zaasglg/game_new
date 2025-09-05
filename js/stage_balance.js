// stage_balance.js

/**
 * Функция для проверки и обновления stage balance
 * @param {string} userId - ID пользователя
 * @param {function} [onSuccess] - Колбэк при успешном обновлении
 * @param {function} [onError] - Колбэк при ошибке
 */
function checkAndUpdateStageBalance(userId, onSuccess, onError) {
  fetch(
    `stage_balance_updater.php?action=check_stage_balance&user_id=${userId}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (data.processed_count > 0) {
          console.log(
            `Обновлен stage balance! Списано: $${data.deducted}. Новый баланс: $${data.new_balance}`
          );
        }

        if (data.stage_updated) {
          console.log("Stage обновлен в supp");
        }

        if (typeof onSuccess === "function") {
          onSuccess(data);
        }
      } else if (data.error) {
        console.error("Ошибка обновления баланса:", data.error);
        if (typeof onError === "function") {
          onError(data.error);
        }
      } else if (data.message) {
        console.log(data.message);
      }
    })
    .catch((error) => {
      console.error("Ошибка при проверке баланса:", error);
      if (typeof onError === "function") {
        onError(error);
      }
    });
}

/**
 * Инициализация автоматической проверки баланса
 * @param {string} userId - ID пользователя
 * @param {object} [options] - Настройки
 * @param {number} [options.interval=60000] - Интервал проверки в мс
 * @param {boolean} [options.immediate=true] - Проверить сразу при загрузке
 */
function initAutoBalanceCheck(userId, options = {}) {
  const { interval = 60000, immediate = true } = options;

  if (immediate) {
    checkAndUpdateStageBalance(userId);
  }

  return setInterval(() => checkAndUpdateStageBalance(userId), interval);
}
