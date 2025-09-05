// WebSocket клиент для Chicken Road
class ChickenRoadWebSocket {
    constructor() {
        this.socket = null;
        this.user_id = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectInterval = 3000;
        
        this.init();
    }
    
    init() {
        // Получаем user_id из URL или создаем уникальный
        const urlParams = new URLSearchParams(window.location.search);
        this.user_id = urlParams.get('user_id') || this.generateUserId();
        
        console.log('🎮 ChickenRoad WebSocket Client - User ID:', this.user_id);
        
        this.connect();
        
        // Подключаем обработчики игровых событий
        this.attachGameEventListeners();
    }
    
    generateUserId() {
        return 'cr_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }
    
    connect() {
        try {
            console.log('🔌 Connecting to WebSocket server...');
            
            this.socket = io('ws://localhost:3001', {
                transports: ['websocket', 'polling'],
                timeout: 5000,
                reconnection: true,
                reconnectionAttempts: this.maxReconnectAttempts,
                reconnectionDelay: this.reconnectInterval
            });
            
            this.setupEventListeners();
            
        } catch (error) {
            console.error('❌ WebSocket connection error:', error);
            this.scheduleReconnect();
        }
    }
    
    setupEventListeners() {
        this.socket.on('connect', () => {
            console.log('✅ Connected to WebSocket server');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            
            // Регистрируемся как игра
            this.socket.emit('register_user', {
                user_id: this.user_id,
                type: 'game'
            });
        });
        
        this.socket.on('registered', (data) => {
            console.log('👤 Registered successfully:', data);
            
            // Отправляем начальное состояние игры
            this.sendGameState({
                game_state: 'ready',
                difficulty: this.getCurrentDifficulty(),
                bet_amount: this.getCurrentBetAmount(),
                balance: this.getCurrentBalance()
            });
        });
        
        this.socket.on('hack_prediction', (data) => {
            console.log('🔮 Received hack prediction:', data);
            this.handleHackPrediction(data);
        });
        
        this.socket.on('analysis_request', (data) => {
            console.log('🔍 Received analysis request:', data);
            this.handleAnalysisRequest(data);
        });
        
        this.socket.on('disconnect', () => {
            console.log('📱 Disconnected from WebSocket server');
            this.isConnected = false;
        });
        
        this.socket.on('error', (error) => {
            console.error('❌ WebSocket error:', error);
        });
        
        this.socket.on('connect_error', (error) => {
            console.error('❌ Connection error:', error);
            this.scheduleReconnect();
        });
        
        this.socket.on('peer_disconnected', (data) => {
            console.log('👋 Peer disconnected:', data);
        });
    }
    
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('❌ Max reconnection attempts reached');
            return;
        }
        
        this.reconnectAttempts++;
        console.log(`🔄 Reconnecting in ${this.reconnectInterval}ms (attempt ${this.reconnectAttempts})`);
        
