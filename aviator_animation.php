<?php
// Путь к базе данных SQLite
$db_file = 'game_state.db';

// Базовое логирование до выполнения основного кода
file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

// Проверка расширения SQLite
if (!class_exists('SQLite3')) {
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Fatal error: SQLite3 class not found\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'SQLite3 extension not installed']);
    exit;
}

// Установка часового пояса
date_default_timezone_set('Asia/Bangkok'); // +07:00

// Логирование для отладки
function logError($message) {
    $timezone = new DateTimeZone('+0700');
    $date = new DateTime('now', $timezone);
    $timestamp = $date->format('Y-m-d H:i:s');
    file_put_contents('error_log.txt', "$timestamp - $message\n", FILE_APPEND);
}

// Инициализация базы данных SQLite
function initDatabase() {
    global $db_file;
    try {
        $db = new SQLite3($db_file);
        if (!$db) {
            throw new Exception("Failed to open SQLite database");
        }
        $db->exec("CREATE TABLE IF NOT EXISTS state (key TEXT PRIMARY KEY, value TEXT)");
        logError("Database initialized successfully");
        return $db;
    } catch (Exception $e) {
        logError("Failed to initialize database: " . $e->getMessage());
        throw $e;
    }
}

// Инициализация состояния по умолчанию
function getDefaultState($current_time) {
    return [
        'game_time' => 0,
        'crash_time' => (mt_rand(40, 120) / 10), // 4–12 секунд
        'is_crashed' => false,
        'crash_pause' => 0,
        'last_update' => $current_time,
        'is_reset' => false,
        'preload_duration' => 0, // Оставшееся время прелоадера в секундах
        'crash_multiplier' => 1.00,
        'crash_timestamp' => 0 // Время последнего краша
    ];
}

