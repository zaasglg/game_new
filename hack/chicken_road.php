
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
    // Fixed WebSocket client for hack bot
class ChickenHackWebSocket {
    constructor() {
        this.ws = null;
        this.isConnected = false;
        this.currentLevel = 'easy';
        this.lastTraps = [];
        this.isLocked = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.reconnectDelay = 3000;
        this.connect();
    }

    connect() {
        try {
            console.log('🔌 Chicken Hack connecting to WebSocket server...');
            console.log('🔌 Attempt:', this.reconnectAttempts + 1);
            
            // Закрываем существующее подключение если есть
            if (this.ws) {
                this.ws.close();
            }
            
            this.ws = new WebSocket('wss://valor-games.co/ws');

            this.ws.onopen = () => {
                this.isConnected = true;
                this.reconnectAttempts = 0; // Сбрасываем счетчик попыток
                console.log('✅ Chicken Hack connected to WebSocket server');
                
                // Инициализируем подключение
                this.initializeConnection();
            };

            this.ws.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    console.log('📨 Message received:', data);
                    this.handleMessage(data);
                } catch (error) {
                    console.error('Error parsing WebSocket message:', error);
                }
            };

            this.ws.onclose = (event) => {
                this.isConnected = false;
                console.log('📱 Disconnected from WebSocket server. Code:', event.code, 'Reason:', event.reason);
                
                // Обновляем статус в UI
                this.updateConnectionStatus('disconnected');
                
                // Попытка переподключения
                this.scheduleReconnect();
            };

