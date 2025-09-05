<?php
session_start();
include 'overlaying.php';

// Получаем user_id из URL параметров (как в chicken-road игре)
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// Если user_id не передан, перенаправляем на главную
if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Получаем коэффициент ловушки из базы данных
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
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .chicken-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .chicken-header {
            margin-bottom: 30px;
        }

        .chicken-title {
            font-size: 2.5em;
            margin: 20px 0;
            color: #FFD700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* Большой коэффициент по центру */
        .coefficient-display {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            border-radius: 20px;
            padding: 40px 20px;
            margin: 30px 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            border: 3px solid #FFD700;
        }

        .coefficient-label {
            font-size: 1.2em;
            color: white;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .coefficient-value {
            font-size: 4em;
            font-weight: bold;
            color: white;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.7);
            margin: 20px 0;
        }

        .x-symbol {
            color: #FFD700;
            font-size: 0.8em;
        }

        .coefficient-status {
            font-size: 1.1em;
            color: #ffed4a;
            margin-top: 10px;
        }

        /* Кнопка анализа */
        .analyze-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            font-size: 1.3em;
            font-weight: bold;
            cursor: pointer;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
            width: 100%;
        }

        .analyze-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
        }

        .analyze-btn:active {
            transform: translateY(0);
        }

        /* Выбор уровня */
        .level-selector {
            margin: 30px 0;
        }

        .level-selector h3 {
            color: #FFD700;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .level-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .level-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 15px 10px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .level-btn:hover {
            transform: scale(1.05);
            border-color: #FFD700;
        }

        .level-btn.selected {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.3);
        }

        .level-btn small {
            display: block;
            margin-top: 5px;
            opacity: 0.8;
            font-size: 0.8em;
        }

        /* Информация о выборе */
        .current-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .current-info p {
            margin: 10px 0;
            font-size: 1.1em;
        }

        .current-info span {
            color: #FFD700;
            font-weight: bold;
        }

        /* Статус загрузки */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>

