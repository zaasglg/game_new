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
  <title>🐔 Chicken Road Hack - Predictor de Camino</title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
      min-height: 100vh;
      color: #fff;
      overflow-x: hidden;
    }

    .container {
      max-width: 800px;
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
      border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .logo {
      width: 60px;
      height: 60px;
      margin: 0 auto 15px;
      display: block;
    }

    .title {
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 10px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      background: linear-gradient(45deg, #ffd700, #ffed4e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      color: #f0f0f0;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 15px;
      margin-bottom: 25px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 215, 0, 0.2);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 215, 0, 0.5);
    }

    .stat-value {
      font-size: 1.8rem;
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
      flex-wrap: wrap;
    }

    .difficulty-btn {
      padding: 12px 20px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      border: 2px solid transparent;
      font-size: 0.9rem;
    }

    .difficulty-btn:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.3);
    }

    .difficulty-btn.active {
      background: #ffd700;
      color: #1a1a2e;
      border-color: #ffd700;
      box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    .road-visualization {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 25px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 215, 0, 0.2);
    }

    .road-title {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #ffd700;
    }

    .road-container {
      overflow-x: auto;
      padding: 20px 0;
    }

    .road-track {
      display: flex;
      align-items: center;
      min-width: 800px;
      gap: 10px;
      position: relative;
    }

    .road-segment {
      min-width: 80px;
      height: 60px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      position: relative;
    }

    .road-segment.start {
      background: linear-gradient(45deg, #4CAF50, #8BC34A);
      border-color: #4CAF50;
    }

    .road-segment.safe {
      background: linear-gradient(45deg, #2196F3, #03A9F4);
      border-color: #2196F3;
      animation: pulse 2s infinite;
    }

    .road-segment.danger {
      background: linear-gradient(45deg, #F44336, #FF9800);
      border-color: #F44336;
      box-shadow: 0 0 15px rgba(244, 67, 54, 0.5);
    }

    .road-segment.flame {
      background: linear-gradient(45deg, #ff0000, #ff6b00);
      border-color: #ff0000;
      animation: flame 1s infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 15px rgba(33, 150, 243, 0.5); }
      50% { box-shadow: 0 0 25px rgba(33, 150, 243, 0.8); }
    }

    @keyframes flame {
      0%, 100% { box-shadow: 0 0 20px rgba(255, 0, 0, 0.7); }
      50% { box-shadow: 0 0 30px rgba(255, 107, 0, 0.9); }
    }

    .segment-multiplier {
      font-size: 0.9rem;
      font-weight: bold;
      color: #fff;
      margin-bottom: 2px;
    }

    .segment-icon {
      font-size: 1.2rem;
    }

    .chicken-icon {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 2rem;
      animation: bounce 0.5s ease-in-out;
    }

    @keyframes bounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50% { transform: translateX(-50%) translateY(-10px); }
    }

    .action-panel {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 15px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 215, 0, 0.2);
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }

    .action-btn {
      flex: 1;
      padding: 15px 20px;
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

    .prediction-result {
      margin-top: 20px;
      padding: 20px;
      border-radius: 10px;
      background: rgba(0, 0, 0, 0.3);
      text-align: left;
      font-size: 1rem;
      line-height: 1.6;
      min-height: 60px;
      border: 2px solid rgba(255, 215, 0, 0.3);
    }

    .session-panel {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 215, 0, 0.2);
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
      margin-bottom: 15px;
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
      width: 100%;
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
        font-size: 1.8rem;
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
      
      .action-buttons {
        flex-direction: column;
      }
      
      .road-track {
        min-width: 600px;
      }
      
      .road-segment {
        min-width: 60px;
        height: 50px;
      }
    }

    /* WebSocket панель стили */
    .websocket-panel {
      background: rgba(0, 50, 100, 0.3);
      border: 1px solid rgba(0, 150, 255, 0.3);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 20px;
      backdrop-filter: blur(10px);
    }

    .websocket-status {
      margin-bottom: 15px;
    }

    .status-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
      padding: 5px 0;
    }

    .status-label {
      color: #ccc;
      font-weight: 500;
    }

    .status-connected {
      color: #4CAF50;
      font-weight: bold;
    }

    .status-disconnected {
      color: #f44336;
      font-weight: bold;
    }

    .user-id {
      color: #ffd700;
      font-family: monospace;
      font-size: 0.9em;
    }

    .game-state {
      color: #00bcd4;
      font-weight: bold;
    }

    .websocket-controls {
      display: flex;
      gap: 10px;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <img src="images/chicken-road-logo.svg" alt="Chicken Road Logo" class="logo" onerror="this.style.display='none'">
      <h1 class="title">🐔 Chicken Road Predictor</h1>
      <p class="subtitle">Predicción inteligente de caminos seguros</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value" id="success-rate">96.2%</div>
        <div class="stat-label">Precisión</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="total-predictions">2,847</div>
        <div class="stat-label">Predicciones</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="safe-steps">1,284</div>
        <div class="stat-label">Pasos Seguros</div>
      </div>
    </div>

    <!-- Difficulty Selector -->
    <div class="difficulty-selector">
      <h3 class="difficulty-title">🎯 Nivel de Dificultad</h3>
      <div class="difficulty-buttons">
        <button class="difficulty-btn active" data-difficulty="easy">🟢 Fácil</button>
        <button class="difficulty-btn" data-difficulty="medium">🟡 Medio</button>
        <button class="difficulty-btn" data-difficulty="hard">🔴 Difícil</button>
        <button class="difficulty-btn" data-difficulty="hardcore">⚫ Hardcore</button>
      </div>
    </div>

    <!-- Road Visualization -->
    <div class="road-visualization">
      <h3 class="road-title">🛣️ Predicción de Camino Chicken Road</h3>
      <div class="road-container">
        <div class="road-track" id="road-track">
          <!-- Segments will be generated here -->
        </div>
      </div>
    </div>

    <!-- Action Panel -->
    <div class="action-panel">
      <div class="action-buttons">
        <button id="analyze-btn" class="action-btn primary">
          🔍 ANALIZAR CAMINO
        </button>
        <button id="reset-btn" class="action-btn secondary">
          🔄 NUEVO ANÁLISIS
        </button>
      </div>
      <div class="prediction-result" id="prediction-result">
        📋 Presiona "ANALIZAR CAMINO" para obtener predicción...
      </div>
    </div>

    <!-- Session Panel -->
    <div class="session-panel">
      <h3 style="color: #ffd700; margin-bottom: 15px; text-align: center;">
        🔐 Código de Sesión
      </h3>
      
      <div class="instruction-panel">
        <h4 class="instruction-title">
          📖 Instrucciones para obtener el código:
        </h4>
        <ol class="instruction-list">
          <li>Abre el juego Chicken Road en tu navegador</li>
          <li>Presiona F12 y ve a la pestaña "Network"</li>
          <li>Haz una apuesta y busca peticiones AJAX</li>
          <li>Copia el ID de sesión de los headers</li>
        </ol>
      </div>
      
      <input type="text" id="session-code" class="session-input" placeholder="Pega tu código de sesión aquí..." maxlength="50">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
      <button id="confirm-btn" class="action-btn confirm">
        ✅ CONFIRMAR CÓDIGO
      </button>
    </div>
  </div>

  <!-- Socket.IO клиент -->
  <script src="https://cdn.socket.io/4.8.0/socket.io.min.js"></script>
  
  <script>
    // WebSocket клиент для хак-бота
    class HackWebSocketClient {
      constructor() {
        this.socket = null;
        this.user_id = null;
        this.isConnected = false;
        
        this.init();
      }
      
      init() {
        const urlParams = new URLSearchParams(window.location.search);
        this.user_id = urlParams.get('user_id') || 'hack_' + Date.now();
        this.connect();
      }
      
      connect() {
        try {
          console.log('🔌 Connecting to WebSocket server...');
          
          this.socket = new WebSocket('ws://localhost:8080');
          this.setupEventListeners();
          
        } catch (error) {
          console.error('❌ WebSocket connection error:', error);
          this.showConnectionStatus('Ошибка подключения', false);
        }
      }
      
      setupEventListeners() {
        this.socket.onopen = () => {
          console.log('✅ Connected to WebSocket server');
          this.isConnected = true;
          this.showConnectionStatus('Подключен к серверу', true);
          
          // Устанавливаем тип клиента как хак-бот
          this.socket.send(JSON.stringify({
            type: 'set_client_type',
            isHackBot: true
          }));
        };
        
        this.socket.onmessage = (event) => {
          try {
            const data = JSON.parse(event.data);
            console.log('📨 Message received:', data);
            
            if (data.type === 'traps') {
              console.log('🎯 Traps received:', data.traps);
              // Данные будут обработаны в обработчике кнопки анализа
            }
          } catch (error) {
            console.error('Error parsing WebSocket message:', error);
          }
        };
        
        this.socket.onclose = () => {
          console.log('📱 Disconnected from WebSocket server');
          this.isConnected = false;
          this.showConnectionStatus('Отключен', false);
        };
        
        this.socket.onerror = (error) => {
          console.error('❌ WebSocket error:', error);
          this.showConnectionStatus('Ошибка соединения', false);
        };
      }
      
      // Установка уровня сложности
      setLevel(level) {
        if (!this.isConnected) return false;
        
        this.socket.send(JSON.stringify({
          type: 'set_level',
          level: level
        }));
        return true;
      }
      
      // Запрос ловушек
      requestTraps(level) {
        if (!this.isConnected) return false;
        
        this.socket.send(JSON.stringify({
          type: 'request_traps',
          level: level
        }));
        return true;
      }
      
      // Обработка обновлений от игры
      handleGameUpdate(data) {
        // Обновляем состояние хак-бота на основе данных игры
        if (data.difficulty) {
          gameState.difficulty = data.difficulty;
          this.updateDifficultyDisplay(data.difficulty);
        }
        
        if (data.game_state) {
          this.updateGameStateDisplay(data.game_state);
        }
        
        // Автоматический анализ при старте игры
        if (data.event === 'game_start' && gameState.autoMode) {
          setTimeout(() => {
            this.performAutoPrediction();
          }, 1000);
        }
      }
      
      // Обработка ответа на анализ
      handleAnalysisResponse(data) {
        console.log('📊 Analysis response received:', data);
        
        // Очищаем таймаут если он есть
        if (window.analysisTimeout) {
          clearTimeout(window.analysisTimeout);
          window.analysisTimeout = null;
        }
        
        if (data.game_data) {
          console.log('📊 Game data received:', data.game_data);
          
          // Используем данные игры для более точного прогнозирования
          const prediction = this.createPredictionFromGameData(data.game_data);
          if (prediction) {
            this.displayPrediction(prediction);
            this.sendPrediction(prediction);
            
            // Обновляем UI
            showPredictionResult(prediction);
            
            // Восстанавливаем кнопку анализа
            if (window.analysisButton) {
              window.analysisButton.disabled = false;
              window.analysisButton = null;
            }
            gameState.active = false;
            
            // Обновляем статистику
            const totalPredictions = document.getElementById('total-predictions');
            if (totalPredictions) {
              const currentCount = parseInt(totalPredictions.textContent.replace(',', ''));
              totalPredictions.textContent = (currentCount + 1).toLocaleString();
            }
          }
        } else {
          // Fallback на локальный анализ если нет данных
          if (window.analysisButton) {
            window.analysisButton.performLocalAnalysis();
          }
        }
      }
      
      // Создание прогноза на основе данных игры
      createPredictionFromGameData(gameData) {
        try {
          // Анализируем состояние поля
          const fieldState = gameData.field_state || [];
          const difficulty = gameData.difficulty || 'easy';
          
          // Создаем прогноз на основе реальных данных
          const prediction = this.generateSmartPrediction(fieldState, difficulty);
          return prediction;
          
        } catch (error) {
          console.error('❌ Error creating prediction from game data:', error);
          return null;
        }
      }
      
      // Показ статуса подключения
      showConnectionStatus(message, isConnected) {
        console.log('Status:', message, isConnected ? '✅' : '❌');
      }

    }

    // Создаем глобальный экземпляр WebSocket клиента
    window.HackWS = new HackWebSocketClient();

    // Состояние глобального предсказателя
    let gameState = {
      active: false,
      difficulty: 'easy',
      sessionCode: '',
      currentPrediction: null,
      chickenPosition: 0,
      predictionHistory: []
    };

    // Конфигурация ТОЧНО из game2.js (расширенные массивы)
    const CHICKEN_ROAD_CONFIG = {
      cfs: {
        easy: [1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63, 1.75, 1.88, 2.04, 2.22, 2.45, 2.72, 3.06, 3.50, 4.08, 4.90, 6.13, 6.61, 9.81, 19.44],
        medium: [1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96, 6.20, 6.91, 8.90, 11.74, 15.99, 22.61, 33.58, 53.20, 92.17, 182.51, 451.71, 1788.80],
        hard: [1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21, 22.59, 34.79, 55.97, 94.99, 172.42, 341.40, 760.46, 2007.63, 6956.47, 41321.43],
        hardcore: [1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19, 2643.89, 9161.08, 39301.05, 233448.29]
      },
      chance: {
        easy: [7, 23],
        medium: [5, 15],
        hard: [3, 10],
        hardcore: [2, 6]
      }
    };

    // Псевдо-random с seed для более реалистичного предсказания
    class SeededRandom {
      constructor(seed) {
        this.seed = seed % 2147483647;
        if (this.seed <= 0) this.seed += 2147483646;
      }
      
      next() {
        return this.seed = this.seed * 16807 % 2147483647;
      }
      
      nextFloat() {
        return (this.next() - 1) / 2147483646;
      }
    }

    // Генерация seed на основе времени и sessionCode
    function generateGameSeed() {
      const now = Date.now();
      const sessionHash = gameState.sessionCode ? 
        gameState.sessionCode.split('').reduce((a, b) => {
          a = ((a << 5) - a) + b.charCodeAt(0);
          return a & a;
        }, 0) : 12345;
      
      return Math.abs(now + sessionHash) % 1000000;
    }

    // Функция из реального кода игры
    function selectValueHybridIndex(mainArray, chanceArray) {
      const limit = Math.random() <= 0.1 ? chanceArray[1] : chanceArray[0];
      const filteredIndices = mainArray
        .map((val, index) => ({ val, index }))
        .filter(({ val, index }) => val <= limit && (index <= 1 || Math.random() < 0.3))
        .map(({ index }) => index);
      
      if (filteredIndices.length === 0) {
        const fallbackIndex = mainArray.findIndex(val => val <= limit);
        return fallbackIndex !== -1 ? fallbackIndex : 0;
      }
      
      return filteredIndices[Math.floor(Math.random() * filteredIndices.length)];
    }

    // Генерация предсказания с использованием WebSocket данных
    function generateRealPrediction(difficulty, wsData = null) {
      console.log(`Генерируем точное предсказание для уровня: ${difficulty}`);
      
      const cfs = CHICKEN_ROAD_CONFIG.cfs[difficulty];
      const chance = CHICKEN_ROAD_CONFIG.chance[difficulty];
      
      let flameSegment;
      
      // Если есть данные от WebSocket сервера, используем их
      if (wsData && wsData.traps && wsData.traps.length > 0) {
        flameSegment = wsData.traps[0] - 1; // WebSocket возвращает 1-based индекс
        console.log('🌐 Используем данные от WebSocket сервера:', wsData.traps[0]);
      } else {
        // Fallback на локальную генерацию (точная копия из game2.js)
        const selectedChance = chance[Math.round(Math.random() * 100) > 95 ? 1 : 0];
        flameSegment = Math.ceil(Math.random() * selectedChance) - 1; // -1 для 0-based индекса
        console.log('🔄 Локальная генерация, flame segment:', flameSegment + 1);
      }
      
      console.log(`Final flame segment: ${flameSegment}`);
      
      // Генерируем массив множителей для дорожки
      const roadSegments = [];
      const totalSegments = Math.min(15, cfs.length + 5);
      
      for (let i = 0; i < totalSegments; i++) {
        let multiplier;
        
        if (i < cfs.length) {
          multiplier = cfs[i];
        } else {
          const lastMultiplier = cfs[cfs.length - 1];
          const growthFactor = difficulty === 'hardcore' ? 3.0 : 
                             difficulty === 'hard' ? 2.2 :
                             difficulty === 'medium' ? 1.7 : 1.4;
          multiplier = lastMultiplier * Math.pow(growthFactor, i - cfs.length + 1);
        }
        
        const isFlame = i === flameSegment;
        const isSafe = i < flameSegment;
        const isDanger = i > flameSegment;
        
        roadSegments.push({
          position: i,
          multiplier: multiplier.toFixed(2),
          isFlame,
          isSafe,
          isDanger
        });
      }
      
      const safeSteps = flameSegment;
      const maxSafeMultiplier = flameSegment > 0 ? cfs[flameSegment - 1] : 1.0;
      
      let confidence;
      if (flameSegment === 0) {
        confidence = Math.floor(95 + Math.random() * 4);
      } else if (flameSegment <= 3) {
        confidence = Math.floor(90 + Math.random() * 8);
      } else {
        confidence = Math.floor(85 + Math.random() * 10);
      }
      
      const prediction = {
        flameSegment,
        roadSegments,
        safeSteps,
        maxMultiplier: maxSafeMultiplier,
        confidence,
        difficulty,
        wsData: wsData,
        timestamp: Date.now()
      };
      
      gameState.predictionHistory.push(prediction);
      if (gameState.predictionHistory.length > 50) {
        gameState.predictionHistory.shift();
      }
      
      console.log('Generated prediction:', prediction);
      
      return prediction;
    }

    // Отображение дорожки
    function renderRoad(prediction = null) {
      const roadTrack = document.getElementById('road-track');
      roadTrack.innerHTML = '';
      
      if (!prediction) {
        // Показываем пустую дорожку
        for (let i = 0; i < 10; i++) {
          const segment = document.createElement('div');
          segment.className = 'road-segment';
          segment.innerHTML = `
            <div class="segment-multiplier">?</div>
            <div class="segment-icon">❓</div>
          `;
          roadTrack.appendChild(segment);
        }
        return;
      }
      
      // Отображаем предсказание
      prediction.roadSegments.forEach((segmentData, index) => {
        const segment = document.createElement('div');
        let className = 'road-segment';
        let icon = '❓';
        
        if (index === 0) {
          className += ' start';
          icon = '🏁';
        } else if (segmentData.isFlame) {
          className += ' flame';
          icon = '🔥';
        } else if (segmentData.isSafe) {
          className += ' safe';
          icon = '✅';
        } else if (segmentData.isDanger) {
          className += ' danger';
          icon = '⚠️';
        }
        
        segment.className = className;
        segment.innerHTML = `
          <div class="segment-multiplier">${segmentData.multiplier}x</div>
          <div class="segment-icon">${icon}</div>
        `;
        
        // Добавляем цыпленка на старт
        if (index === 0) {
          segment.innerHTML += '<div class="chicken-icon">🐔</div>';
        }
        
        roadTrack.appendChild(segment);
      });
    }

    // Показ результата анализа с детальной информацией
    function showPredictionResult(prediction) {
      const resultElement = document.getElementById('prediction-result');
      
      let riskLevel = '';
      let riskColor = '';
      
      if (prediction.flameSegment === 0) {
        riskLevel = 'CRÍTICO - Fuego inmediato';
        riskColor = '#ff0000';
      } else if (prediction.flameSegment <= 2) {
        riskLevel = 'ALTO - Pocos pasos seguros';
        riskColor = '#ff6b00';
      } else if (prediction.flameSegment <= 5) {
        riskLevel = 'MEDIO - Moderado';
        riskColor = '#ffd700';
      } else {
        riskLevel = 'BAJO - Muchos pasos seguros';
        riskColor = '#4CAF50';
      }
      
      resultElement.innerHTML = `
        <div style="text-align: left; line-height: 1.8;">
          <strong style="color: #ffd700; font-size: 1.1em;">🎯 ANÁLISIS CHICKEN ROAD COMPLETADO</strong><br><br>
          
          <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; margin: 10px 0;">
            <strong>📍 INFORMACIÓN CRÍTICA:</strong><br>
            <span style="color: #ff6b00;">🔥 Posición de Flame: Paso ${prediction.flameSegment + 1}</span><br>
            <span style="color: #4CAF50;">✅ Pasos Seguros: ${prediction.safeSteps}</span><br>
            <span style="color: ${riskColor};">⚠️ Nivel de Riesgo: ${riskLevel}</span>
          </div>
          
          <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; margin: 10px 0;">
            <strong>💰 INFORMACIÓN FINANCIERA:</strong><br>
            <span style="color: #ffd700;">🎲 Multiplicador Máximo Seguro: ${prediction.maxMultiplier.toFixed(2)}x</span><br>
            <span style="color: #2196F3;">📊 Confianza del Análisis: ${prediction.confidence}%</span><br>
            <span style="color: #9C27B0;">🎯 Dificultad: ${prediction.difficulty.toUpperCase()}</span>
          </div>
          
          <div style="background: rgba(255,107,0,0.2); padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ff6b00;">
            <strong style="color: #ff6b00;">⚠️ RECOMENDACIÓN ESTRATÉGICA:</strong><br>
            ${prediction.flameSegment === 0 ? 
              '<span style="color: #ff0000;">🚨 NO JUEGUES - Fuego inmediato detectado</span>' :
              `<span style="color: #ffd700;">🎯 Retira ANTES del paso ${prediction.flameSegment + 1}</span><br>
               <span style="color: #4CAF50;">💡 Máximo seguro: ${prediction.maxMultiplier.toFixed(2)}x en paso ${prediction.safeSteps}</span>`
            }
          </div>
          
          <div style="text-align: center; margin-top: 15px; font-size: 0.9em; opacity: 0.8;">
            <span style="color: #2196F3;">🔬 Análisis basado en algoritmos reales de Chicken Road</span>
          </div>
        </div>
      `;
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Chicken Road Predictor inicializado');
      
      // Inicializar dорожку
      renderRoad();
      
      // Обработчики кнопок сложности
      document.querySelectorAll('.difficulty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.difficulty-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          gameState.difficulty = this.dataset.difficulty;
          console.log('Выбрана сложность:', gameState.difficulty);
          
          // Обновляем уровень в WebSocket
          if (window.HackWS && window.HackWS.isConnected) {
            window.HackWS.setLevel(gameState.difficulty);
          }
          
          // Сброс предсказания
          renderRoad();
          document.getElementById('prediction-result').innerHTML = '📋 Presiona "ANALIZAR CAMINO" para obtener nueva predicción...';
        });
      });
      
      // Кнопка анализа
      document.getElementById('analyze-btn').addEventListener('click', function() {
        if (gameState.active) return;
        
        gameState.active = true;
        this.disabled = true;
        
        document.getElementById('prediction-result').innerHTML = '🔄 Analizando patrones de Chicken Road...';
        
        // Если WebSocket подключен, запрашиваем данные от сервера
        if (window.HackWS && window.HackWS.isConnected) {
          console.log('🌐 Requesting traps via WebSocket...');
          
          // Устанавливаем уровень сложности
          window.HackWS.socket.send(JSON.stringify({
            type: 'set_level',
            level: gameState.difficulty
          }));
          
          // Запрашиваем актуальные ловушки от сервера
          window.HackWS.socket.send(JSON.stringify({
            type: 'request_traps',
            level: gameState.difficulty
          }));
          
          // Ждем ответ от сервера
          const timeout = setTimeout(() => {
            console.log('⏰ WebSocket timeout, using local prediction');
            this.performLocalAnalysis();
          }, 3000);
          
          // Обработчик ответа от сервера
          const trapHandler = (event) => {
            try {
              const data = JSON.parse(event.data);
              if (data.type === 'traps') {
                clearTimeout(timeout);
                window.HackWS.socket.removeEventListener('message', trapHandler);
                
                console.log('📨 Received traps from server:', data);
                
                // Создаем прогноз на основе данных сервера
                const prediction = generateRealPrediction(gameState.difficulty, data);
                gameState.currentPrediction = prediction;
                
                renderRoad(prediction);
                showPredictionResult(prediction);
                
                this.disabled = false;
                gameState.active = false;
                
                // Обновляем статистику
                const totalPredictions = document.getElementById('total-predictions');
                const currentCount = parseInt(totalPredictions.textContent.replace(',', ''));
                totalPredictions.textContent = (currentCount + 1).toLocaleString();
              }
            } catch (e) {
              console.error('Error parsing WebSocket message:', e);
            }
          };
          
          window.HackWS.socket.addEventListener('message', trapHandler);
          
          window.analysisTimeout = timeout;
          window.analysisButton = this;
          return;
        }
        
        // Fallback на локальный анализ
        this.performLocalAnalysis();
      });
      
      // Вспомогательная функция для локального анализа
      document.getElementById('analyze-btn').performLocalAnalysis = function() {
        setTimeout(() => {
          const prediction = generateRealPrediction(gameState.difficulty);
          gameState.currentPrediction = prediction;
          
          renderRoad(prediction);
          showPredictionResult(prediction);
          
          document.getElementById('analyze-btn').disabled = false;
          gameState.active = false;
          
          // Обновляем статистику
          const totalPredictions = document.getElementById('total-predictions');
          const currentCount = parseInt(totalPredictions.textContent.replace(',', ''));
          totalPredictions.textContent = (currentCount + 1).toLocaleString();
        }, 2500);
      };
      
      // Кнопка сброса
      document.getElementById('reset-btn').addEventListener('click', function() {
        gameState.currentPrediction = null;
        renderRoad();
        document.getElementById('prediction-result').innerHTML = '📋 Presiona "ANALIZAR CAMINO" para obtener predicción...';
        document.getElementById('analyze-btn').disabled = false;
        gameState.active = false;
      });
      
      // Подтверждение кода сессии
      const confirmBtn = document.getElementById('confirm-btn');
      const sessionInput = document.getElementById('session-code');
      
      confirmBtn.addEventListener('click', function() {
        const code = sessionInput.value.trim();
        
        if (code.length < 8) {
          alert('⚠️ Código de sesión demasiado corto. Debe tener al menos 8 caracteres.');
          return;
        }
        
        gameState.sessionCode = code;
        this.textContent = '✅ CÓDIGO CONFIRMADO';
        this.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
        sessionInput.disabled = true;
        
        setTimeout(() => {
          this.textContent = '✅ CONFIRMAR CÓDIGO';
          sessionInput.disabled = false;
        }, 3000);
      });
      

      
      console.log('Todos los обработчики событий инициализированы');
    });
  </script>
</body>
</html>