            this.ws.onerror = (error) => {
                console.error('❌ WebSocket connection error:', error);
                this.updateConnectionStatus('error');
            };

        } catch (error) {
            console.error('❌ Failed to create WebSocket connection:', error);
            this.updateConnectionStatus('error');
            this.scheduleReconnect();
        }
    }

    initializeConnection() {
        // Отправляем инициализационные сообщения
        if (this.isConnected && this.ws) {
            this.ws.send(JSON.stringify({ 
                type: 'set_client_type', 
                isHackBot: true 
            }));
            
            this.ws.send(JSON.stringify({ 
                type: 'set_level', 
                level: this.currentLevel 
            }));
            
            // Запрашиваем последние данные и автоматически запускаем анализ
            setTimeout(() => {
                if (this.isConnected) {
                    this.ws.send(JSON.stringify({ 
                        type: 'get_last_traps' 
                    }));
                    
                    // Автоматический запуск анализа через небольшую задержку
                    setTimeout(() => {
                        startAutoAnalysis();
                    }, 1000);
                }
            }, 500);
        }
    }

    scheduleReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            const delay = this.reconnectDelay * Math.pow(1.5, this.reconnectAttempts - 1);
            
            console.log(`🔄 Scheduling reconnect in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.connect();
            }, delay);
        } else {
            console.error('❌ Max reconnect attempts reached');
            this.updateConnectionStatus('failed');
        }
    }

    handleMessage(data) {
        switch (data.type) {
            case 'traps':
                console.log('🎯 Single level traps received:', data);
                this.handleTrapsData(data);
                break;
                
            case 'traps_all_levels':
                console.log('🌐 All levels traps received:', data.traps);
                this.handleAllLevelsData(data.traps);
                break;
                
            case 'connection_confirmed':
                console.log('✅ Connection confirmed');
                break;
                
            case 'level_changed':
                console.log('🔄 Level changed to:', data.level);
                break;
                
            default:
                console.log('📦 Unknown message type:', data.type);
        }
    }

    handleTrapsData(data) {
        if (data.traps && data.coefficient !== undefined) {
            console.log('💰 Coefficient received:', data.coefficient);
            console.log('🔥 Trap index:', data.trapIndex);
            
            this.lastTrapData = {
                traps: data.traps,
                coefficient: data.coefficient,
                trapIndex: data.trapIndex,
                level: data.level || this.currentLevel
            };
            
            this.displayCoefficient(data.coefficient, data.trapIndex);
            this.updateConnectionStatus('active');
        }
    }

    handleAllLevelsData(allLevelsData) {
        this.allLevelsData = allLevelsData;
        
        // Обновляем данные для всех уровней
        for (const [level, levelData] of Object.entries(allLevelsData)) {
            if (levelData && levelData.coefficient !== undefined) {
                lastLevelCoefficients[level] = levelData.coefficient;
                wsReceivedForLevel[level] = true;
                console.log(`📊 Level ${level}: coefficient ${levelData.coefficient}x`);
            }
        }
        
        // Обновляем текущий уровень
        const currentLevelData = allLevelsData[this.currentLevel];
        if (currentLevelData && currentLevelData.coefficient !== undefined) {
            this.displayCoefficient(currentLevelData.coefficient, currentLevelData.trapIndex);
            this.updateConnectionStatus('active');
        }
    }

    setLevel(level) {
        console.log('🎮 Changing level to:', level);
        this.currentLevel = level;
        
        if (this.isConnected && this.ws) {
            this.ws.send(JSON.stringify({ 
                type: 'set_level', 
                level: level 
            }));
            
            // Запрашиваем данные для нового уровня
            setTimeout(() => {
                if (this.isConnected) {
                    this.ws.send(JSON.stringify({ 
                        type: 'get_last_traps',
                        level: level 
                    }));
                }
            }, 200);
        }
    }

    startHackAnalyze() {
        console.log('🔍 Starting hack analyze for level:', this.currentLevel);
        
        if (!this.isConnected || !this.ws) {
            console.error('❌ Not connected to WebSocket server');
            this.updateConnectionStatus('error');
            return false;
        }

        try {
            // Разблокируем коэффициент
            this.ws.send(JSON.stringify({ 
                type: 'unlock_coefficient' 
            }));
            
            // Запрашиваем новые ловушки
            setTimeout(() => {
                if (this.isConnected) {
                    this.ws.send(JSON.stringify({ 
                        type: 'request_traps', 
                        level: this.currentLevel 
                    }));
                }
            }, 100);
            
            this.updateConnectionStatus('analyzing');
            return true;
            
        } catch (error) {
            console.error('❌ Error during hack analyze:', error);
            this.updateConnectionStatus('error');
            return false;
        }
    }

    displayCoefficient(coefficient, trapIndex) {
        console.log(`💰 Displaying coefficient: ${coefficient}x at trap ${trapIndex}`);
        
        // Проверяем валидность коэффициента
        if (coefficient === undefined || coefficient === null || coefficient <= 0) {
            console.warn('⚠️ Invalid coefficient received:', coefficient);
            return;
        }
        
        // Обновляем коэффициент в интерфейсе
        const coeffElement = document.getElementById('coefficient-number');
        if (coeffElement) {
            coeffElement.textContent = parseFloat(coefficient).toFixed(2);
        }
        
        // Показываем иконку огня
        const fireIcon = document.getElementById('fire-icon');
        if (fireIcon && trapIndex !== undefined) {
            fireIcon.style.display = 'inline-block';
            let fireImgNum = Math.max(1, Math.min(21, trapIndex));
            fireIcon.src = `../chicken-road/res/img/fire_${fireImgNum}.png`;
            fireIcon.alt = `Fire at ${trapIndex}`;
        }
        
        // Обновляем статус
        const statusElement = document.getElementById('coefficient-status');
        if (statusElement) {
            statusElement.innerHTML = this.getRecommendation(coefficient);
        }
        
        // Сохраняем в базу данных
        updateCoefficientInDB(coefficient);
        
        // Сохраняем глобально
        lastLevelCoefficients[this.currentLevel] = coefficient;
        wsReceivedForLevel[this.currentLevel] = true;
    }

    getRecommendation(coefficient) {
        const coeff = parseFloat(coefficient);
        
        if (coeff < 2.0) {
            return '🔴 Low coefficient';
        } else if (coeff < 3.0) {
            return '🟡 Moderate risk';
        } else if (coeff < 5.0) {
            return '🟢 Good chances';
        } else {
            return '✨ Excellent chances!';
        }
    }

    updateConnectionStatus(status) {
        const statusElement = document.getElementById('coefficient-status');
        const coeffElement = document.getElementById('coefficient-number');
        
        switch (status) {
            case 'connected':
                console.log('🟢 Connection status: Connected');
                break;
                
            case 'disconnected':
                console.log('🟡 Connection status: Disconnected');
                if (statusElement) {
                    statusElement.textContent = 'Reconnecting...';
                }
                break;
                
            case 'error':
                console.log('🔴 Connection status: Error');
                if (statusElement) {
                    statusElement.textContent = 'Connection error';
                }
                break;
                
            case 'analyzing':
                console.log('🔍 Connection status: Analyzing');
                if (statusElement) {
                    statusElement.textContent = 'Analyzing...';
                }
                break;
                
            case 'active':
                console.log('✅ Connection status: Active');
                break;
                
            case 'failed':
                console.log('❌ Connection status: Failed');
                if (statusElement) {
                    statusElement.textContent = 'Connection failed';
                }
                break;
        }
    }

    // Метод для проверки состояния подключения
    getConnectionInfo() {
        return {
            isConnected: this.isConnected,
            readyState: this.ws ? this.ws.readyState : -1,
            reconnectAttempts: this.reconnectAttempts,
            currentLevel: this.currentLevel
        };
    }
}

// Глобальные переменные
let hackWebSocket;
let currentLevel = 'easy';
let lastLevelCoefficients = {};
let wsReceivedForLevel = { easy: false, medium: false, hard: false, hardcore: false };

// Автоматический анализ без кнопки
function startAutoAnalysis() {
    console.log('🎯 Starting automatic analysis');
    
    const coefficientStatus = document.getElementById('coefficient-status');

    if (!hackWebSocket) {
        console.error('❌ WebSocket client not initialized');
        if (coefficientStatus) {
            coefficientStatus.textContent = 'System not ready';
        }
        return;
    }

    // Проверяем соединение
    const connInfo = hackWebSocket.getConnectionInfo();
    console.log('🔍 Connection info:', connInfo);

    if (!hackWebSocket.isConnected) {
        console.error('❌ WebSocket not connected');
        if (coefficientStatus) {
            coefficientStatus.textContent = 'Not connected to server';
        }
        return;
    }

    // Запускаем анализ автоматически
    const success = hackWebSocket.startHackAnalyze();
    
    if (!success) {
        if (coefficientStatus) {
            coefficientStatus.textContent = 'Analysis failed';
        }
    }
}

// Функция выбора уровня
function selectLevel(level) {
    console.log('🎮 Level selected:', level);
    currentLevel = level;
    
    if (hackWebSocket) {
        hackWebSocket.setLevel(level);
    }

    // Обновляем UI кнопок
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

    // Показываем сохранённый коэффициент для уровня, если есть
    if (wsReceivedForLevel[level] && lastLevelCoefficients[level]) {
        const coeffElement = document.getElementById('coefficient-number');
        if (coeffElement) {
            coeffElement.textContent = parseFloat(lastLevelCoefficients[level]).toFixed(2);
        }
        
        const statusElement = document.getElementById('coefficient-status');
        if (statusElement && hackWebSocket) {
            statusElement.innerHTML = hackWebSocket.getRecommendation(lastLevelCoefficients[level]);
        }
    }
}

// Функция обновления коэффициента в БД
function updateCoefficientInDB(coefficient) {
    if (!coefficient || coefficient <= 0) {
        console.warn('⚠️ Invalid coefficient for DB update:', coefficient);
        return;
    }

    fetch('../db.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_chicken_coefficient&coefficient=${coefficient}&user_id=${userId}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(data => {
        console.log('💾 Coefficient updated in DB:', coefficient, 'Response:', data);
    })
    .catch(error => {
        console.error('❌ Error updating database:', error);
    });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 DOM loaded, initializing...');
    
    // Устанавливаем начальные значения
    document.getElementById('coefficient-number').textContent = '0.00';
    document.getElementById('coefficient-status').textContent = 'Connecting...';
    
    const fireIcon = document.getElementById('fire-icon');
    if (fireIcon) {
        fireIcon.style.display = 'none';
    }

    // Создаем WebSocket клиент
    hackWebSocket = new ChickenHackWebSocket();

    // Обработчик кликов по кнопкам уровня
    const levelButtons = document.getElementById('level-buttons');
    if (levelButtons) {
        levelButtons.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('level-btn')) {
                const level = e.target.getAttribute('data-level');
                selectLevel(level);
                // Автоматически запускаем анализ при смене уровня
                setTimeout(() => {
                    startAutoAnalysis();
                }, 500);
            }
        });
    }

    // Периодическая проверка соединения
    setInterval(() => {
        if (hackWebSocket && !hackWebSocket.isConnected) {
            console.log('⚠️ Connection lost, attempting to reconnect...');
            hackWebSocket.connect();
        }
    }, 30000); // Проверяем каждые 30 секунд

    // Периодическое обновление коэффициентов каждые 5 секунд
    setInterval(() => {
        if (hackWebSocket && hackWebSocket.isConnected) {
            startAutoAnalysis();
        }
    }, 5000);

    console.log('✅ Initialization complete');
});
    </script>
</body>

</html>