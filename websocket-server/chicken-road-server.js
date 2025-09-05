const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');

const app = express();
const server = http.createServer(app);

// Настройка CORS для WebSocket
const io = socketIo(server, {
  cors: {
    origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
    methods: ["GET", "POST"],
    allowedHeaders: ["*"],
    credentials: true
  }
});

app.use(cors());
app.use(express.json());

// Функция для хэширования строки (простая реализация для детерминизма)
function hashString(str) {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    const char = str.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convert to 32bit integer
  }
  return Math.abs(hash);
}

// Seeded Random класс для детерминированной генерации
class SeededRandom {
  constructor(seed) {
    this.seed = seed;
  }
  
  nextFloat() {
    this.seed = (this.seed * 9301 + 49297) % 233280;
    return this.seed / 233280;
  }
}

// Хранилище подключений по user_id
const userConnections = new Map();
const gameRooms = new Map(); // user_id -> room_data

// Конфигурация игры (ГОТОВЫЕ коэффициенты из game2.js)
const GAME_CONFIG = {
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

// Генератор точной последовательности ходов
class ExactMovesGenerator {
  constructor() {
    this.gameSeeds = new Map(); // user_id -> { seed, sequence }
    this.activeGames = new Map(); // user_id -> game_data
  }

  // Генерация seed'а для новой игры
  generateGameSeed(user_id, difficulty = 'easy') {
    const seed = Math.floor(Math.random() * 1000000);
    
    // Генерируем точную последовательность для всей игры
    const exactSequence = this.generateExactSequence(seed, difficulty);
    
    this.gameSeeds.set(user_id, {
      seed,
      difficulty,
      sequence: exactSequence,
      created_at: new Date()
    });

    console.log(`🎯 Generated exact sequence for ${user_id}:`, {
      seed,
      difficulty,
      total_moves: exactSequence.length
    });

    return {
      user_id,
      seed,
      difficulty,
      sequence: exactSequence,
      total_moves: exactSequence.length
    };
  }

  // Генерация точной последовательности (ТОЧНАЯ копия алгоритма из game2.js строка 236)
  generateExactSequence(seed, difficulty) {
    const sequence = [];
    const cfs = GAME_CONFIG.cfs[difficulty];
    const chance = GAME_CONFIG.chance[difficulty];

    // Генерируем только ОДИН flame segment для всей игры (как в реальной игре)
    const random1 = this.seededRandom(seed, 0);
    const random2 = this.seededRandom(seed, 1);
    
    // ТОЧНЫЙ алгоритм из строки 236 game2.js:
    // Math.random() * 100 < 20 ? 0 : Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
    let flameSegment;
    
    if (random1 * 100 < 20) {
      // 20% шанс сгореть на первом шаге
      flameSegment = 0;
    } else {
      // 80% шанс использовать обычную логику
      const useSecondChance = Math.round(random2 * 100) > 95; // 5% шанс использовать второй шанс
      const selectedChance = chance[useSecondChance ? 1 : 0];
      const random3 = this.seededRandom(seed, 2);
      flameSegment = Math.ceil(random3 * selectedChance);
    }

    console.log(`🎯 Generated flame segment ${flameSegment} for difficulty ${difficulty} (seed: ${seed})`);

    // Создаем последовательность: все шаги до flame segment = безопасно, flame segment = бомба
    for (let step = 0; step < 15; step++) { // Максимум 15 шагов как в игре
      const isSafe = step < flameSegment;
      const multiplier = step < cfs.length ? cfs[step] : cfs[cfs.length - 1];
      
      sequence.push({
        step: step + 1,
        position: step, // Позиция курочки
        safe: isSafe,
        multiplier: multiplier,
        result: isSafe ? 'safe' : 'bomb',
        is_flame_segment: step === flameSegment
      });

      // Если достигли flame segment - игра заканчивается
      if (!isSafe) {
        break;
      }
    }

    return sequence;
  }

  // Тот же алгоритм рандома что в игре
  seededRandom(seed, step) {
    const x = Math.sin(seed + step * 12345) * 10000;
    return x - Math.floor(x);
  }

  // Получить следующие N ходов для пользователя
  getNextMoves(user_id, current_step = 0, count = 10) {
    const gameData = this.gameSeeds.get(user_id);
    if (!gameData) {
      return null;
    }

    const nextMoves = gameData.sequence.slice(current_step, current_step + count);
    
    return {
      user_id,
      current_step,
      next_moves: nextMoves,
      remaining_moves: gameData.sequence.length - current_step,
      game_will_end_at: gameData.sequence.length
    };
  }

  // Получить полную последовательность
  getFullSequence(user_id) {
    const gameData = this.gameSeeds.get(user_id);
    return gameData ? gameData.sequence : null;
  }

  // Проверить результат хода
  validateMove(user_id, step, position) {
    const gameData = this.gameSeeds.get(user_id);
    if (!gameData || step > gameData.sequence.length || step < 1) {
      return null;
    }

    const expectedMove = gameData.sequence[step - 1];
    
    return {
      step,
      expected_position: expectedMove.position,
      actual_position: position,
      result: expectedMove.result,
      safe: expectedMove.safe,
      multiplier: expectedMove.multiplier,
      is_flame_segment: expectedMove.is_flame_segment,
      position_match: expectedMove.position === position, // Проверяем совпадение позиций
      step_valid: true
    };
  }

  // Создать новую игру для пользователя
  startNewGame(user_id, difficulty = 'easy') {
    return this.generateGameSeed(user_id, difficulty);
  }

  // Очистить данные игры
  clearGame(user_id) {
    this.gameSeeds.delete(user_id);
    this.activeGames.delete(user_id);
  }
}

const exactGenerator = new ExactMovesGenerator();

console.log('🎮 Chicken Road WebSocket Server Starting...');

io.on('connection', (socket) => {
  console.log(`📱 New connection: ${socket.id}`);
  
  // Регистрация пользователя
  socket.on('register_user', (data) => {
    const { user_id, type } = data; // type: 'game' или 'hack'
    
    if (!user_id) {
      socket.emit('error', { message: 'user_id is required' });
      return;
    }
    
    // Сохраняем соединение
    if (!userConnections.has(user_id)) {
      userConnections.set(user_id, {});
    }
    
    userConnections.get(user_id)[type] = socket;
    socket.user_id = user_id;
    socket.connection_type = type;
    
    console.log(`👤 User ${user_id} registered as ${type}`);
    
    // Уведомляем об успешной регистрации
    socket.emit('registered', { 
      user_id, 
      type, 
      status: 'connected' 
    });
    
    // Если это игра, создаем комнату
    if (type === 'game') {
      if (!gameRooms.has(user_id)) {
        gameRooms.set(user_id, {
          user_id,
          game_state: 'waiting',
          current_round: null,
          predictions: [],
          created_at: new Date()
        });
      }
      
      // Отправляем текущее состояние комнаты хак-боту (если подключен)
      const userConns = userConnections.get(user_id);
      if (userConns && userConns.hack) {
        userConns.hack.emit('game_connected', {
          user_id,
          room_data: gameRooms.get(user_id)
        });
      }
    }
    
    // Если это хак-бот, отправляем состояние игры (если игра подключена)
    if (type === 'hack') {
      const roomData = gameRooms.get(user_id);
      if (roomData) {
        socket.emit('game_state', roomData);
      }
    }
  });
  
  // Получение данных от игры
  socket.on('game_update', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🎯 Game update from ${user_id}:`, data);
    
    // Обновляем состояние комнаты
    if (gameRooms.has(user_id)) {
      const room = gameRooms.get(user_id);
      Object.assign(room, data, { 
        last_update: new Date(),
        user_id 
      });
      gameRooms.set(user_id, room);
    }
    
    // Отправляем данные хак-боту
    const userConns = userConnections.get(user_id);
    if (userConns && userConns.hack) {
      userConns.hack.emit('game_update', {
        user_id,
        ...data,
        timestamp: new Date()
      });
    }
  });
  
  // Запрос на анализ от хак-бота - теперь возвращает точную последовательность
  socket.on('request_analysis', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🔍 Exact sequence request from ${user_id}:`, data);
    
    // Создаем новую игру с точной последовательностью
    const difficulty = data.difficulty || 'easy';
    const exactGame = exactGenerator.startNewGame(user_id, difficulty);
    
    // Получаем следующие ходы
    const nextMoves = exactGenerator.getNextMoves(user_id, 0, 15);
    
    // Отправляем точную последовательность хак-боту
    socket.emit('exact_sequence', {
      user_id,
      request_id: data.request_id,
      exact_game: exactGame,
      next_moves: nextMoves,
      message: 'Точная последовательность ходов сгенерирована',
      timestamp: new Date()
    });
    
    // Также отправляем игре (если подключена)
    const userConns = userConnections.get(user_id);
    if (userConns && userConns.game) {
      userConns.game.emit('exact_sequence', {
        user_id,
        exact_game: exactGame,
        next_moves: nextMoves,
        timestamp: new Date()
      });
    }
  });
  
  // Запрос следующих ходов
  socket.on('get_next_moves', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🎯 Next moves request from ${user_id}:`, data);
    
    const currentStep = data.current_step || 0;
    const count = data.count || 10;
    const nextMoves = exactGenerator.getNextMoves(user_id, currentStep, count);
    
    if (nextMoves) {
      socket.emit('next_moves_response', {
        user_id,
        ...nextMoves,
        timestamp: new Date()
      });
    } else {
      socket.emit('error', {
        message: 'Игра не найдена. Создайте новую игру.'
      });
    }
  });
  
  // Проверка хода игрока
  socket.on('validate_move', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`✅ Move validation from ${user_id}:`, data);
    
    const validation = exactGenerator.validateMove(user_id, data.step, data.position);
    
    if (validation) {
      // Отправляем результат валидации
      socket.emit('move_result', {
        user_id,
        validation,
        timestamp: new Date()
      });
      
      // Отправляем результат другому соединению (игра <-> хак)
      const userConns = userConnections.get(user_id);
      if (userConns) {
        const targetConnection = socket.connection_type === 'game' ? userConns.hack : userConns.game;
        if (targetConnection) {
          targetConnection.emit('move_result', {
            user_id,
            validation,
            from: socket.connection_type,
            timestamp: new Date()
          });
        }
      }
    }
  });
  
  // Начать новую игру
  socket.on('start_new_game', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🎮 New game started by ${user_id}:`, data);
    
    const difficulty = data.difficulty || 'easy';
    const exactGame = exactGenerator.startNewGame(user_id, difficulty);
    const nextMoves = exactGenerator.getNextMoves(user_id, 0, 15);
    
    // Уведомляем все соединения пользователя
    const userConns = userConnections.get(user_id);
    if (userConns) {
      Object.values(userConns).forEach(conn => {
        if (conn) {
          conn.emit('new_game_started', {
            user_id,
            exact_game: exactGame,
            next_moves: nextMoves,
            timestamp: new Date()
          });
        }
      });
    }
  });
  
  // Завершить игру
  socket.on('end_game', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🏁 Game ended by ${user_id}:`, data);
    
    // Очищаем данные игры
    exactGenerator.clearGame(user_id);
    
    // Уведомляем все соединения
    const userConns = userConnections.get(user_id);
    if (userConns) {
      Object.values(userConns).forEach(conn => {
        if (conn) {
          conn.emit('game_ended', {
            user_id,
            final_result: data.result,
            timestamp: new Date()
          });
        }
      });
    }
  });

  // Получение прогнозов от хак-бота (оставляем для совместимости)
  socket.on('hack_prediction', (data) => {
    const { user_id } = socket;
    if (!user_id) return;
    
    console.log(`🔮 Hack prediction from ${user_id}:`, data);
    
    // Сохраняем прогноз в комнате
    if (gameRooms.has(user_id)) {
      const room = gameRooms.get(user_id);
      if (!room.predictions) room.predictions = [];
      room.predictions.push({
        ...data,
        timestamp: new Date()
      });
      gameRooms.set(user_id, room);
    }
    
    // Отправляем прогноз игре
    const userConns = userConnections.get(user_id);
    if (userConns && userConns.game) {
      userConns.game.emit('hack_prediction', {
        user_id,
        ...data,
        timestamp: new Date()
      });
    }
  });

  // Отключение
  socket.on('disconnect', () => {
    const { user_id, connection_type } = socket;
    
    console.log(`📱 Disconnected: ${socket.id}${user_id ? ` (${user_id}, ${connection_type})` : ''}`);
    
    if (user_id && userConnections.has(user_id)) {
      const userConns = userConnections.get(user_id);
      if (userConns[connection_type]) {
        delete userConns[connection_type];
        
        // Если больше нет соединений для этого пользователя
        if (Object.keys(userConns).length === 0) {
          userConnections.delete(user_id);
          gameRooms.delete(user_id);
          console.log(`🗑️ Removed user ${user_id} completely`);
        } else {
          // Уведомляем оставшиеся соединения об отключении
          Object.values(userConns).forEach(conn => {
            if (conn) {
              conn.emit('peer_disconnected', {
                user_id,
                disconnected_type: connection_type
              });
            }
          });
        }
      }
    }
  });
});

// API endpoints
app.get('/status', (req, res) => {
  res.json({
    status: 'running',
    connections: userConnections.size,
    rooms: gameRooms.size,
    timestamp: new Date()
  });
});

app.get('/users', (req, res) => {
  const users = Array.from(userConnections.keys()).map(user_id => {
    const connections = userConnections.get(user_id);
    const room = gameRooms.get(user_id);
    return {
      user_id,
      connections: Object.keys(connections),
      room_state: room ? room.game_state : null,
      last_update: room ? room.last_update : null,
      auto_prediction: room ? room.auto_prediction : null
    };
  });
  
  res.json({
    users,
    total: users.length
  });
});

// Генерация точной последовательности для пользователя (API)
app.get('/exact-sequence/:user_id', (req, res) => {
  const { user_id } = req.params;
  const { difficulty = 'easy' } = req.query;
  
  try {
    const exactGame = exactGenerator.startNewGame(user_id, difficulty);
    res.json(exactGame);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Получение следующих ходов (API)
app.get('/next-moves/:user_id', (req, res) => {
  const { user_id } = req.params;
  const { current_step = 0, count = 10 } = req.query;
  
  try {
    const nextMoves = exactGenerator.getNextMoves(user_id, parseInt(current_step), parseInt(count));
    if (nextMoves) {
      res.json(nextMoves);
    } else {
      res.status(404).json({ error: 'Game not found for user' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Получение полной последовательности (API)
app.get('/full-sequence/:user_id', (req, res) => {
  const { user_id } = req.params;
  
  try {
    const sequence = exactGenerator.getFullSequence(user_id);
    if (sequence) {
      res.json({ user_id, sequence, total_moves: sequence.length });
    } else {
      res.status(404).json({ error: 'Game not found for user' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Валидация хода (API)
app.post('/validate-move/:user_id', (req, res) => {
  const { user_id } = req.params;
  const { step, position } = req.body;
  
  try {
    const validation = exactGenerator.validateMove(user_id, step, position);
    if (validation) {
      res.json(validation);
    } else {
      res.status(404).json({ error: 'Game not found or invalid step' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Генерация коэффициентов для игры (API) - возвращает ГОТОВЫЕ коэффициенты из game2.js
app.get('/generate-coefficients/:user_id', (req, res) => {
  const { user_id } = req.params;
  const { difficulty = 'easy' } = req.query;
  
  try {
    // Возвращаем ГОТОВЫЕ коэффициенты из game2.js
    const coefficients = GAME_CONFIG.cfs[difficulty];
    
    if (!coefficients) {
      return res.status(400).json({ 
        error: `Неверная сложность: ${difficulty}. Доступные: easy, medium, hard, hardcore` 
      });
    }
    
    res.json({
      user_id,
      difficulty,
      coefficients,
      total_coefficients: coefficients.length,
      generated_at: new Date(),
      message: 'Готовые коэффициенты из game2.js получены'
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Генерация flame segment для игры (API) - определяет до какого коэффициента дойдет курица
app.get('/generate-flame-segment/:user_id', (req, res) => {
  const { user_id } = req.params;
  const { difficulty = 'easy' } = req.query;
  
  try {
    // Генерируем flame segment на основе user_id и сложности
    const flameData = generateFlameSegment(user_id, difficulty);
    
    res.json({
      user_id,
      difficulty,
      ...flameData,
      generated_at: new Date(),
      message: 'Flame segment сгенерирован для игры'
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Функция генерации flame segment (ТОЧНАЯ копия из game2.js)
function generateFlameSegment(user_id, difficulty) {
  // Создаем seed ТОЛЬКО на основе user_id и difficulty для полной детерминированности
  const seed = hashString(user_id + difficulty);
  const rng = new SeededRandom(seed);
  
  const cfs = GAME_CONFIG.cfs[difficulty];
  const chance = GAME_CONFIG.chance[difficulty];
  
  // ТОЧНЫЙ алгоритм из game2.js строка 236:
  // Math.random() * 100 < 20 ? 0 : Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
  let flameSegment;
  
  if (rng.nextFloat() * 100 < 20) {
    // 20% шанс сгореть на первом шаге
    flameSegment = 0;
  } else {
    // 80% шанс использовать обычную логику
    const useSecondChance = Math.round(rng.nextFloat() * 100) > 95; // 5% шанс использовать второй шанс
    const selectedChance = chance[useSecondChance ? 1 : 0];
    flameSegment = Math.ceil(rng.nextFloat() * selectedChance);
  }
  
  // Определяем максимальный безопасный коэффициент
  const maxSafeMultiplier = flameSegment > 0 ? cfs[flameSegment - 1] : 1.0;
  
  // Определяем коэффициент, на котором будет огонь
  const flameMultiplier = flameSegment < cfs.length ? cfs[flameSegment] : 'MAX';
  
  return {
    flame_segment: flameSegment,
    max_safe_steps: flameSegment,
    max_safe_multiplier: maxSafeMultiplier,
    flame_multiplier: flameMultiplier,
    total_safe_coefficients: flameSegment,
    seed: seed,
    risk_level: flameSegment === 0 ? 'CRITICAL' : flameSegment <= 2 ? 'HIGH' : flameSegment <= 5 ? 'MEDIUM' : 'LOW'
  };
}

// Graceful shutdown
process.on('SIGINT', () => {
  console.log('\n🛑 Shutting down server...');
  server.close(() => {
    console.log('✅ Server shut down gracefully');
    process.exit(0);
  });
});

// Запуск сервера
const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`🚀 Chicken Road WebSocket Server запущен на порту ${PORT}`);
  console.log(`📡 WebSocket: ws://localhost:${PORT}`);
  console.log(`🌐 HTTP API: http://localhost:${PORT}`);
  console.log(`📊 Статус: http://localhost:${PORT}/status`);
});
