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
  <link rel="stylesheet" href="./css/icons.css" />
  <link rel="stylesheet" href="./css/style.css" />
  <title>Chicken Road Hack Bot</title>
  <link rel="icon" href="./images/default.svg" />
  <script src="./js/jquery.js"></script>
  
  <style>
    html,
    body {
      overflow-y: visible !important;
      height: auto !important;
      min-height: 100%;
    }

    body {
      position: relative;
      background: linear-gradient(135deg, #0f1419 0%, #1a2332 100%);
      color: white;
      font-family: 'Arial', sans-serif;
    }

    #app {
      position: relative;
      z-index: 5;
      padding: 20px;
      max-width: 800px;
      margin: 0 auto;
    }

    .hack-header {
      text-align: center;
      padding: 20px 0;
      border-bottom: 2px solid #333;
      margin-bottom: 30px;
    }

    .hack-header h1 {
      color: #ffd700;
      font-size: 2.5em;
      margin: 0;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .hack-header .subtitle {
      color: #ccc;
      font-size: 1.2em;
      margin: 10px 0;
    }

    .prediction-panel {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 25px;
      margin: 20px 0;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .prediction-panel h3 {
      color: #00ff88;
      margin-top: 0;
      font-size: 1.5em;
    }

    .multiplier-display {
      background: linear-gradient(45deg, #ff6b35, #f7931e);
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      margin: 15px 0;
    }

    .multiplier-display .value {
      font-size: 2.5em;
      font-weight: bold;
      color: white;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .positions-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 10px;
      margin: 20px 0;
    }

    .position-cell {
      aspect-ratio: 1;
      border: 2px solid #333;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.1);
    }

    .position-cell.safe {
      background: linear-gradient(45deg, #00ff88, #00cc6a);
      border-color: #00ff88;
      color: white;
    }

    .position-cell.danger {
      background: rgba(255, 0, 0, 0.3);
      border-color: #ff0000;
      color: #ff6666;
    }

    .status-panel {
      background: rgba(0, 255, 136, 0.2);
      border: 1px solid #00ff88;
      border-radius: 10px;
      padding: 15px;
      margin: 20px 0;
      text-align: center;
    }

    .status-panel.error {
      background: rgba(255, 0, 0, 0.2);
      border-color: #ff0000;
      color: #ff6666;
    }

    .control-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin: 25px 0;
    }

    .control-buttons button {
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(45deg, #007bff, #0056b3);
      color: white;
    }

    .btn-success {
      background: linear-gradient(45deg, #28a745, #1e7e34);
      color: white;
    }

    .btn-warning {
      background: linear-gradient(45deg, #ffc107, #e0a800);
      color: #212529;
    }

    .control-buttons button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0, 0, 0, 0.9);
      backdrop-filter: blur(10px);
      z-index: 200;
    }

    .footer__link {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      padding: 10px;
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer__link.active_footer {
      color: #ffd700;
    }

    .footer__link img {
      width: 24px;
      height: 24px;
      margin-bottom: 5px;
    }
  </style>
</head>

<body class="chicken-hack _loaded">
  <div id="app">
    <div class="hack-header">
      <h1>🐔 Chicken Road Hack Bot</h1>
      <p class="subtitle">Predict safe positions and multipliers</p>
    </div>

    <div class="prediction-panel">
      <h3>📊 Current Prediction</h3>
      
      <div class="multiplier-display">
        <div class="label">Predicted Multiplier</div>
        <div class="value" id="multiplier-value">Loading...</div>
      </div>

      <h4>Safe Positions:</h4>
      <div class="positions-grid" id="positions-grid">
        <!-- Позиции курицы от 1 до 25 -->
      </div>
    </div>

    <div id="status-panel" class="status-panel">
      <p id="status-text">Initializing hack bot...</p>
    </div>

    <div class="control-buttons">
      <button class="btn-primary" onclick="refreshPrediction()">🔄 Refresh</button>
      <button class="btn-success" onclick="startAutoRefresh()">▶️ Auto Update</button>
      <button class="btn-warning" onclick="stopAutoRefresh()">⏸️ Stop</button>
    </div>

    <div class="overlaying">
      <p>
        <span class="translate">Para activar la versión funcional es necesario realizar un depósito, por favor escribe a Fabio</span>
      </p>
      <button class="btn__overlaying translate" type="button" data-url="https://t.me/Dominguez_Fabio_Bot">
        Escríbeme
      </button>
    </div>

  </div>

  <footer class="footer">
    <a class="footer__link home" href="home.php">
      <img src="./images/home.webp" alt="home" />
      <p>Home</p>
    </a>
    <a class="footer__link aviator" href="aviator.php">
      <img src="./images/aviator.webp" alt="aviator" />
      <p>Aviator</p>
    </a>
    <a class="footer__link mines" href="mines.php">
      <img src="./images/mines.webp" alt="mines" />
      <p>Mines</p>
    </a>
    <a class="footer__link chicken active_footer" href="chicken.php">
      <img src="./images/chicken.webp" alt="chicken" />
      <p>Chicken</p>
    </a>
  </footer>

  <script>
    function ChickenHackBot() {
      return {
        activeUserId: <?= $_SESSION['user_id'] ?? 0 ?>,
        checkInterval: null,
        safePositions: [],
        predictedMultiplier: 2.0,

        init: function () {
          console.log("🚀 Инициализация Chicken Road Hack Bot...");
          console.log("👤 ID пользователя:", this.activeUserId);
          
          if (!this.activeUserId) {
            console.error("❌ Пользователь не авторизован!");
            this.showStatus("Ошибка: Пользователь не авторизован", 'error');
            return;
          }

          this.showStatus("Загрузка hack bot...");
          this.createPositionsGrid();
          this.loadPrediction();
        },

        createPositionsGrid: function() {
          const grid = document.getElementById('positions-grid');
          grid.innerHTML = '';
          
          for (let i = 1; i <= 25; i++) {
            const cell = document.createElement('div');
            cell.className = 'position-cell';
            cell.textContent = i;
            cell.setAttribute('data-position', i);
            grid.appendChild(cell);
          }
        },

        loadPrediction: function () {
          console.log("🔍 Загружаем предсказание для пользователя:", this.activeUserId);

          fetch('db-chicken.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_chicken_prediction&user_id=' + this.activeUserId
          })
          .then(response => response.json())
          .then(data => {
            console.log("📡 Ответ от сервера:", data);
            
            if (data && data.success) {
              this.safePositions = data.safe_positions || [];
              this.predictedMultiplier = data.predicted_multiplier || 2.0;
              
              console.log("✅ Получены данные hack bot:");
              console.log("🛡️ Безопасные позиции:", this.safePositions);
              console.log("💰 Предсказанный множитель:", this.predictedMultiplier);
              
              this.updateDisplay();
              this.showStatus(`Hack bot активен! Найдено ${this.safePositions.length} безопасных позиций`);
            } else {
              console.error("❌ Ошибка:", data ? data.message : 'Unknown error');
              this.showStatus("Ошибка: " + (data ? data.message : 'Неизвестная ошибка'), 'error');
            }
          })
          .catch(error => {
            console.error("🚨 Fetch ошибка:", error);
            this.showStatus("Ошибка соединения с сервером", 'error');
          });
        },

        updateDisplay: function() {
          // Обновляем множитель
          document.getElementById('multiplier-value').textContent = this.predictedMultiplier + 'x';
          
          // Обновляем сетку позиций
          const cells = document.querySelectorAll('.position-cell');
          cells.forEach(cell => {
            const position = parseInt(cell.getAttribute('data-position'));
            cell.className = 'position-cell';
            
            if (this.safePositions.includes(position)) {
              cell.classList.add('safe');
              console.log("🛡️ Безопасная позиция:", position);
            } else {
              cell.classList.add('danger');
            }
          });
        },

        showStatus: function (message, type = 'normal') {
          console.log("📢 Статус:", message);
          
          const statusPanel = document.getElementById('status-panel');
          const statusText = document.getElementById('status-text');
          
          statusText.textContent = message;
          statusPanel.className = 'status-panel' + (type === 'error' ? ' error' : '');
        },

        startAutoRefresh: function() {
          this.loadPrediction();
          this.checkInterval = setInterval(() => {
            this.loadPrediction();
          }, 10000); // Обновляем каждые 10 секунд
          
          this.showStatus("Автообновление включено (каждые 10 сек)");
        },

        stopAutoRefresh: function() {
          if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
            this.showStatus("Автообновление отключено");
          }
        }
      };
    }

    // Глобальные функции для кнопок
    let hackBot;

    function refreshPrediction() {
      hackBot.loadPrediction();
    }

    function startAutoRefresh() {
      hackBot.startAutoRefresh();
    }

    function stopAutoRefresh() {
      hackBot.stopAutoRefresh();
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
      hackBot = new ChickenHackBot();
      hackBot.init();
      
      // Обработчик для overlay кнопки
      const overlayButton = document.querySelector('.btn__overlaying');
      if (overlayButton) {
        overlayButton.addEventListener('click', function() {
          const url = this.getAttribute('data-url');
          if (url) {
            window.open(url, '_blank');
          }
        });
      }
    });
  </script>

</body>

</html>
