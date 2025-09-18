const WebSocket = require('ws');

const SETTINGS = {
    chance: {
        easy: [7, 23],
        medium: [5, 15],
        hard: [3, 10],
        hardcore: [3, 8]
    }
};

const wss = new WebSocket.Server({ port: 8080 });

// Храним последние traps для всех уровней
let lastTrapsByLevel = {
    easy: [],
    medium: [],
    hard: [],
    hardcore: []
};

const clients = new Map(); // ws -> { level, gameActive, lastTraps }
let globalGameActive = false; // Глобальный статус игры - влияет на всех клиентов
const sessionTraps = new Map(); // ws -> { level: trapIndex }
const activeGames = new Set(); // Множество активных игр (WebSocket соединений)

wss.on('connection', function connection(ws) {
    clients.set(ws, { level: 'easy', gameActive: false, lastTraps: [], connectedAt: Date.now() });
    sessionTraps.set(ws, {});
    console.log('Client connected, total clients:', clients.size);

    ws.on('message', function incoming(message) {
        try {
            const data = JSON.parse(message);
            const clientData = clients.get(ws);
            
            if (data.type === 'set_level') {
                clientData.level = data.level;
                // No log
            } else if (data.type === 'set_client_type') {
                clientData.isHackBot = data.isHackBot || false;
                // No log
            } else if (data.type === 'request_traps') {
                // Всегда генерируем новые коэффициенты для всех
                const trapData = generateTraps(clientData.level, 0);
                clientData.lastTraps = trapData.traps;
                // No log
                ws.send(JSON.stringify({ 
                    type: 'traps', 
                    traps: trapData.traps, 
                    level: clientData.level,
                    coefficient: trapData.coefficient,
                    trapIndex: trapData.trapIndex
                }));
            } else if (data.type === 'get_last_traps') {
                // Отправить клиенту последние traps_all_levels
                ws.send(JSON.stringify({ type: 'traps_all_levels', traps: lastTrapsByLevel }));
            } else if (data.type === 'end_game') {
                sessionTraps.forEach((session, ws) => {
                    sessionTraps.set(ws, {});
                });
            } else if (data.type === 'game_start') {
                activeGames.add(ws);
                clientData.gameActive = true;
                console.log(`Game started for client. Active games: ${activeGames.size}`);
            } else if (data.type === 'game_end') {
                activeGames.delete(ws);
                clientData.gameActive = false;
                console.log(`Game ended for client. Active games: ${activeGames.size}`);
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    });

    ws.on('close', function() {
        clients.delete(ws);
        sessionTraps.delete(ws);
        activeGames.delete(ws);
        console.log('Client disconnected, total clients:', clients.size, 'active games:', activeGames.size);
    });

    ws.on('error', function(error) {
        console.error('WebSocket error:', error);
    });
});

// Генерация ловушек каждые 30 секунд (только если игра неактивна глобально)
setInterval(() => {
    if (clients.size > 0) {
        // Проверяем есть ли активные игры
        if (activeGames.size > 0) {
            console.log(`Skipping broadcast - ${activeGames.size} active games in progress`);
            return;
        }
        
        console.log('--- Broadcasting traps for ALL LEVELS to', clients.size, 'clients ---');
        const broadcastSeed = Date.now();
        const allLevels = ['easy', 'medium', 'hard', 'hardcore'];
        const trapsByLevel = {};
        
        allLevels.forEach(level => {
            const trapData = generateTraps(level, 0, broadcastSeed);
            trapsByLevel[level] = {
                traps: trapData.traps,
                coefficient: trapData.coefficient,
                trapIndex: trapData.trapIndex
            };
        });
// Сохраняем последние traps
        Object.assign(lastTrapsByLevel, trapsByLevel);
        
        clients.forEach((clientData, ws) => {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({ type: 'traps_all_levels', traps: trapsByLevel }));
            }
        });
        console.log('--- End broadcast ---\n');
    }
}, 30000);

function generateTraps(level, clientIndex = 0, broadcastSeed = null) {
    const chance = SETTINGS.chance[level];
    if (!chance) return { traps: [], coefficient: 1.0, trapIndex: 0 };

    // Всегда генерируем новый случайный seed
    const seed = broadcastSeed || (Date.now() + Math.floor(Math.random() * 100000) + clientIndex * 1000);
    const random = seededRandom(seed);

    // Коэффициенты для каждого уровня (из оригинальной игры)
    const coefficients = {
        easy: [ 1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63, 1.75, 1.88, 2.04, 2.22, 2.45, 2.72, 3.06, 3.50, 4.08, 4.90, 6.13, 6.61, 9.81, 19.44 ],
        medium: [ 1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96, 6.20, 6.91, 8.90, 11.74, 15.99, 22.61, 33.58, 53.20, 92.17, 182.51, 451.71, 1788.80 ],
        hard: [ 1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21, 22.59, 34.79, 55.97, 94.99, 172.42, 341.40, 760.46, 2007.63, 6956.47, 41321.43 ],
        hardcore: [ 1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19, 2643.89, 9161.08, 39301.05, 233448.29 ]
    };

    const levelCoeffs = coefficients[level] || coefficients.easy;

    // Генерируем случайный trap index в диапазоне шанса для уровня
    const maxTrap = chance[Math.round(random() * 100) > 95 ? 1 : 0];
    const flameIndex = Math.ceil(random() * maxTrap);

    const coefficient = levelCoeffs[flameIndex - 1] || levelCoeffs[0];

    console.log(`Client ${clientIndex}: Level: ${level}, Trap index: ${flameIndex}, Coefficient: ${coefficient}x`);

    // Создаем огонь для всех режимов
    const traps = [];
    if (flameIndex > 0) {
        traps.push(flameIndex);
    }
    
    return {
        traps: traps,
        coefficient: coefficient,
        trapIndex: flameIndex
    };
}

// Функция для создания seeded random generator
function seededRandom(seed) {
    let x = Math.sin(seed) * 10000;
    return function() {
        x = Math.sin(x) * 10000;
        return x - Math.floor(x);
    };
}
