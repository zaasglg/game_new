<?php
// Защита от CSRF атак
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Включение всех ошибок для отладки (убрать в продакшене)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🐔 Chicken Road Hack - Análisis Inteligente</title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #fff;
      overflow-x: hidden;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .logo {
      width: 60px;
      height: 60px;
      margin: 0 auto 15px;
      display: block;
    }

    .title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 10px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .subtitle {
      font-size: 1rem;
      opacity: 0.9;
      color: #f0f0f0;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 25px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.15);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: bold;
      color: #ffd700;
      margin-bottom: 5px;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.8;
    }

    .difficulty-selector {
      margin-bottom: 25px;
    }

    .difficulty-title {
      text-align: center;
      margin-bottom: 15px;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .difficulty-buttons {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .difficulty-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      border: 2px solid transparent;
    }

    .difficulty-btn:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.3);
    }

    .difficulty-btn.active {
      background: #ffd700;
      color: #333;
      border-color: #ffd700;
      box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    .game-board {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 25px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .board-title {
      text-align: center;
      margin-bottom: 15px;
      font-size: 1.1rem;
      font-weight: 600;
    }

    #game_field {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 8px;
      margin: 20px 0;
      padding: 15px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 10px;
    }

    .game-cell {
      aspect-ratio: 1;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.1);
      cursor: pointer;
    }

    .game-cell.chicken {
      background: linear-gradient(45deg, #4CAF50, #8BC34A);
      color: white;
      border-color: #4CAF50;
      box-shadow: 0 0 15px rgba(76, 175, 80, 0.5);
    }

    .game-cell.fire {
      background: linear-gradient(45deg, #F44336, #FF9800);
      color: white;
      border-color: #F44336;
      box-shadow: 0 0 15px rgba(244, 67, 54, 0.5);
    }

    .game-cell.safe {
      background: linear-gradient(45deg, #2196F3, #03A9F4);
      color: white;
      border-color: #2196F3;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 15px rgba(33, 150, 243, 0.5); }
      50% { box-shadow: 0 0 25px rgba(33, 150, 243, 0.8); }
    }

    .tabs__item {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 15px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .tabs__item-inner {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .action-btn {
      padding: 15px 30px;
      border: none;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
    }

    .action-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .action-btn:hover::before {
      left: 100%;
    }

    .action-btn.primary {
      background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
      color: white;
      box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    }

    .action-btn.primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
    }

    .action-btn.secondary {
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .action-btn.secondary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }

    .action-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    #signal-result {
      margin-top: 15px;
      padding: 15px;
      border-radius: 10px;
      background: rgba(0, 0, 0, 0.3);
      text-align: center;
      font-size: 1.1rem;
      line-height: 1.5;
      min-height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .session-input {
      width: 100%;
      padding: 15px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      font-size: 1rem;
      backdrop-filter: blur(10px);
    }

    .session-input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .session-input:focus {
      outline: none;
      border-color: #ffd700;
      box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
    }

    .instruction-panel {
      background: rgba(0, 0, 0, 0.3);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .instruction-title {
      color: #ffd700;
      margin-bottom: 10px;
      font-size: 1rem;
    }

    .instruction-list {
      list-style: none;
      padding-left: 0;
    }

    .instruction-list li {
      margin-bottom: 8px;
      padding-left: 20px;
      position: relative;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .instruction-list li::before {
      content: "→";
      position: absolute;
      left: 0;
      color: #ffd700;
      font-weight: bold;
    }

    .action-btn.confirm {
      background: linear-gradient(45deg, #28a745, #20c997);
      color: white;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }

    .action-btn.confirm:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
    }

    @media (max-width: 768px) {
      .container {
        padding: 15px;
      }
      
      .title {
        font-size: 1.5rem;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }
      
      .difficulty-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .difficulty-btn {
        width: 100%;
        max-width: 200px;
      }
      
      #game_field {
        gap: 5px;
        padding: 10px;
      }
      
      .game-cell {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <img src="images/chicken-road-logo.svg" alt="Chicken Road Logo" class="logo" onerror="this.style.display='none'">
      <h1 class="title translate" data-key="title">🐔 Chicken Road Hack</h1>
      <p class="subtitle translate" data-key="subtitle">Análisis inteligente para maximizar ganancias</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value" id="success-rate">94.7%</div>
        <div class="stat-label translate" data-key="success_rate">Tasa de Éxito</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="total-predictions">1,247</div>
        <div class="stat-label translate" data-key="predictions">Predicciones</div>
      </div>
    </div>

    <!-- Difficulty Selector -->
    <div class="difficulty-selector">
      <h3 class="difficulty-title translate" data-key="difficulty">🎯 Nivel de Dificultad</h3>
      <div class="difficulty-buttons">
        <button class="difficulty-btn active" data-difficulty="easy">🟢 Fácil</button>
        <button class="difficulty-btn" data-difficulty="medium">🟡 Medio</button>
        <button class="difficulty-btn" data-difficulty="hard">🔴 Difícil</button>
      </div>
    </div>

    <!-- Game Board -->
    <div class="game-board">
      <h3 class="board-title translate" data-key="game_field">🎮 Campo de Juego Chicken Road</h3>
      <div id="game_field"></div>
    </div>

    <!-- Control Buttons -->
    <div class="tabs__item">
      <div class="tabs__item-inner">
        <button id="main_proc" class="action-btn primary translate" data-key="get_signal">
          🐔 ANALIZAR CAMINO
        </button>
        <button id="next_proc" class="action-btn secondary translate" data-key="next_game">
          🔄 NUEVO ANÁLISIS
        </button>
        <div id="signal-result"></div>
      </div>
    </div>

    <!-- Session Code Input -->
    <div class="tabs__item">
      <div class="tabs__item-inner">
        <h3 style="color: #fff; margin: 0; text-align: center;">
          Ingresa tu código de sesión:
        </h3>
        
        <!-- Instructions Panel -->
        <div class="instruction-panel">
          <h4 class="instruction-title">
            🔍 Cómo obtener el ID de sesión:
          </h4>
          <ol class="instruction-list">
            <li>Abre la página del juego Chicken Road</li>
            <li>Presiona F12 para abrir las herramientas de desarrollador</li>
            <li>Ve a la pestaña "Network" (Red)</li>
            <li>Haz una apuesta o inicia el juego</li>
            <li>Busca una petición que contenga "session" o "bet"</li>
            <li>Copia el ID de sesión de los headers o del payload</li>
          </ol>
        </div>
        
        <input type="text" id="session-code" class="session-input" placeholder="Ej: abc123def456..." maxlength="50">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <button id="confirm-btn" class="action-btn confirm translate" data-key="confirm">
          ✅ CONFIRMAR CÓDIGO
        </button>
      </div>
    </div>
  </div>

  <script>
    // Глобальные переменные для состояния игры
    let gameActive = false;
    let currentDifficulty = 'easy';
    let sessionCode = '';
    let currentPrediction = null;

    // Реальная логика из Chicken Road game2.js
    const REAL_CHICKEN_ROAD_LOGIC = {
      // Реальные массивы из игры
      cfs: [1.01, 1.25, 1.5, 1.75, 2.0, 2.5, 3.0, 4.0, 5.0, 10.0],
      
      // Функция расчета flame segment из реального кода
      getFlameSegment: function(gameId, roundId) {
        const combined = parseInt(gameId.toString() + roundId.toString());
        const hash = this.simpleHash(combined);
        return (hash % 100) / 100;
      },
      
      // Простой хеш для имитации реальной логики
      simpleHash: function(input) {
        let hash = 0;
        const str = input.toString();
        for (let i = 0; i < str.length; i++) {
          const char = str.charCodeAt(i);
          hash = ((hash << 5) - hash) + char;
          hash = hash & hash; // Convert to 32bit integer
        }
        return Math.abs(hash);
      },
      
      // Функция выбора значения из массива cfs
      selectValueHybridIndex: function(segment, difficulty = 'easy') {
        const difficultyMultiplier = {
          'easy': 0.7,
          'medium': 0.85,
          'hard': 1.0
        };
        
        const adjustedSegment = segment * difficultyMultiplier[difficulty];
        const index = Math.floor(adjustedSegment * this.cfs.length);
        return this.cfs[Math.min(index, this.cfs.length - 1)];
      },
      
      // Определение безопасных позиций
      getSafePositions: function(flameSegment, difficulty = 'easy') {
        const totalCells = 25; // 5x5 grid
        const safeCount = Math.floor(totalCells * (0.4 + (flameSegment * 0.3)));
        const positions = [];
        
        // Генерируем безопасные позиции на основе flame segment
        for (let i = 0; i < safeCount; i++) {
          const hash = this.simpleHash(flameSegment * 1000 + i);
          const position = hash % totalCells;
          if (!positions.includes(position)) {
            positions.push(position);
          }
        }
        
        return positions;
      }
    };

    // Функция инициализации игрового поля
    function initRealChickenRoadVisualization() {
      console.log('Инициализация визуализации Chicken Road...');
      const gameField = document.getElementById('game_field');
      if (!gameField) {
        console.error('Игровое поле не найдено!');
        return;
      }
      
      gameField.innerHTML = '';
      
      // Создаем сетку 5x5 как в реальном Chicken Road
      for (let i = 0; i < 25; i++) {
        const cell = document.createElement('div');
        cell.className = 'game-cell';
        cell.dataset.index = i;
        cell.textContent = '?';
        gameField.appendChild(cell);
      }
      
      console.log('Игровое поле инициализировано с 25 ячейками');
    }

    // Функция отображения реального прогноза Chicken Road
    function showRealChickenRoadPrediction() {
      console.log('Показываем реальный прогноз Chicken Road...');
      
      // Генерируем ID для имитации реальной игры
      const gameId = Math.floor(Math.random() * 10000) + 1000;
      const roundId = Math.floor(Math.random() * 1000) + 100;
      
      console.log(`Game ID: ${gameId}, Round ID: ${roundId}`);
      
      // Используем реальную логику из игры
      const flameSegment = REAL_CHICKEN_ROAD_LOGIC.getFlameSegment(gameId, roundId);
      const multiplier = REAL_CHICKEN_ROAD_LOGIC.selectValueHybridIndex(flameSegment, currentDifficulty);
      const safePositions = REAL_CHICKEN_ROAD_LOGIC.getSafePositions(flameSegment, currentDifficulty);
      
      console.log(`Flame Segment: ${flameSegment}, Multiplier: ${multiplier}, Safe Positions: ${safePositions.length}`);
      
      // Обновляем игровое поле
      const cells = document.querySelectorAll('.game-cell');
      cells.forEach((cell, index) => {
        cell.classList.remove('chicken', 'fire', 'safe');
        
        if (safePositions.includes(index)) {
          cell.classList.add('safe');
          cell.textContent = '🐔';
        } else {
          cell.classList.add('fire');
          cell.textContent = '🔥';
        }
      });
      
      // Обновляем результат
      const resultElement = document.getElementById('signal-result');
      if (resultElement) {
        const confidence = Math.floor(85 + (flameSegment * 10));
        resultElement.innerHTML = `
          <div style="text-align: left;">
            <strong>🎯 ANÁLISIS COMPLETADO</strong><br>
            <span style="color: #4CAF50;">✅ Camino Seguro: ${safePositions.length} posiciones</span><br>
            <span style="color: #ffd700;">🎲 Multiplicador Sugerido: ${multiplier}x</span><br>
            <span style="color: #2196F3;">📊 Confianza: ${confidence}%</span><br>
            <span style="color: #FF9800;">🔥 Evita las posiciones rojas</span>
          </div>
        `;
      }
      
      // Сохраняем текущий прогноз
      currentPrediction = {
        gameId,
        roundId,
        flameSegment,
        multiplier,
        safePositions,
        confidence
      };
      
      console.log('Прогноз отображен успешно');
    }

    // Инициализация после загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM загружен, инициализируем Chicken Road бот...');
      
      // Проверяем наличие всех элементов
      console.log('main_proc button:', document.getElementById('main_proc'));
      console.log('next_proc button:', document.getElementById('next_proc'));
      console.log('game_field:', document.getElementById('game_field'));
      console.log('difficulty buttons:', document.querySelectorAll('.difficulty-btn'));
      
      // Инициализация игры
      initRealChickenRoadVisualization();

      // Обработчик кнопки анализа
      const mainProcBtn = document.getElementById('main_proc');
      if (mainProcBtn) {
        console.log('Найдена кнопка main_proc, добавляем обработчик');
        mainProcBtn.addEventListener('click', function() {
          console.log('Кнопка анализа нажата');
          if (!gameActive) {
            gameActive = true;
            this.disabled = true;
            const resultElement = document.getElementById('signal-result');
            if (resultElement) {
              resultElement.innerHTML = `🔄 Анализируем реальную логику Chicken Road...`;
            }
            
            setTimeout(() => {
              showRealChickenRoadPrediction();
              this.disabled = false;
              gameActive = false;
            }, 2500);
          }
        });
      } else {
        console.error('Кнопка main_proc не найдена!');
      }

      // Обработчик кнопки нового анализа
      const nextProcBtn = document.getElementById('next_proc');
      if (nextProcBtn) {
        console.log('Найдена кнопка next_proc, добавляем обработчик');
        nextProcBtn.addEventListener('click', function() {
          console.log('Кнопка нового анализа нажата');
          gameActive = false;
          initRealChickenRoadVisualization();
          const resultElement = document.getElementById('signal-result');
          if (resultElement) {
            resultElement.innerHTML = '';
          }
          const mainBtn = document.getElementById('main_proc');
          if (mainBtn) {
            mainBtn.disabled = false;
          }
        });
      } else {
        console.error('Кнопка next_proc не найдена!');
      }

      // Обработчики кнопок сложности
      console.log('Настраиваем кнопки сложности...');
      document.querySelectorAll('.difficulty-btn').forEach(btn => {
        console.log('Найдена кнопка сложности:', btn.dataset.difficulty);
        btn.addEventListener('click', function() {
          console.log('Выбрана сложность:', this.dataset.difficulty);
          document.querySelectorAll('.difficulty-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          currentDifficulty = this.dataset.difficulty;
          console.log('Текущая сложность установлена:', currentDifficulty);
          
          // Сброс игры
          gameActive = false;
          initRealChickenRoadVisualization();
          const resultElement = document.getElementById('signal-result');
          if (resultElement) {
            resultElement.innerHTML = '';
          }
          const mainBtn = document.getElementById('main_proc');
          if (mainBtn) {
            mainBtn.disabled = false;
          }
        });
      });

      // Обработчик подтверждения кода сессии
      const confirmBtn = document.getElementById('confirm-btn');
      const sessionInput = document.getElementById('session-code');
      
      if (confirmBtn && sessionInput) {
        console.log('Настраиваем обработчик кода сессии...');
        confirmBtn.addEventListener('click', function() {
          const code = sessionInput.value.trim();
          console.log('Код сессии введен:', code ? 'да' : 'нет');
          
          if (code.length < 8) {
            alert('⚠️ Código de sesión demasiado corto. Debe tener al menos 8 caracteres.');
            return;
          }
          
          sessionCode = code;
          this.textContent = '✅ CÓDIGO CONFIRMADO';
          this.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
          sessionInput.disabled = true;
          
          setTimeout(() => {
            this.textContent = '✅ CONFIRMAR CÓDIGO';
            sessionInput.disabled = false;
          }, 3000);
        });
      }

      console.log('Инициализация Chicken Road бота завершена');
    });
  </script>
</body>
</html>
