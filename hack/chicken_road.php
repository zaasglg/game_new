
<?php
session_start();
include 'overlaying.php';

// Get user_id from URL parameters (like in chicken-road game)
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// If user_id is not provided, redirect to home
if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Get trap coefficient from database
require_once '../db.php';

try {
    $stmt = $conn->prepare("SELECT chicken_trap_coefficient FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $trap_coefficient = $stmt->fetchColumn();
    if ($trap_coefficient === false) {
        $trap_coefficient = 0.00;
    }
} catch (PDOException $e) {
    $trap_coefficient = 0.00;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>🐔 Bot Hack Chicken Road</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./images/home-page.png" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0a0a0a;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chicken-container {
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .chicken-title {
            font-size: 1.8em;
            margin-bottom: 40px;
            color: #00ff88;
            font-weight: 300;
            letter-spacing: 1px;
        }

        /* Минималистичный дисплей коэффициента */
        .coefficient-display {
            background: #111111;
            border: 1px solid #00ff88;
            border-radius: 8px;
            padding: 30px 20px;
            margin-bottom: 30px;
        }

        .coefficient-label {
            font-size: 0.9em;
            color: #888888;
            margin-bottom: 15px;
            font-weight: 400;
        }


        .coefficient-value {
            margin: 24px 0 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .coefficient-number {
            font-size: 4.2em;
            font-weight: 800;
            background: linear-gradient(90deg, #ffe066 0%, #ffb300 40%, #ff6f00 100%);
            color: #fff;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            text-shadow: 0 0 16px #ffb300cc, 0 2px 8px #fff2, 0 0 2px #fff8;
            letter-spacing: 2px;
            filter: drop-shadow(0 0 8px #ffb30088);
            transition: color 0.3s, text-shadow 0.3s;
            animation: coeffGlow 2.2s infinite alternate;
        }
        .x-symbol {
            color: #ffb300;
            font-size: 1.2em;
            margin-left: 7px;
            font-weight: 700;
            text-shadow: 0 0 8px #ffb30099;
        }
        @keyframes coeffGlow {
            0% { text-shadow: 0 0 16px #ffb300cc, 0 2px 8px #fff2, 0 0 2px #fff8; }
            100% { text-shadow: 0 0 32px #ffb300, 0 2px 16px #fff4, 0 0 8px #fff8; }
        }



        .coefficient-status {
            font-size: 0.9em;
            color: #cccccc;
            line-height: 1.4;
        }

        /* Минималистичная кнопка */
        .analyze-btn {
            background: #00ff88;
            color: #000000;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .analyze-btn:hover {
            background: #00cc6a;
            transform: translateY(-1px);
        }

        .analyze-btn:active {
            transform: translateY(0);
        }

        /* Анимация пульса */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        /* Статусы подключения */
        #ws-connection-status {
            transition: color 0.3s ease;
        }

        /* Level buttons */
        .level-btn {
            transition: all 0.15s cubic-bezier(.4,0,.2,1);
            background: linear-gradient(180deg, #232323 60%, #111 100%);
            border: 2.5px solid #444;
            border-radius: 7px;
            box-shadow: 0 3px 0 #222, 0 6px 12px #0006;
            color: #fff;
            font-weight: 600;
            text-shadow: 0 1px 2px #000a;
            position: relative;
            outline: none;
        }
        .level-btn:hover {
            background: linear-gradient(180deg, #333 60%, #191919 100%);
            border-color: #00ff88;
            box-shadow: 0 2px 0 #00ff88, 0 6px 16px #00ff8833;
            color: #00ff88;
        }
        .level-btn:active {
            background: linear-gradient(180deg, #191919 80%, #232323 100%);
            box-shadow: 0 1px 0 #00cc6a, 0 2px 4px #0008 inset;
            top: 2px;
        }
        .level-btn.selected {
            border-color: #00ff88;
            background: linear-gradient(180deg, #00ff88 60%, #00cc6a 100%);
            color: #000;
            box-shadow: 0 2px 0 #00cc6a, 0 6px 16px #00ff8833;
        }
    </style>
</head>

<body>
    <div class="chicken-container">
        <h1 class="chicken-title">Chicken Road Bot</h1>

                <!-- Вывод user_id -->
        <div style="margin-bottom:20px; font-size:0.9em; color:#bbb;">
            User ID: <span style="color:#00ff88;"><?php echo htmlspecialchars($user_id); ?></span>
        </div>

        <!-- Level Selection -->
        <div class="level-selection" style="margin-bottom: 20px;">
            <div style="font-size: 0.9em; color: #888; margin-bottom: 10px;">Select Level:</div>
            <div class="level-buttons" id="level-buttons" style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                <button class="level-btn selected" data-level="easy" style="background: #333; color: #fff; border: 1px solid #00ff88; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Easy</button>
                <button class="level-btn" data-level="medium" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Medium</button>
                <button class="level-btn" data-level="hard" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Hard</button>
                <button class="level-btn" data-level="hardcore" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Hardcore</button>
            </div>
        </div>


        <div class="coefficient-display" style="position:relative; overflow:visible;">

            <div style="display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                <img id="fire-icon" src="../chicken-road/res/img/fire_1.png" style="width:48px; height:48px; margin-right:12px; display:none; animation: firePulse 1.2s infinite alternate;" alt="fire">
                <span id="coefficient-number" class="coefficient-number" style="font-size:3.2em; color:#ffb300; text-shadow:0 0 8px #ffb30099;">0.00</span><span class="x-symbol" style="color:#ffb300;">x</span>
            </div>
            <div class="coefficient-status" id="coefficient-status" style="font-size:1.1em; color:#fff; min-height:32px;">Ready to analyze</div>
        </div>

        <style>
        @keyframes firePulse {
            0% { filter: drop-shadow(0 0 0 #ffb300); opacity:1; }
            100% { filter: drop-shadow(0 0 16px #ffb300cc); opacity:0.7; }
        }
        .coefficient-display {
            background: linear-gradient(135deg, #1a1a1a 80%, #ffb30022 100%);
            border: 2px solid #ffb300;
            box-shadow: 0 0 16px #ffb30033;
            border-radius: 16px;
            padding: 36px 20px 28px 20px;
            margin-bottom: 30px;
        }
        .coefficient-number {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
        }
        </style>


    </div>

    <script>
    const userId = <?php echo $user_id; ?>;
    let currentLevel = 'easy';
    // Глобальный объект для хранения последних коэффициентов по уровням
    let lastLevelCoefficients = {};
        // Флаг получения коэффициента от WebSocket для каждого уровня
        let wsReceivedForLevel = { easy: false, medium: false, hard: false, hardcore: false };

        // Сохранять коэффициенты для всех уровней при traps_all_levels
        function saveAllLevelCoefficients(trapsByLevel) {
            const coefficients = {
                easy: [ 1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63, 1.75, 1.88, 2.04, 2.22, 2.45, 2.72, 3.06, 3.50, 4.08, 4.90, 6.13, 6.61, 9.81, 19.44 ],
                medium: [ 1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96, 6.20, 6.91, 8.90, 11.74, 15.99, 22.61, 33.58, 53.20, 92.17, 182.51, 451.71, 1788.80 ],
                hard: [ 1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21, 22.59, 34.79, 55.97, 94.99, 172.42, 341.40, 760.46, 2007.63, 6956.47, 41321.43 ],
                hardcore: [ 1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19, 2643.89, 9161.08, 39301.05, 233448.29 ]
            };
            for (const level in trapsByLevel) {
                const traps = trapsByLevel[level];
                if (traps && traps.length > 0) {
                    const firePosition = traps[0];
                    // firePosition это индекс сектора (1-based), коэффициент находится по индексу firePosition-1
                    const coeffIndex = firePosition - 1;
                    const coeff = coefficients[level][coeffIndex] || coefficients[level][0];
                    lastLevelCoefficients[level] = coeff;
                    wsReceivedForLevel[level] = true;
                    console.log(`Level ${level}: trap at sector ${firePosition}, coeff index ${coeffIndex}, coeff ${coeff}x`);
                }
            }
        }

        // WebSocket client for hack bot
        class ChickenHackWebSocket {
            constructor() {
                this.ws = null;
                this.isConnected = false;
                this.currentLevel = 'easy';
                this.lastTraps = [];
                this.isLocked = false; // Заблокирован ли коэффициент
                this.connect();
            }

            connect() {
                try {
                    console.log('🔌 Chicken Hack connecting to WebSocket server...');
                    this.ws = new WebSocket('wss://valor-games.co/ws');

                    this.ws.onopen = () => {
                        this.isConnected = true;
                        console.log('✅ Chicken Hack connected to WebSocket server');
                        this.ws.send(JSON.stringify({ type: 'set_level', level: this.currentLevel }));
                        this.ws.send(JSON.stringify({ type: 'set_client_type', isHackBot: true }));
                        // this.updateConnectionStatus('connected');
                    };

                    this.ws.onmessage = (event) => {
                        try {
                            const data = JSON.parse(event.data);
                            console.log('📨 Message received:', data);
                            
                            if (data.type === 'traps') {
                                console.log('🎯 Traps received:', data.traps);
                                console.log('🎯 Coefficient:', data.coefficient);
                                console.log('🎯 Trap Index:', data.trapIndex);
                                
                                // Сохраняем данные для использования в анализе
                                this.lastTrapData = {
                                    traps: data.traps,
                                    coefficient: data.coefficient,
                                    trapIndex: data.trapIndex,
                                    level: data.level
                                };
                                
                                // Отображаем коэффициент в интерфейсе (если нужно)
                                this.displayCoefficient(data.coefficient, data.trapIndex);
                                
                            } else if (data.type === 'traps_all_levels') {
                                console.log('🌐 All levels traps received:', data.traps);
                                
                                // Сохраняем данные всех уровней
                                this.allLevelsData = data.traps;
                                
                                // Извлекаем данные для текущего уровня
                                const currentLevelData = data.traps[this.currentLevel];
                                if (currentLevelData) {
                                    console.log(`📊 Current level (${this.currentLevel}) data:`, currentLevelData);
                                    console.log(`🎯 Coefficient: ${currentLevelData.coefficient}`);
                                    console.log(`🔥 Trap Index: ${currentLevelData.trapIndex}`);
                                    
                                    this.lastTrapData = {
                                        traps: currentLevelData.traps,
                                        coefficient: currentLevelData.coefficient,
                                        trapIndex: currentLevelData.trapIndex,
                                        level: this.currentLevel
                                    };
                                    
                                    this.displayCoefficient(currentLevelData.coefficient, currentLevelData.trapIndex);
                                }
                            }
                        } catch (error) {
                            console.error('Error parsing WebSocket message:', error);
                        }
                    };

                    this.ws.onclose = () => {
                        this.isConnected = false;
                        console.log('📱 Disconnected from WebSocket server');
                        // this.updateConnectionStatus('disconnected');
                        // Auto-reconnect after 3 seconds
                        setTimeout(() => this.connect(), 3000);
                    };

                    this.ws.onerror = (error) => {
                        console.error('❌ WebSocket connection error:', error);
                        // this.updateConnectionStatus('error');
                    };

                } catch (error) {
                    console.error('❌ Failed to connect to WebSocket:', error);
                    // this.updateConnectionStatus('error');
                }
            }

            setLevel(level) {
                this.currentLevel = level;
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'set_level', level: level }));
                }
            }

            requestTraps() {
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'request_traps', level: this.currentLevel }));
                } else {
                    console.error('❌ Not connected to WebSocket server');
                }
            }

            startGame() {
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'game_start' }));
                    console.log('🎮 Game started, traps locked');
                }
            }

            endGame() {
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'game_end' }));
                    console.log('🏁 Game ended, traps unlocked');
                }
            }

            startHackAnalyze() {
                if (this.isConnected && this.ws) {
                    // Сначала разблокируем коэффициент для получения нового
                    this.ws.send(JSON.stringify({ type: 'unlock_coefficient' }));
                    // Затем запрашиваем новые ловушки
                    setTimeout(() => {
                        this.ws.send(JSON.stringify({ type: 'request_traps', level: this.currentLevel }));
                    }, 100);
                    console.log('🎯 Hack analyze - unlocking and requesting new traps');
                } else {
                    console.error('❌ Not connected to WebSocket server');
                }
            }
            


            endGame() {
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'end_game' }));
                    console.log('🏁 Game ended - resuming broadcast');
                }
            }

            displayCoefficient(coefficient, trapIndex) {
                console.log(`💰 Coefficient received: ${coefficient}x at trap ${trapIndex}`);
                
                // Обновляем коэффициент в интерфейсе
                document.getElementById('coefficient-number').textContent = coefficient.toFixed(2);
                
                // Показываем анимированный огонь
                const fireIcon = document.getElementById('fire-icon');
                if (fireIcon) {
                    fireIcon.style.display = 'inline-block';
                    let fireImgNum = trapIndex;
                    if (fireImgNum < 1) fireImgNum = 1;
                    if (fireImgNum > 21) fireImgNum = 21;
                    fireIcon.src = `../chicken-road/res/img/fire_${fireImgNum}.png`;
                }
                
                // Обновляем статус
                document.getElementById('coefficient-status').innerHTML = '';
                
                // Сохраняем коэффициент в базу
                updateCoefficientInDB(coefficient);
                
                // Сохраняем в глобальные переменные
                lastLevelCoefficients[this.currentLevel] = coefficient;
                wsReceivedForLevel[this.currentLevel] = true;
            }

            updateHackDisplay(traps, level, isHackAnalyze = false) {
                if (traps && traps.length > 0 && isHackAnalyze) {
                    const firePosition = traps[0]; // Позиция огня (1-based)
                    const coefficients = this.getCoefficientsForLevel(level);
                    // firePosition это индекс сектора (1-based), коэффициент находится по индексу firePosition-1
                    const coeffIndex = firePosition - 1;
                    const coefficient = coefficients[coeffIndex] || coefficients[0];
                    const safeSteps = firePosition - 1;
                    console.log(`updateHackDisplay: firePosition=${firePosition}, coeffIndex=${coeffIndex}, coefficient=${coefficient}x`);

                    // Сохраняем последний коэффициент для уровня
                    lastLevelCoefficients[level] = coefficient;
                    wsReceivedForLevel[level] = true;

                    // Показываем анимированный огонь
                    const fireIcon = document.getElementById('fire-icon');
                    if (fireIcon) {
                        fireIcon.style.display = 'inline-block';
                        // Меняем иконку в зависимости от позиции (1-21)
                        let fireImgNum = firePosition;
                        if (fireImgNum < 1) fireImgNum = 1;
                        if (fireImgNum > 21) fireImgNum = 21;
                        fireIcon.src = `../chicken-road/res/img/fire_${fireImgNum}.png`;
                        fireIcon.alt = `Fire at ${firePosition}`;
                    }

                    document.getElementById('coefficient-number').textContent = coefficient.toFixed(2);
                    document.getElementById('coefficient-status').innerHTML = '';

                    updateCoefficientInDB(coefficient);
                    return firePosition;
                } else if (traps && traps.length > 0) {
                    // Если просто пришли новые ловушки (например, при смене уровня), тоже обновим коэффициент
                    const firePosition = traps[0];
                    const coefficients = this.getCoefficientsForLevel(level);
                    const coeffIndex = firePosition - 1;
                    const coefficient = coefficients[coeffIndex] || coefficients[0];
                    lastLevelCoefficients[level] = coefficient;
                    wsReceivedForLevel[level] = true;
                    console.log(`updateHackDisplay (level change): firePosition=${firePosition}, coeffIndex=${coeffIndex}, coefficient=${coefficient}x`);
                }
            }

            getCoefficientsForLevel(level) {
                const coefficients = {
                    easy: [ 1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63, 1.75, 1.88, 2.04, 2.22, 2.45, 2.72, 3.06, 3.50, 4.08, 4.90, 6.13, 6.61, 9.81, 19.44 ],
                    medium: [ 1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96, 6.20, 6.91, 8.90, 11.74, 15.99, 22.61, 33.58, 53.20, 92.17, 182.51, 451.71, 1788.80 ],
                    hard: [ 1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21, 22.59, 34.79, 55.97, 94.99, 172.42, 341.40, 760.46, 2007.63, 6956.47, 41321.43 ],
                    hardcore: [ 1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19, 2643.89, 9161.08, 39301.05, 233448.29 ]
                };
                return coefficients[level] || coefficients.easy;
            }


        }

        const trapHandler = (event) => {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'traps') {
                    clearTimeout(timeout);
                    window.HackWS.socket.removeEventListener('message', trapHandler);
                    
                    console.log('📨 Received traps from server:', data);
                    console.log('💰 Coefficient:', data.coefficient);
                    console.log('🔥 Trap index:', data.trapIndex);
                    
                    // Создаем расширенный объект данных
                    const wsData = {
                        traps: data.traps,
                        coefficient: data.coefficient,
                        trapIndex: data.trapIndex,
                        level: data.level
                    };
                    
                    // Создаем прогноз на основе данных сервера
                    const prediction = generateRealPrediction(currentLevel, wsData);
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

        // Create global WebSocket client instance
        let hackWebSocket;

        // Game analysis function
        function analyzeChickenGame() {
            const coefficientStatus = document.getElementById('coefficient-status');

            if (hackWebSocket && hackWebSocket.isConnected) {
                // Сбрасываем состояние блокировки
                hackWebSocket.isLocked = false;
                // Запускаем новый анализ
                hackWebSocket.startHackAnalyze();
                coefficientStatus.innerHTML = 'Analyzing...';
                console.log('🔄 Starting new analysis - coefficient unlocked');
            } else {
                coefficientStatus.textContent = 'WebSocket not available';
            }
        }

        // Функция сохранения коэффициента в базу после анализа
        function saveAnalysisResult() {
            if (hackWebSocket && hackWebSocket.fixedCoefficient) {
                updateCoefficientInDB(hackWebSocket.fixedCoefficient);
                console.log('💾 Analysis result saved to database:', hackWebSocket.fixedCoefficient);
            }
        }


        // Level selection function
        function selectLevel(level) {
            currentLevel = level;
            if (hackWebSocket) {
                hackWebSocket.setLevel(level);
            }
            // Если есть сохранённый коэффициент для этого уровня — показываем его, иначе ничего не меняем (оставляем как есть)
            if (wsReceivedForLevel[level] && lastLevelCoefficients[level] && lastLevelCoefficients[level] > 0) {
                document.getElementById('coefficient-number').textContent = parseFloat(lastLevelCoefficients[level]).toFixed(2);
                document.getElementById('coefficient-status').textContent = '';
            } else {
                // Не меняем значение, не показываем 0.00, не трогаем статус
            }
            document.querySelectorAll('.level-btn').forEach(btn => {
                btn.classList.remove('selected');
                btn.style.borderColor = '#666';
                btn.style.background = '#333';
                btn.style.color = '#fff';
            });
            const selectedBtn = document.querySelector(`[data-level="${level}"]`);
            if (selectedBtn) {
                selectedBtn.classList.add('selected');
                selectedBtn.style.borderColor = '#00ff88';
                selectedBtn.style.background = '#00ff88';
                selectedBtn.style.color = '#000';
            }
        }

        // Делегируем обработку кликов по кнопкам выбора уровня
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('level-buttons').addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('level-btn')) {
                    const level = e.target.getAttribute('data-level');
                    selectLevel(level);
                }
            });
        });

        function updateRecommendation(coefficient) {
            const coeff = parseFloat(coefficient);
            let recommendation = '';

            if (coeff < 2.0) {
                recommendation = '🔴 Low coefficient';
            } else if (coeff < 3.0) {
                recommendation = '🟡 Moderate risk';
            } else if (coeff < 5.0) {
                recommendation = '🟢 Good chances';
            } else {
                recommendation = '✨ Excellent chances!';
            }

            const coefficientStatus = document.getElementById('coefficient-status');
            if (coefficientStatus && coeff > 0) {
                coefficientStatus.textContent = `${recommendation} (${currentLevel})`;
            }
        }

        // Обновление коэффициента в базе данных
        function updateCoefficientInDB(coefficient) {
            fetch('../db.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_chicken_coefficient&coefficient=${coefficient}&user_id=${userId}`
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('Coefficient updated:', data);
                })
                .catch(error => {
                    console.error('Error updating database:', error);
                });
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function () {
            // Не показываем дефолтный коэффициент из базы, ждём ответ от WebSocket
            document.getElementById('coefficient-number').textContent = '0.00';
            document.getElementById('coefficient-status').textContent = '';
            const fireIcon = document.getElementById('fire-icon');
            if (fireIcon) fireIcon.style.display = 'none';

            // Создаем WebSocket клиент
            hackWebSocket = new ChickenHackWebSocket();

            // После подключения WebSocket сразу запрашиваем последние коэффициенты
            const wsInterval = setInterval(() => {
                if (hackWebSocket && hackWebSocket.ws && hackWebSocket.ws.readyState === 1) {
                    hackWebSocket.ws.send(JSON.stringify({ type: 'get_last_traps' }));
                    clearInterval(wsInterval);
                }
            }, 100);
        });
    </script>
</body>

</html>