        setTimeout(() => {
            this.connect();
        }, this.reconnectInterval);
    }
    
    // Отправка состояния игры
    sendGameState(state) {
        if (!this.isConnected) return;
        
        const gameState = {
            user_id: this.user_id,
            timestamp: new Date(),
            ...state
        };
        
        console.log('📤 Sending game state:', gameState);
        this.socket.emit('game_update', gameState);
    }
    
    // Обработка прогнозов от хак-бота
    handleHackPrediction(data) {
        // Показываем прогноз в игре (визуально)
        this.displayPrediction(data);
        
        // Сохраняем прогноз для использования
        window.LAST_HACK_PREDICTION = data;
        
        // Триггер события для игры
        window.dispatchEvent(new CustomEvent('hackPrediction', { detail: data }));
    }
    
    displayPrediction(data) {
        // Создаем уведомление о прогнозе
        const notification = document.createElement('div');
        notification.className = 'hack-prediction-notification';
        notification.innerHTML = `
            <div class="hack-notification-inner">
                <div class="hack-notification-header">
                    🔮 Hack Prediction
                </div>
                <div class="hack-notification-content">
                    <div class="prediction-data">
                        ${data.prediction_type}: ${JSON.stringify(data.prediction)}
                    </div>
                    <div class="prediction-confidence">
                        Confidence: ${data.confidence || 'N/A'}%
                    </div>
                </div>
            </div>
        `;
        
        // Добавляем стили
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 10000;
            max-width: 300px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Анимация появления
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Удаляем через 5 секунд
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
    
    // Обработка запросов на анализ
    handleAnalysisRequest(data) {
        // Собираем текущие данные игры
        const gameData = this.collectGameData();
        
        // Отправляем ответ
        this.socket.emit('analysis_response', {
            user_id: this.user_id,
            request_id: data.request_id,
            game_data: gameData,
            timestamp: new Date()
        });
    }
    
    // Сбор данных игры для анализа
    collectGameData() {
        const gameData = {
            field_state: this.getFieldState(),
            difficulty: this.getCurrentDifficulty(),
            bet_amount: this.getCurrentBetAmount(),
            balance: this.getCurrentBalance(),
            current_position: this.getCurrentPosition(),
            round_history: this.getRoundHistory()
        };
        
        console.log('📊 Collected game data:', gameData);
        return gameData;
    }
    
    // Получение состояния игрового поля
    getFieldState() {
        const battlefield = document.getElementById('battlefield');
        if (!battlefield) return null;
        
        const cells = battlefield.querySelectorAll('.cell');
        const fieldState = [];
        
        cells.forEach((cell, index) => {
            fieldState.push({
                index,
                classList: Array.from(cell.classList),
                revealed: cell.classList.contains('revealed'),
                safe: cell.classList.contains('safe'),
                danger: cell.classList.contains('danger')
            });
        });
        
        return fieldState;
    }
    
    getCurrentDifficulty() {
        const difficultyInput = document.querySelector('input[name="difficulity"]:checked');
        return difficultyInput ? difficulityInput.value : 'easy';
    }
    
    getCurrentBetAmount() {
        const betInput = document.getElementById('bet_size');
        return betInput ? parseFloat(betInput.value) || 0.5 : 0.5;
    }
    
    getCurrentBalance() {
        return window.DEMO_BALANCE || 500.00;
    }
    
    getCurrentPosition() {
        const activeCell = document.querySelector('.cell.current');
        return activeCell ? Array.from(document.querySelectorAll('.cell')).indexOf(activeCell) : -1;
    }
    
    getRoundHistory() {
        // Возвращаем историю раундов (если доступна)
        return window.ROUND_HISTORY || [];
    }
    
    // Подключение к событиям игры
    attachGameEventListeners() {
        // Начало игры
        document.addEventListener('gameStart', (event) => {
            this.sendGameState({
                game_state: 'playing',
                event: 'game_start',
                difficulty: this.getCurrentDifficulty(),
                bet_amount: this.getCurrentBetAmount()
            });
        });
        
        // Клик по клетке
        document.addEventListener('cellClick', (event) => {
            this.sendGameState({
                game_state: 'playing',
                event: 'cell_click',
                cell_index: event.detail.cellIndex,
                cell_result: event.detail.result
            });
        });
        
        // Конец игры
        document.addEventListener('gameEnd', (event) => {
            this.sendGameState({
                game_state: 'finished',
                event: 'game_end',
                result: event.detail.result,
                final_multiplier: event.detail.multiplier,
                winnings: event.detail.winnings
            });
        });
        
        // Изменение ставки
        document.addEventListener('betChange', (event) => {
            this.sendGameState({
                game_state: 'ready',
                event: 'bet_change',
                new_bet: event.detail.amount
            });
        });
        
        // Изменение сложности
        document.addEventListener('difficultyChange', (event) => {
            this.sendGameState({
                game_state: 'ready',
                event: 'difficulty_change',
                new_difficulty: event.detail.difficulty
            });
        });
    }
    
    // Публичные методы
    getUserId() {
        return this.user_id;
    }
    
    isSocketConnected() {
        return this.isConnected;
    }
    
    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}

// Создаем глобальный экземпляр WebSocket клиента
window.ChickenRoadWS = new ChickenRoadWebSocket();

console.log('🎮 ChickenRoad WebSocket Client initialized');
