<?php
session_start();
include 'overlaying.php';

// Проверяем, вошел ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Перенаправление на страницу входа
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" translate="no">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="google" content="notranslate" />
  <link rel="stylesheet" href="./css/main.css" />
  <link rel="stylesheet" href="./css/mines.css" />
  <link rel="stylesheet" href="./css/icons.css" />
  <link rel="stylesheet" href="./css/style.css" />
  <title>Mines</title>
  <link rel="icon" href="./images/default.svg" />
  <link rel="canonical" href />
  <script src="./js/jquery.js"></script>
      <script>
    // Дополнительная подстраховка для загрузки
    window.addEventListener('load', function() {
      [].slice.call(document.querySelectorAll('.diamond, .bomb')).forEach(function(el) {
        el.style.backgroundImage = el.style.backgroundImage;
      });
    });

    (function() {
      const images = [
        '../images/star.png',
        '../images/mines.png'
      ];
  
      images.forEach(img => {
        new Image().src = img;
      });
    })
    ();
  </script>
  <style>
    .game-tile._win {
      background-image: url("../user/images/star.png") !important;
    }

    html,
    body {
      overflow-y: visible !important;
      height: auto !important;
      min-height: 100%;
    }

    body {
      position: relative;
    }

    #tbg {
      position: absolute;
      z-index: -1;
      width: 100%;
      height: 100%;
      background: #000 url(./images/bg.png) center center no-repeat;
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
    }

    #app {
      position: relative;
      z-index: 5;
    }

    #post-message-size {
      max-width: 500px;
    }

    .game-header {
      height: auto;
      text-align: center;
      padding: 0;
      padding-top: 20px;
    }

    @media screen and (max-height: 667px) {
      .game-header {
        padding-top: 0;
      }
    }

    .game-header img {
      width: 24%;
      max-width: 400px;
      height: auto;
      margin: 0px auto;
    }

    .tabs__item {
      width: 100%;
    }

    .tabs__item-inner {
      width: 100%;
      height: 100px;
      display: flex;
      flex-flow: row-nowrap;
      justify-content: stretch;
      align-items: center;
      padding: 3px;
    }

    .settings-input__wrapper {
      flex-grow: 1;
      flex-shrink: 1;
    }

    #num_lose_fields {
      display: flex;
      flex-flow: row nowrap;
      justify-content: stretch;
      align-items: center;
      gap: 15px;
      padding: 0 10px;
      margin: 0;
      background: transparent;
      box-shadow: none;
    }

    #num_lose_fields .button {
      flex-grow: 0;
      flex-shrink: 0;
      position: relative;
      left: 0;
      right: 0;
      top: 0;
    }

    #num_lose_fields .games-input__wrapper {
      flex-grow: 1;
      flex-shrink: 1;
      width: 50%;
      background-color: #620000;

      box-shadow: none;
    }

    .bombs_flex {
      display: flex;
      flex-flow: row nowrap;
      justify-content: center;
      align-items: center;
      gap: 15px;
      width: 100%;
    }

    .bomba img {
      width: 50px;
    }

    .game-container {
      display: flex;
      flex-flow: column nowrap;
      justify-content: start;
      align-items: stretch;
      gap: 10px;
      padding: 0px;
      margin: 0px;
    }

    .button__inner .button__text {
      font-size: 24px;
    }

    .chart-wrapper {
      margin: 20px;
      margin-bottom: 0;
      padding: 20px;
      background: url(./images/bg_game_block.png);
      background-repeat: no-repeat;
      background-position: center;
      background-size: 130%;
    }

    @media screen and (max-height: 667px) {
      .chart-wrapper {
        margin: 10px 40px 0px 40px;
      }
    }

    #mines_count_wrapper {
      justify-content: center;
      padding: 5px;
      margin: 0;

      .tabs__item {
        width: 90%;
      }

      .tabs__item-inner {
        height: 80px;
      }
    }

    #game_field {
      grid-gap: 12px;
    }

    #start_wrapper {
      justify-content: center;

      .tabs__item {
        text-align: center;
        justify-content: center;
        width: 90%;

        .tabs__item-inner {
          text-align: center;
          justify-content: center;
          height: 80px;
        }
      }
    }

    #main_proc {
      width: 98%;
      height: 90%;
      color: #fff;
      text-shadow: -1px -1px 2px rgb(1, 43, 104);
      font-size: 50px;
      font-weight: bold;
      text-align: center;
      border: 0;
      background: rgb(244, 102, 24);
      background: linear-gradient(90deg,
          rgba(244, 102, 24, 1) 0%,
          rgba(248, 144, 51, 1) 50%,
          rgba(253, 186, 77, 1) 100%);
      background-size: 150%;
      cursor: pointer;
      transition: all 0.5s linear;
      border-radius: 10px;

      &.active {
        background: rgb(244, 102, 24);
        background: linear-gradient(90deg,
            rgba(244, 102, 24, 1) 0%,
            rgba(248, 144, 51, 1) 50%,
            rgba(253, 186, 77, 1) 100%);
      }
    }

    .game-tile {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 50px;
      text-align: center;
      line-height: 100px;
      background: url(".//images/icon_game.png");
      background-size: contain;
      border-radius: 13px;
      aspect-ratio: 1;
    }
  </style>
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body class="mines _loaded">
  <div id="app" class="mines" style="overflow-y: visible !important; position: relative">
    <div id="post-message-size" class="game-wrapper" style="overflow-y: visible !important; z-index: 6; height: 100vh">
      <div id="tbg"></div>
      <div class="game-header">
        <img id="logo" src="./images/mines-content-logo.webp" alt="logo" />
      </div>

      <div id="modalPresetGames" class="modal" style="display: none;">
        <div class="modal-content">
          <span id="closeModal" class="close">&times;</span>
          <h2>Preset Games</h2>
          <div id="editableContent"></div>
          <button id="addGame">Add a game</button>
          <button id="saveChanges">Save changes</button>
        </div>
      </div>

      <div class="game-container">
        <div class="chart-wrapper">
          <div class="table-holder" style="position: relative">
            <div class="game-tiles" id="game_field"></div>
          </div>
        </div>

      </div>

      <div class="overlaying">
        <p>
          <span class="translate" data-key="overlay_text_p" id="overlay-text-p">Para activar la versión funcional es
            necesario realizar un depósito, por favor escribe a Fabio</span>
        </p>
        <button class="btn__overlaying translate" type="button" data-key="make_deposit"
          data-url="https://t.me/Dominguez_Fabio_Bot">
          Escríbeme
        </button>
        <script>
          // Получаем все кнопки с классом btn__overlaying
          const buttons = document.querySelectorAll('.btn__overlaying');

          // Добавляем обработчик событий для каждой кнопки
          buttons.forEach(button => {
            button.addEventListener('click', function () {
              // Получаем URL из атрибута data-url
              const url = this.getAttribute('data-url');
              // Переходим по указанному URL
              if (url) {
                window.location.href = url;
              }
            });
          });</script>
      </div>

      <footer class="footer" style="position: absolute;width: 100%;bottom: 0; z-index: 200;">
        <a class="footer__link home" href="home.php">
          <img src="./images/home.webp" alt="home" />
          <p class="translate" data-key="home">Home</p>
        </a>
        <a class="footer__link aviator" href="aviator.php">
          <img src="./images/aviator.webp" alt="aviator" />
          <p class="translate" data-key="aviator">Aviator</p>
        </a>
        <a class="footer__link mines active_footer" href="mines.php">
          <img src="./images/mines.webp" alt="mines" />
          <p class="translate" data-key="mines">Mines</p>
        </a>
        <a class="footer__link chicken" href="chicken_road.php">
          <img src="./images/mines.webp" alt="chicken" />
          <p class="translate" data-key="chicken">Chicken</p>
        </a>
      </footer>
    </div>
  </div>

  <!-- <script>
