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

const clients = new Map(); // ws -> { level, gameActive, lastTraps }
let globalGameActive = false; // Глобальный статус игры - влияет на всех клиентов
const sessionTraps = new Map(); // ws -> { level: trapIndex }
let lockedCoefficient = null; // Заблокированный коэффициент от хак бота

wss.on('connection', function connection(ws) {
    clients.set(ws, { level: 'easy', gameActive: false, lastTraps: [], connectedAt: Date.now() });
    sessionTraps.set(ws, {});
    console.log('Client connected, total clients:', clients.size);

    ws.on('message', function incoming(message) {
        console.log('Received message:', message.toString());
        try {
            const data = JSON.parse(message);
            const clientData = clients.get(ws);
            
            if (data.type === 'set_level') {
                clientData.level = data.level;
                console.log('Client set level to:', data.level);
            } else if (data.type === 'set_client_type') {
                clientData.isHackBot = data.isHackBot || false;
                console.log('Client type set to:', data.isHackBot ? 'hack bot' : 'player');
            } else if (data.type === 'request_traps') {
                let traps;
                if (lockedCoefficient && !clientData.isHackBot) {
                    // Для основной игры используем заблокированный коэффициент
                    traps = [lockedCoefficient];
                    console.log('Sending locked coefficient to game:', lockedCoefficient);
                    ws.send(JSON.stringify({ type: 'game_traps', traps: traps, level: clientData.level }));
                } else {
                    // Для hack bot всегда генерируем новые
                    traps = generateTraps(clientData.level, 0);
                    clientData.lastTraps = traps;
                    console.log('Generating new traps for level', clientData.level, ':', traps);
                    ws.send(JSON.stringify({ type: 'traps', traps: traps, level: clientData.level }));
                }
            } else if (data.type === 'lock_coefficient') {
                // Хак бот блокирует коэффициент (позицию огня)
                lockedCoefficient = data.coefficient;
                console.log('🔒 Fire position locked:', lockedCoefficient);
                // Уведомляем всех клиентов
                clients.forEach((clientData, clientWs) => {
                    if (clientWs.readyState === WebSocket.OPEN) {
                        clientWs.send(JSON.stringify({ 
                            type: 'coefficient_locked', 
                            firePosition: lockedCoefficient 
                        }));
                    }
                });
            } else if (data.type === 'unlock_coefficient') {
                // Разблокируем коэффициент
                lockedCoefficient = null;
                console.log('🔓 Coefficient unlocked');
                clients.forEach((clientData, clientWs) => {
                    if (clientWs.readyState === WebSocket.OPEN) {
                        clientWs.send(JSON.stringify({ type: 'coefficient_unlocked' }));
                    }
                });
            } else if (data.type === 'end_game') {
                globalGameActive = false;
                sessionTraps.forEach((session, ws) => {
                    sessionTraps.set(ws, {});
                });
                console.log('🏁 END_GAME - Resuming broadcast');
            } else if (data.type === 'game_start') {
                globalGameActive = true;
                console.log('🎮 GAME STARTED - Pausing broadcast');
            } else if (data.type === 'game_end') {
                globalGameActive = false;
                console.log('🏁 GAME ENDED - Resuming broadcast');
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    });

    ws.on('close', function() {
    clients.delete(ws);
    sessionTraps.delete(ws);
    console.log('Client disconnected, total clients:', clients.size);
    });

    ws.on('error', function(error) {
        console.error('WebSocket error:', error);
    });
});

// Генерация ловушек каждые 3 секунды (только если игра неактивна глобально)
setInterval(() => {
    if (clients.size > 0) {
        console.log('--- Broadcasting traps to', clients.size, 'clients ---');
        if (!globalGameActive) {
            // Используем фиксированный seed для всех клиентов в этом broadcast
            const broadcastSeed = Date.now();
            
            // Сортируем клиентов по времени подключения (первый подключенный = игрок)
            const sortedClients = Array.from(clients.entries()).sort((a, b) => a[1].connectedAt - b[1].connectedAt);
            
            sortedClients.forEach(([ws, clientData], index) => {
                if (ws.readyState === WebSocket.OPEN) {
                    // Все клиенты получают одинаковые ловушки (фиксированный seed)
                    const traps = generateTraps(clientData.level, 0, broadcastSeed);
                    clientData.lastTraps = traps;
                    ws.send(JSON.stringify({ type: 'traps', traps: traps, level: clientData.level }));
                }
            });
        } else {
            console.log('🚫 Skipping broadcast - Game is active globally');
        }
        console.log('--- End broadcast ---\n');
    }
}, 3000);

function generateTraps(level, clientIndex = 0, broadcastSeed = null) {
    const chance = SETTINGS.chance[level];
    if (!chance) return [];

    // Всегда генерируем новый случайный seed
    const seed = Date.now() + Math.floor(Math.random() * 100000) + clientIndex * 1000;
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

    const coefficient = levelCoeffs[flameIndex - 1] || levelCoeffs[0]; // -1 потому что индекс с 1

    console.log(`Client ${clientIndex}: Level: ${level}, Trap index: ${flameIndex}, Coefficient: ${coefficient}x`);

    // Создаем огонь для всех режимов
    const traps = [];
    if (flameIndex > 0) {
        traps.push(flameIndex);
    }
    
    return traps;
}

// Функция для создания seeded random generator
function seededRandom(seed) {
    let x = Math.sin(seed) * 10000;
    return function() {
        x = Math.sin(x) * 10000;
        return x - Math.floor(x);
    };
}

console.log('WebSocket server started on ws://localhost:8080');