<body>
    <div class="chicken-container">
        <div class="chicken-header">
            <h1 class="chicken-title">🐔 Bot Hack Chicken Road</h1>
        </div>

        <!-- Большой коэффициент по центру -->
        <div class="coefficient-display">
            <div class="coefficient-label">El pollo morirá en:</div>
            <div class="coefficient-value" id="trap-coefficient">
                <span id="coefficient-number"><?php echo number_format($trap_coefficient, 2); ?></span><span class="x-symbol">x</span>
            </div>
            <div class="coefficient-status" id="coefficient-status">Listo para analizar</div>
        </div>

        <!-- Кнопка анализа -->
        <button class="analyze-btn" id="analyze-btn" onclick="analyzeChickenGame()">
            📊 Análisis del juego
        </button>

        <!-- Выбор уровня -->
        <div class="level-selector">
            <h3>Selecciona el nivel de dificultad:</h3>
            <div class="level-buttons">
                <button class="level-btn" data-level="easy" onclick="selectLevel('easy')">
                    🟢 Fácil<br><small>3 obstáculos</small>
                </button>
                <button class="level-btn" data-level="medium" onclick="selectLevel('medium')">
                    🟡 Medio<br><small>7 obstáculos</small>
                </button>
                <button class="level-btn" data-level="hard" onclick="selectLevel('hard')">
                    🟠 Difícil<br><small>12 obstáculos</small>
                </button>
                <button class="level-btn" data-level="hardcore" onclick="selectLevel('hardcore')">
                    🔴 Extremo<br><small>24 obstáculos</small>
                </button>
            </div>
        </div>

        <!-- Информация о текущем выборе -->
        <div class="current-info" id="current-info">
            <p>📋 Nivel seleccionado: <span id="selected-level">No seleccionado</span></p>
            <p>⚠️ Riesgo: <span id="risk-level">-</span></p>
            <p>🎯 Recomendación: <span id="recommendation">Selecciona un nivel para analizar</span></p>
        </div>

    </div>

    <script>
        const userId = <?php echo $user_id; ?>;
        let currentLevel = null;
        let analyzing = false;

        // Функция анализа игры
        function analyzeChickenGame() {
            if (analyzing) return;
            
            analyzing = true;
            const analyzeBtn = document.getElementById('analyze-btn');
            const coefficientStatus = document.getElementById('coefficient-status');
            
            analyzeBtn.textContent = '🔄 Analizando...';
            analyzeBtn.classList.add('loading');
            coefficientStatus.textContent = 'Cargando datos actuales...';
            coefficientStatus.classList.add('pulse');

            // Загружаем актуальный коэффициент из базы данных
            fetch('/hack/pe/db-chicken-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_chicken_coefficient&user_id=<?php echo $user_id; ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coefficient) {
                    // Обновляем коэффициент из базы данных
                    const dbCoefficient = parseFloat(data.coefficient).toFixed(2);
                    
                    document.getElementById('coefficient-number').textContent = dbCoefficient;
                    coefficientStatus.textContent = '¡Datos actualizados desde la base!';
                    coefficientStatus.classList.remove('pulse');
                    
                    // Обновляем рекомендацию
                    updateRecommendation(dbCoefficient);
                } else {
                    // Если в базе нет данных
                    coefficientStatus.textContent = 'No hay datos en la base para este usuario';
                    coefficientStatus.classList.remove('pulse');
                }
                
                analyzeBtn.textContent = '📊 Análisis del juego';
                analyzeBtn.classList.remove('loading');
                analyzing = false;
            })
            .catch(error => {
                console.error('Ошибка загрузки из базы:', error);
                
                coefficientStatus.textContent = 'Error al cargar datos';
                coefficientStatus.classList.remove('pulse');
                
                analyzeBtn.textContent = '📊 Análisis del juego';
                analyzeBtn.classList.remove('loading');
                analyzing = false;
            });
        }

        // Функция выбора уровня
        function selectLevel(level) {
            currentLevel = level;
            
            // Убираем выделение с всех кнопок
            document.querySelectorAll('.level-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Выделяем выбранную кнопку
            document.querySelector(`[data-level="${level}"]`).classList.add('selected');
            
            // Обновляем информацию
            const levelNames = {
                'easy': 'Fácil (3 obstáculos)',
                'medium': 'Medio (7 obstáculos)', 
                'hard': 'Difícil (12 obstáculos)',
                'hardcore': 'Extremo (24 obstáculos)'
            };
            
            const riskLevels = {
                'easy': 'Bajo',
                'medium': 'Medio',
                'hard': 'Alto', 
                'hardcore': 'Extremo'
            };
            
            document.getElementById('selected-level').textContent = levelNames[level];
            document.getElementById('risk-level').textContent = riskLevels[level];
            
            // Обновляем рекомендацию
            const coefficient = parseFloat(document.getElementById('coefficient-number').textContent);
            updateRecommendation(coefficient);
        }

        // Обновление рекомендации
        function updateRecommendation(coefficient) {
            if (!currentLevel) {
                document.getElementById('recommendation').textContent = 'Selecciona un nivel para analizar';
                return;
            }
            
            const coeff = parseFloat(coefficient);
            let recommendation = '';
            
            if (coeff < 2.0) {
                recommendation = '🔴 ¡Cuidado! Coeficiente bajo';
            } else if (coeff < 3.0) {
                recommendation = '🟡 Riesgo moderado';
            } else if (coeff < 5.0) {
                recommendation = '🟢 Buenas posibilidades';
            } else {
                recommendation = '✨ ¡Excelentes posibilidades!';
            }
            
            document.getElementById('recommendation').textContent = recommendation;
        }

        // Обновление коэффициента в базе данных
        function updateCoefficientInDB(coefficient) {
            fetch('/hack/pe/db-chicken-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_chicken_coefficient&coefficient=${coefficient}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Coeficiente actualizado:', data);
            })
            .catch(error => {
                console.error('Error al actualizar:', error);
            });
        }

        // Загрузка актуального коэффициента при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/hack/pe/db-chicken-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_chicken_coefficient`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coefficient) {
                    document.getElementById('coefficient-number').textContent = parseFloat(data.coefficient).toFixed(2);
                }
            })
            .catch(error => {
                console.error('Error al cargar:', error);
            });
        });
    </script>
</body>
</html>