// Получаем данные из PHP
const userId = <?= json_encode($_SESSION['user_id']); ?>;

// Проверка баланса при загрузке страницы
document.addEventListener("DOMContentLoaded", async function() {
  // Находим элементы модального окна
  const overlaying = document.querySelector('.overlaying');
  const overlayTextP = document.getElementById('overlay-text-p');
  const overlayButton = document.querySelector('.btn__overlaying');
  
  try {
    // Проверяем баланс пользователя
    const deposit = await checkUserDeposit();
    
    if (deposit < 10) {
      // Устанавливаем текст для модального окна
      overlayTextP.textContent = "To activate the full version you need to make a deposit, please contact Fabio";
      overlayButton.textContent = "Write me";
      
      // Показываем модальное окно
      overlaying.style.display = "flex";
    }
    // Если баланс >= 10, ничего не делаем
    
  } catch (error) {
    console.error("Deposit check failed:", error);
  }
  
  // Обработчик кнопки в модальном окне
  overlayButton.addEventListener('click', function() {
    const url = this.getAttribute('data-url');
    if (url) {
      window.open(url, '_blank');
    }
  });
});

// Функция проверки депозита
async function checkUserDeposit() {
  const response = await fetch('db.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=check_deposit&user_id=${userId}`
  });
  
  const data = await response.json();
  
  if (!data.success) {
    throw new Error(data.message || "Deposit check failed");
  }
  
  return parseFloat(data.deposit) || 0;
}
</script> -->


  <script src="./js/toggle.js"></script>
  <script src="./js/script.js"></script>
  <script>
    function App() {
      return {
        coeffs: {
          2: [1.03, 1.13, 1.23, 1.36, 1.5, 1.67, 1.86, 2.1, 2.38, 2.71, 3.13, 3.65, 4.32, 5.18, 6.33, 7.92, 10.18, 13.57, 19, 28.5, 47.5, 95, 285],
          3: [1.08, 1.23, 1.42, 1.64, 1.92, 2.25, 2.68, 3.21, 3.9, 4.8, 6, 7.64, 9.93, 13.24, 18.21, 26.01, 39.02, 62.43, 109.25, 218.5, 546.25, 2180],
          4: [1.13, 1.36, 1.64, 2.01, 2.48, 3.1, 3.93, 5.05, 6.6, 8.8, 12.01, 16.81, 24.28, 36.42, 57.23, 95.38, 171.68, 343.36, 801.17, 2400, 12001],
          5: [1.19, 1.5, 1.92, 2.48, 3.26, 4.34, 5.89, 8.16, 11.56, 16.81, 25.21, 39.22, 63.73, 109.25, 200.29, 400.58, 901.31, 2400, 8410, 50470],
          6: [1.25, 1.67, 2.25, 3.1, 4.34, 6.2, 9.06, 13.59, 21.01, 33.62, 56.03, 98.04, 182.08, 364.17, 801.17, 2000, 6000, 24030, 168240],
          7: [1.32, 1.86, 2.68, 3.93, 5.89, 9.06, 14.35, 23.48, 39.92, 70.97, 133.06, 266.12, 576.6, 1380, 3800, 12680, 57080, 456660],
          8: [1.4, 2.1, 3.21, 5.05, 8.16, 13.59, 23.48, 42.27, 79.84, 159.67, 342.16, 798.37, 2070, 6220, 22830, 114160],
          9: [1.48, 2.38, 3.9, 6.6, 11.56, 21.01, 39.92, 79.84, 169.65, 387.78, 969.44, 2710, 8820, 35280, 194080],
          10: [1.58, 2.71, 4.8, 8.8, 16.81, 33.62, 70.97, 159.67, 387.78, 1030, 3100, 10850, 47050, 282300],
          11: [1.7, 3.13, 6, 12.01, 25.21, 56.03, 133.06, 342.16, 969.44, 3100, 11630, 54280, 352870],
          12: [1.83, 3.65, 7.64, 16.81, 39.22, 98.04, 266.12, 798.37, 2710, 10850, 54280, 380020],
          13: [1.98, 4.32, 9.93, 24.28, 63.73, 182.08, 576.6, 2070, 8820, 47050, 352870],
          14: [2.16, 5.18, 13.24, 36.42, 109.25, 364.17, 1380, 6220, 35280, 282300],
          15: [2.38, 6.33, 18.21, 57.23, 200.29, 801.17, 3800, 22830, 194080],
          16: [2.64, 7.92, 26.01, 95.38, 400.58, 2000, 12680, 114160],
          17: [2.97, 10.18, 39.02, 171.68, 901.31, 6000, 57080],
          18: [3.39, 13.57, 62.43, 343.36, 2400, 24030, 456660],
          19: [3.96, 19, 109.25, 801.17, 8410, 168240],
          20: [4.75, 28.5, 218.5, 2400, 50470],
          21: [5.94, 47.5, 546.25, 12010],
          22: [7.92, 95, 2.18],
          23: [11.88, 285],
          24: [23.75],
          25: [50.0]
        },
        current_coeff: 0,
        num_lose_fields: 3,
        valute: "₺",
        min_cells: 2,
        max_cells: 25,
        timers: [],
        checkInterval: null,
        activeUserId: <?= $_SESSION['user_id'] ?? 0 ?>,
        positionsMine: [],
        randomized: [],

        init: function () {
          console.log("🚀 Инициализация Mines Hack Bot...");
          console.log("👤 ID пользователя:", this.activeUserId);
          
          if (!this.activeUserId) {
            console.error("❌ Пользователь не авторизован!");
            this.showStatus("Ошибка: Пользователь не авторизован");
            return;
          }

          this.showStatus("Загрузка hack bot...");
          this.prepareGame();
          this.startCheckingDatabase();
        },

        prepareGame: function () {
          for (var i = 0; i < this.timers.length; i++) {
            clearTimeout(this.timers[i]);
          }

          this.randomized = Array(this.max_cells).fill(0);
          this.drawGame(this.randomized);
        },

        drawGame: function (arr) {
          var $wrap = $("#game_field");
          $wrap.html("");

          for (var i = 0; i < arr.length; i++) {
            var type = arr[i];
            var $tmps = `<div class="game-tile hidden" data-id="${i + 1}">
                        <div class="game-tile__inner">
                          ${!type ? '<div class="diamond"></div>' : '<div class="bomb"></div>'}
                        </div>
                      </div>`;
            $wrap.append($tmps);
          }
        },

        updateMinesFromDatabase: function () {
          var $this = this;
          console.log("🔍 Проверяем базу данных для пользователя:", $this.activeUserId);

          $.ajax({
            url: "db-valor.php",
            type: "POST",
            data: {
              action: "get_user_mines",
              user_id: $this.activeUserId
            },
            dataType: "json",
            success: function (response) {
              console.log("📡 Ответ от сервера:", response);
              
              if (response && response.success) {
                $this.positionsMine = response.positions_mine || [];
                console.log("✅ Получены позиции мин:", $this.positionsMine);
                $this.updateMineField();
                
                // Показываем статус в интерфейсе
                $this.showStatus("Hack bot активен! Найдено " + $this.positionsMine.length + " безопасных клеток");
              } else {
                console.error("❌ Ошибка:", response ? response.message : 'Unknown error');
                $this.showStatus("Ошибка: " + (response ? response.message : 'Неизвестная ошибка'));
              }
            },
            error: function (xhr) {
              console.error("🚨 AJAX Ошибка:", xhr.responseText);
              $this.showStatus("Ошибка соединения с сервером");
            }
          });
        },

        updateMineField: function () {
          console.log("🎯 Обновляем игровое поле с позициями:", this.positionsMine);
          
          this.randomized = Array(this.max_cells).fill(0);

          this.positionsMine.forEach(function (position) {
            if (position >= 1 && position <= this.max_cells) {
              this.randomized[position - 1] = 1;
              console.log("⭐ Безопасная клетка на позиции:", position);
            }
          }.bind(this));

          this.drawGame(this.randomized);
          this.showMines();
        },

        showMines: function () {
          var $this = this;
          console.log("✨ Показываем безопасные клетки...");

          $('#game_field .game-tile').removeClass('_active _win _loading');

          this.positionsMine.forEach(function (position) {
            if (position >= 1 && position <= this.max_cells) {
              var $victim = $('#game_field .game-tile[data-id="' + position + '"]');
              console.log("🌟 Отмечаем клетку", position, "как безопасную");
              
              $victim.addClass("_loading").removeClass("hidden");

              setTimeout(function () {
                $victim.addClass("_win");
                setTimeout(function () {
                  $victim.addClass("_active");
                }, 100);
              }, 100);

              setTimeout(function () {
                $victim.removeClass("_loading");
              }, 300);
            }
          }.bind(this));
        },

        showStatus: function (message) {
          console.log("📢 Статус:", message);
          
          // Создаем элемент статуса если его нет
          if (!$('#hack-status').length) {
            $('.game-container').prepend('<div id="hack-status" style="background: rgba(0,255,0,0.8); color: white; padding: 10px; text-align: center; border-radius: 5px; margin: 10px;"></div>');
          }
          
          $('#hack-status').text(message);
        },

        startCheckingDatabase: function () {
          this.updateMinesFromDatabase();
          this.checkInterval = setInterval(this.updateMinesFromDatabase.bind(this), 5000);
        },

        stopCheckingDatabase: function () {
          if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
          }
        }
      };
    }

    $(document).ready(function () {
      window.$app = new App();
      $app.init();
    });
  </script>
    <script src="./js/lang.js"></script>


</body>

</html>