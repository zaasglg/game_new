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
            margin: 20px 0;
        }

        .coefficient-number {
            font-size: 3.5em;
            font-weight: 300;
            color: #00ff88;
        }

        .x-symbol {
            color: #00ff88;
            font-size: 0.7em;
            margin-left: 5px;
        }

        #ws-connection-status {
            font-size: 0.8em;
            color: #666666;
            margin: 15px 0;
            padding: 8px;
            background: #0f0f0f;
            border-radius: 4px;
            border: 1px solid #333333;
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
            transition: all 0.2s ease;
        }

        .level-btn:hover {
            border-color: #00ff88 !important;
            background: #444 !important;
        }

        .level-btn.selected {
            border-color: #00ff88 !important;
            background: #00ff88 !important;
            color: #000 !important;
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
            <div class="level-buttons" style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                <button class="level-btn selected" data-level="easy" onclick="selectLevel('easy')" style="background: #333; color: #fff; border: 1px solid #00ff88; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Easy</button>
                <button class="level-btn" data-level="medium" onclick="selectLevel('medium')" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Medium</button>
                <button class="level-btn" data-level="hard" onclick="selectLevel('hard')" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Hard</button>
                <button class="level-btn" data-level="hardcore" onclick="selectLevel('hardcore')" style="background: #333; color: #fff; border: 1px solid #666; border-radius: 4px; padding: 8px 12px; font-size: 0.8em; cursor: pointer;">Hardcore</button>
            </div>
        </div>

        <div class="coefficient-display">
            <div class="coefficient-label">Trap Coefficient</div>
            <div class="coefficient-value">
                <span id="coefficient-number" class="coefficient-number">0.00</span><span class="x-symbol">x</span>
            </div>
            <div id="ws-connection-status">Connecting...</div>
            <div class="coefficient-status" id="coefficient-status">Ready to analyze</div>
        </div>

        <button class="analyze-btn" id="analyze-btn" onclick="analyzeChickenGame()">
            Analyze Game
        </button>
    </div>

    <script>
        const userId = <?php echo $user_id; ?>;
        let currentLevel = 'easy';

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
                    this.ws = new WebSocket('wss://valor-games.com/ws/');

                    this.ws.onopen = () => {
                        this.isConnected = true;
                        console.log('✅ Chicken Hack connected to WebSocket server');
                        this.ws.send(JSON.stringify({ type: 'set_level', level: this.currentLevel }));
                        this.ws.send(JSON.stringify({ type: 'set_client_type', isHackBot: true }));
                        this.updateConnectionStatus('connected');
                    };

                    this.ws.onmessage = (event) => {
                        const data = JSON.parse(event.data);
                        console.log('📥 Chicken Hack received:', data);

                        if (data.type === 'traps') {
                            this.lastTraps = data.traps;
                            // Обновляем только если анализируем и не заблокированы
                            const coefficientStatus = document.getElementById('coefficient-status');
                            if (coefficientStatus && coefficientStatus.textContent === 'Analyzing...' && !this.isLocked) {
                                this.updateHackDisplay(data.traps, data.level, true);
                                this.isLocked = true; // Блокируем после получения коэффициента
                            }
                        }
                    };

                    this.ws.onclose = () => {
                        this.isConnected = false;
                        console.log('📱 Disconnected from WebSocket server');
                        this.updateConnectionStatus('disconnected');
                        // Auto-reconnect after 3 seconds
                        setTimeout(() => this.connect(), 3000);
                    };

                    this.ws.onerror = (error) => {
                        console.error('❌ WebSocket connection error:', error);
                        this.updateConnectionStatus('error');
                    };

                } catch (error) {
                    console.error('❌ Failed to connect to WebSocket:', error);
                    this.updateConnectionStatus('error');
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
                    // Отправляем request_traps для получения фиксированных трапов
                    this.ws.send(JSON.stringify({ type: 'request_traps', level: this.currentLevel }));
                    console.log('🎯 Hack analyze - requesting fixed traps');
                } else {
                    console.error('❌ Not connected to WebSocket server');
                }
            }

            endHackAnalyze() {
                if (this.isConnected && this.ws) {
                    this.ws.send(JSON.stringify({ type: 'game_end' }));
                    console.log('🏁 Hack analyze ended');
                }
            }

            updateHackDisplay(traps, level, isHackAnalyze = false) {
                if (traps && traps.length > 0 && isHackAnalyze) {
                    const trapIndex = traps[0] - 1;
                    const coefficients = this.getCoefficientsForLevel(level);
                    const coefficient = (trapIndex >= 0 && trapIndex < coefficients.length) ? 
                        coefficients[trapIndex] : coefficients[0];

                    document.getElementById('coefficient-number').textContent = coefficient.toFixed(2);
                    document.getElementById('coefficient-status').textContent = 'Coefficient Locked - Game Active';
                    
                    updateCoefficientInDB(coefficient);
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

            updateConnectionStatus(status) {
                const wsStatus = document.getElementById('ws-connection-status');
                if (wsStatus) {
                    switch (status) {
                        case 'connected':
                            wsStatus.textContent = 'Connected';
                            wsStatus.style.color = '#00ff88';
                            break;
                        case 'disconnected':
                            wsStatus.textContent = 'Disconnected';
                            wsStatus.style.color = '#ff6b6b';
                            break;
                        case 'error':
                            wsStatus.textContent = 'Connection Error';
                            wsStatus.style.color = '#ff6b6b';
                            break;
                    }
                }
            }
        }

        // Create global WebSocket client instance
        let hackWebSocket;

        // Game analysis function
        function analyzeChickenGame() {
            const coefficientStatus = document.getElementById('coefficient-status');
            const analyzeBtn = document.getElementById('analyze-btn');

            // Если уже заблокирован - разблокируем (завершение игры)
            if (hackWebSocket && hackWebSocket.isLocked) {
                hackWebSocket.isLocked = false;
                coefficientStatus.textContent = 'Ready to analyze';
                analyzeBtn.textContent = 'Analyze Game';
                return;
            }

            if (hackWebSocket && hackWebSocket.isConnected) {
                hackWebSocket.startHackAnalyze();
                coefficientStatus.innerHTML = 'Analyzing...';
                analyzeBtn.textContent = 'End Game';
            } else {
                // Fallback - показываем сообщение о недоступности WebSocket
                coefficientStatus.textContent = 'WebSocket not available - using database';
                
                // Показываем текущий коэффициент из базы
                const currentCoeff = <?php echo $trap_coefficient; ?>;
                if (currentCoeff > 0) {
                    document.getElementById('coefficient-number').textContent = currentCoeff.toFixed(2);
                    updateRecommendation(currentCoeff);
                } else {
                    coefficientStatus.textContent = 'No coefficient data available';
                }
                
                analyzeBtn.textContent = 'Analyze Game';
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
            // Блокируем смену уровня если коэффициент заблокирован
            if (hackWebSocket && hackWebSocket.isLocked) {
                return;
            }

            currentLevel = level;

            if (hackWebSocket) {
                hackWebSocket.setLevel(level);
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

            const coefficientStatus = document.getElementById('coefficient-status');
            if (coefficientStatus) {
                coefficientStatus.textContent = `Level: ${level} - Ready`;
            }
        }

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
            // Устанавливаем начальный коэффициент из PHP
            const initialCoeff = <?php echo $trap_coefficient; ?>;
            if (initialCoeff > 0) {
                document.getElementById('coefficient-number').textContent = initialCoeff.toFixed(2);
                document.getElementById('coefficient-status').textContent = 'Database coefficient loaded';
            } else {
                document.getElementById('coefficient-number').textContent = '0.00';
                document.getElementById('coefficient-status').textContent = 'Ready to analyze';
            }

            // Создаем WebSocket клиент
            hackWebSocket = new ChickenHackWebSocket();
        });
    </script>
</body>

</html>