// Чтение состояния из базы данных
function readGameState() {
    global $db_file;
    $current_time = isset($_POST['timestamp']) ? floatval($_POST['timestamp']) / 1000 : microtime(true);
    $delta_time = isset($_POST['delta_time']) ? floatval($_POST['delta_time']) : 0;

    try {
        $db = initDatabase();
        logError("Starting transaction for readGameState");
        $db->exec("BEGIN EXCLUSIVE TRANSACTION");

        $result = $db->querySingle("SELECT value FROM state WHERE key = 'global_state'", true);
        logError("Query executed: SELECT value FROM state WHERE key = 'global_state'");

        if (!$result) {
            $state = getDefaultState($current_time);
            $db->exec("INSERT INTO state (key, value) VALUES ('global_state', '" . json_encode($state) . "')");
            logError("Initialized new game state. Crash time: {$state['crash_time']}");
        } else {
            $state = json_decode($result['value'], true);
            if (!$state || !isset($state['game_time'])) {
                logError("Failed to decode state from database: {$result['value']}");
                $state = getDefaultState($current_time);
                $db->exec("UPDATE state SET value = '" . json_encode($state) . "' WHERE key = 'global_state'");
            }
        }

        logError("Current state - preload_duration: {$state['preload_duration']}, current_time: $current_time");

        // Обновление времени прелоадера
        if ($state['preload_duration'] > 0) {
            $state['preload_duration'] = max(0, $state['preload_duration'] - $delta_time);
            logError("Updated preload_duration: {$state['preload_duration']}");
        }

        // Обновление game_time, если прелоадер не активен
        if ($state['preload_duration'] <= 0 && !$state['is_crashed']) {
            $state['game_time'] += $delta_time;
            logError("Updated game_time: {$state['game_time']}");
        }
        $state['last_update'] = $current_time;

        // Проверка краша
        if ($state['game_time'] >= $state['crash_time'] && !$state['is_crashed']) {
            $state['is_crashed'] = true;
            $state['crash_multiplier'] = round(max(1, 1 + 0.5 * (exp($state['game_time'] / 10) - 1)), 2);
            $state['crash_timestamp'] = $current_time;
            logError("Crash detected at game_time: {$state['game_time']}, crash_time: {$state['crash_time']}, multiplier: {$state['crash_multiplier']}");
        }

        // Обработка краша
        if ($state['is_crashed']) {
            $state['crash_pause'] += $delta_time * 2; // Для интервала 2000 мс
            if ($state['crash_pause'] >= 4) { // 2 секунды после краша
                $state['game_time'] = 0;
                $state['crash_time'] = (mt_rand(40, 120) / 10);
                $state['is_crashed'] = false;
                $state['crash_pause'] = 0;
                $state['preload_duration'] = 3; // 3 секунды прелоадера
                $state['crash_multiplier'] = 1.00;
                logError("Round reset. New crash time: {$state['crash_time']}, preload_duration: {$state['preload_duration']}");
            }
        }

        $db->exec("UPDATE state SET value = '" . json_encode($state) . "' WHERE key = 'global_state'");
        $db->exec("COMMIT");
        $db->close();
        logError("Transaction committed successfully");
        return $state;
    } catch (Exception $e) {
        logError("Error in readGameState: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['get_state'])) {
            logError("Missing get_state parameter in POST request");
            http_response_code(400);
            echo json_encode(['error' => 'Missing get_state parameter']);
            exit;
        }

        $state = readGameState();
        if (!$state) {
            throw new Exception("Invalid game state");
        }

        $multiplier = $state['is_crashed'] ? $state['crash_multiplier'] : max(1, 1 + 0.5 * (exp($state['game_time'] / 10) - 1));
        $response = [
            'multiplier' => round($multiplier, 2),
            'status' => $state['is_crashed'] ? 'crashed' : 'running',
            'is_preloading' => $state['preload_duration'] > 0,
            'multiplier_rate' => 0.5 / 10, // Скорость роста множителя для интерполяции
            'crash_timestamp' => $state['crash_timestamp'] // Время последнего краша
        ];

        if ($state['is_crashed']) {
            $state['crash_pause'] += 4.0; // Для интервала 2000 мс
            if ($state['crash_pause'] >= 4) { // 2 секунды после краша
                $state['game_time'] = 0;
                $state['crash_time'] = (mt_rand(40, 120) / 10);
                $state['is_crashed'] = false;
                $state['crash_pause'] = 0;
                $state['preload_duration'] = 3; // 3 секунды прелоадера
                $state['crash_multiplier'] = 1.00;
                $response['is_preloading'] = true;
                logError("Round reset. New crash time: {$state['crash_time']}, preload_duration: {$state['preload_duration']}");
            }
        }

        $db = initDatabase();
        $db->exec("BEGIN EXCLUSIVE TRANSACTION");
        $db->exec("UPDATE state SET value = '" . json_encode($state) . "' WHERE key = 'global_state'");
        $db->exec("COMMIT");
        $db->close();
        logError("POST request processed successfully");

        echo json_encode($response);
    } catch (Exception $e) {
        logError("Error in POST request: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviator Multiplier Animation</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background: #1a1a1a; color: white; font-family: Arial, sans-serif; }
        canvas { background: #000; }
        #preloader {
            position: absolute;
            display: none;
            width: 50px;
            height: 50px;
            border: 5px solid #00ff00;
            border-top: 5px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas" width="800" height="600"></canvas>
    <div id="preloader"></div>
    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const preloader = document.getElementById('preloader');

        class AviatorSimulator {
            constructor() {
                this.multiplier = 1.00;
                this.displayMultiplier = 1.00;
                this.status = 'running';
                this.particles = [];
                this.isPreloading = false;
                this.isCrashDisplaying = false;
                this.isInitialized = false;
                this.lastCrashMultiplier = 1.00;
                this.localGameTime = 0;
                this.lastUpdateTime = Date.now();
                this.multiplierRate = 0;
                this.lastServerUpdate = Date.now();
                this.crashDisplayTimer = null;
                this.lastCrashTimestamp = 0;
                this.updateGameState();
                setInterval(() => this.updateGameState(), 2000);
            }

            showPreloader() {
                preloader.style.display = 'block';
                preloader.style.left = `${canvas.width / 2 - 25}px`;
                preloader.style.top = `${canvas.height / 2 - 25}px`;
                this.isPreloading = true;
                this.isCrashDisplaying = false;

                setTimeout(() => {
                    preloader.style.display = 'none';
                    this.isPreloading = false;
                    this.status = 'running';
                    this.isCrashDisplaying = false;
                    this.localGameTime = 0;
                    this.lastUpdateTime = Date.now();
                    this.lastServerUpdate = Date.now();
                    console.log('Preloader ended');
                }, 3000);
            }

            updateGameState() {
                const currentTime = Date.now();
                const deltaTime = (currentTime - this.lastUpdateTime) / 1000;
                this.lastUpdateTime = currentTime;

                if (this.status === 'running' && !this.isPreloading && !this.isCrashDisplaying) {
                    this.localGameTime += deltaTime;
                    const elapsed = (currentTime - this.lastServerUpdate) / 1000;
                    this.displayMultiplier = Math.max(this.displayMultiplier, this.multiplier + this.multiplierRate * elapsed);
                }

                fetch('aviator_animation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `get_state=1&timestamp=${currentTime}&delta_time=${deltaTime}`
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Network error:', response.status);
                        throw new Error('Network response was not ok ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Server error:', data.error);
                        if (this.status === 'running' && !this.isPreloading && !this.isCrashDisplaying) {
                            this.displayMultiplier = Math.max(this.displayMultiplier, 1 + 0.5 * (Math.exp(this.localGameTime / 10) - 1));
                        }
                        return;
                    }

                    // Проверка краша через crash_timestamp
                    if (data.crash_timestamp > this.lastCrashTimestamp) {
                        this.lastCrashTimestamp = data.crash_timestamp;
                        this.status = 'crashed';
                    }

                    this.status = data.status;
                    this.isPreloading = data.is_preloading;
                    this.isInitialized = true;
                    if (this.isPreloading && !this.isCrashDisplaying) {
                        clearTimeout(this.crashDisplayTimer);
                        this.isCrashDisplaying = true;
                        this.crashDisplayTimer = setTimeout(() => {
                            this.showPreloader();
                        }, 3000);
                    }
                    if (this.status === 'crashed') {
                        this.lastCrashMultiplier = data.multiplier || 1.00;
                        if (!this.particles.length && !this.isPreloading) {
                            this.createExplosion();
                        }
                    } else if (!this.isCrashDisplaying) {
                        this.multiplier = data.multiplier || 1.00;
                        this.displayMultiplier = Math.max(this.displayMultiplier, this.multiplier);
                        this.localGameTime = 0;
                        this.multiplierRate = data.multiplier_rate || 0;
                        this.lastServerUpdate = currentTime;
                    }
                    console.log('Update - Multiplier:', this.multiplier, 'Display Multiplier:', this.displayMultiplier, 'Status:', this.status, 'Preloading:', this.isPreloading, 'CrashDisplaying:', this.isCrashDisplaying, 'Crash Timestamp:', this.lastCrashTimestamp);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    this.isInitialized = true;
                    if (this.status === 'running' && !this.isPreloading && !this.isCrashDisplaying) {
                        this.displayMultiplier = Math.max(this.displayMultiplier, 1 + 0.5 * (Math.exp(this.localGameTime / 10) - 1));
                    }
                });
            }

            createExplosion() {
                for (let i = 0; i < 50; i++) {
                    this.particles.push({
                        x: canvas.width / 2,
                        y: canvas.height / 2,
                        radius: Math.random() * 5 + 2,
                        vx: (Math.random() - 0.5) * 400,
                        vy: (Math.random() - 0.5) * 400,
                        alpha: 1
                    });
                }
            }

            draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                if (!this.isPreloading) {
                    ctx.font = 'bold 100px Arial';
                    ctx.fillStyle = this.status === 'crashed' ? 'red' : 'white';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const displayMultiplier = this.status === 'crashed' ? this.lastCrashMultiplier : this.displayMultiplier;
                    ctx.fillText(`${displayMultiplier.toFixed(2)}x`, canvas.width / 2, canvas.height / 2);

                    if (this.status === 'crashed') {
                        this.particles.forEach(p => {
                            ctx.beginPath();
                            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                            ctx.fillStyle = `rgba(255, 100, 100, ${p.alpha})`;
                            ctx.fill();
                            p.x += p.vx * 0.016;
                            p.y += p.vy * 0.016;
                            p.alpha -= 0.02;
                            p.radius *= 0.98;
                        });
                        this.particles = this.particles.filter(p => p.alpha > 0);
                    }
                }
            }
        }

        const simulator = new AviatorSimulator();

        function animate() {
            simulator.draw();
            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>
</html>