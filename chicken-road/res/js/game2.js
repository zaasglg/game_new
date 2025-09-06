var SETTINGS = {
    w: document.querySelector('#game_container').offsetWidth, //$('#canvas').width(), 
    h: document.querySelector('#game_container').offsetHeight, //$('#canvas').height(), 
    start: {
        x: 0, 
        y: 0 
    }, 
    timers: {  
    }, 
    volume: {
        active: +$('body').data('sound'), 
        music: +$('body').data('sound') ? 0.2 : 0, 
        sound: +$('body').data('music') ? 0.9 : 0
    }, 
    currency: $('body').attr('data-currency') ? $('body').attr('data-currency')  : "USD", 
    //cfs: {
    //    easy: [ 1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63, 1.75, 1.88, 2.04, 2.22, 2.45, 2.72, 3.06, 3.50, 4.08, 4.90, 6.13, 6.61, 9.81, 19.44 ], 
    //    medium: [ 1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96, 6.20, 6.91, 8.90, 11.74, 15.99, 22.61, 33.58, 53.20, 92.17, 182.51, 451.71, 1788.80 ],  
    //    hard: [ 1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21, 22.59, 34.79, 55.97, 94.99, 172.42, 341.40, 760.46, 2007.63, 6956.47, 41321.43 ], 
    //    hardcore: [ 1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19, 2643.89, 9161.08, 39301.05, 233448.29 ]
    //}, 
    cfs: window.CFS,  
    chance: {
        easy: [ 7, 23 ], 
        medium: [ 5, 15 ], 
        hard: [ 3, 10 ], 
        hardcore: [ 2, 6 ]
    },
    min_bet: 0.5, 
    max_bet: 150, 
    segw: parseInt( $('#battlefield .sector').css('width') )  
} 

var SOUNDS = {
    music: new Howl({
        src: ['res/sfx/music.webm'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: true, 
        volume: SETTINGS.volume.music 
    }), 
    button: new Howl({
        src: ['res/sfx/button.webm'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: false, 
        volume: SETTINGS.volume.sound 
    }), 
    win: new Howl({
        src: ['res/sfx/win.webm'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: false, 
        volume: SETTINGS.volume.sound 
    }), 
    lose: new Howl({
        src: ['res/sfx/lose.webm'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: false, 
        volume: SETTINGS.volume.sound 
    }), 
    step: new Howl({
        src: ['res/sfx/step.webm'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: false, 
        volume: SETTINGS.volume.sound 
    })
}

class Game{
    constructor( $obj ){ 
        this.balance = +$('[data-rel="menu-balance"] span').html(); 
        this.currency = SETTINGS.currency; 
        this.stp = 0;  
        this.cur_cfs = 'easy'; 
        this.cur_lvl = 'easy'; 
        this.current_bet = 0; 
        this.cur_status = "loading"; 
        this.wrap = $('#battlefield'); 
        this.sectors = []; 
        this.alife = 0; 
        this.win = 0; 
        this.fire = 0; 
        this.saved_coefficient = null; // Сохраненный коэффициент ловушки
        this.coefficient_updater = null; // Таймер для обновления коэффициентов
        this.auto_update_active = true; // Флаг активности автообновления
        this.loadSavedCoefficient(); // Загружаем сохраненный коэффициент
        this.startCoefficientAutoUpdate(); // Запускаем автообновление
        this.create(); 
        this.bind(); 
        $('#game_container').css('min-height', parseInt( $('#main').css('height') )+'px' );
    }
    
    // Функция для запуска автоматического обновления коэффициентов каждые 3 секунды
    startCoefficientAutoUpdate() {
        var self = this;
        
        // Останавливаем предыдущий таймер если он есть
        this.stopCoefficientAutoUpdate();
        
        this.coefficient_updater = setInterval(function() {
            // Проверяем можно ли обновлять
            if (self.auto_update_active && self.cur_status === "loading") {
                self.autoUpdateCoefficient();
            }
        }, 3000); // Каждые 3 секунды
        
        console.log("Автоматическое обновление коэффициентов запущено (каждые 3 сек)");
    }
    
    // Функция для остановки автоматического обновления
    stopCoefficientAutoUpdate() {
        if (this.coefficient_updater) {
            clearInterval(this.coefficient_updater);
            this.coefficient_updater = null;
            console.log("Автоматическое обновление коэффициентов остановлено");
        }
    }
    
    // Функция для паузы автоматического обновления (без остановки таймера)
    // Функция для приостановки автоматического обновления
    pauseCoefficientAutoUpdate() {
        console.log("🔥 ВЫЗВАНА pauseCoefficientAutoUpdate");
        console.log("🔥 До изменений - auto_update_active:", this.auto_update_active);
        console.log("🔥 До изменений - coefficient_updater:", this.coefficient_updater !== null);
        
        this.auto_update_active = false;
        
        // Также останавливаем таймер
        if (this.coefficient_updater) {
            console.log("🔥 Останавливаем таймер...");
            clearInterval(this.coefficient_updater);
            this.coefficient_updater = null;
            console.log("🔥 Таймер остановлен");
        } else {
            console.log("🔥 Таймер уже был остановлен");
        }
        
        console.log("🔥 После изменений - auto_update_active:", this.auto_update_active);
        console.log("🔥 После изменений - coefficient_updater:", this.coefficient_updater !== null);
        console.log("Автоматическое обновление коэффициентов приостановлено");
    }
    
    // Функция для возобновления автоматического обновления
    resumeCoefficientAutoUpdate() {
        this.auto_update_active = true;
        // Заново запускаем таймер автообновления
        this.startCoefficientAutoUpdate();
        console.log("Автоматическое обновление коэффициентов возобновлено");
    }
    
    // Функция для автоматического обновления коэффициента
    autoUpdateCoefficient() {
        console.log("🔄 ВЫЗВАНА autoUpdateCoefficient");
        console.log("🔄 auto_update_active:", this.auto_update_active);
        console.log("🔄 cur_status:", this.cur_status);
        
        // Получаем массив коэффициентов для текущего уровня сложности
        var currentLevelCoefficients = SETTINGS.cfs[this.cur_lvl];
        
        if (!currentLevelCoefficients || currentLevelCoefficients.length === 0) {
            console.log("Ошибка: коэффициенты для уровня " + this.cur_lvl + " не найдены");
            return;
        }
        
        // Выбираем случайный коэффициент из текущего уровня сложности
        var randomIndex = Math.floor(Math.random() * currentLevelCoefficients.length);
        var newCoefficient = currentLevelCoefficients[randomIndex];
        
        var isDemo = (typeof window.HOST_ID === 'undefined' || window.HOST_ID === 'demo') ? 1 : 0;
        
        $.ajax({
            url: "/hack/pe/db-chicken-api.php", 
            type: "json", 
            method: "post",
            data: { 
                action: "update_chicken_coefficient",
                coefficient: newCoefficient,
                user_id: window.HOST_ID || 'demo',
                is_demo: isDemo,
                auto_update: true, // Флаг автоматического обновления
                difficulty_level: this.cur_lvl, // Передаем уровень сложности
                coefficient_index: randomIndex // Передаем индекс в массиве
            },
            error: function(e) { 
                console.log("Ошибка автообновления коэффициента:", e); 
            },
            success: function(r) {
                var obj = typeof r == "string" ? eval('('+r+')') : r;
                if (obj.success) {
                    console.log("Коэффициент автоматически обновлен для уровня " + GAME.cur_lvl + ":", newCoefficient + " (индекс: " + randomIndex + ")");
                    // Обновляем сохраненный коэффициент
                    GAME.saved_coefficient = parseFloat(newCoefficient);
                }
            }
        });
    }
    
    // Функция для загрузки сохраненного коэффициента ловушки
    loadSavedCoefficient() {
        var isDemo = (typeof window.HOST_ID === 'undefined' || window.HOST_ID === 'demo') ? 1 : 0;
        var self = this;
        
        $.ajax({
            url: "/hack/pe/db-chicken-api.php", 
            type: "json", 
            method: "post",
            data: { 
                action: "get_chicken_coefficient",
                user_id: window.HOST_ID || 'demo',
                is_demo: isDemo
            },
            error: function(e) { 
                console.log("Ошибка загрузки коэффициента ловушки:", e);
                // Используем локальное сохранение как резерв
                var localCoeff = localStorage.getItem('chicken_trap_coefficient');
                self.saved_coefficient = localCoeff ? parseFloat(localCoeff) : null;
            },
            success: function(r) {
                var obj = typeof r == "string" ? eval('('+r+')') : r;
                if (obj.success) {
                    self.saved_coefficient = obj.coefficient;
                    console.log("Загружен сохраненный коэффициент ловушки:", obj.coefficient, "режим:", obj.mode || 'unknown');
                } else {
                    console.log("Не удалось загрузить коэффициент ловушки:", obj.message);
                }
            }
        });
    }
    
    create(){
        this.wrap.html('').css('left', 0);
        var $arr = SETTINGS.cfs[ this.cur_lvl ]; 
        this.wrap.append(`<div class="sector start" data-id="0">
                                <div class="breaks" breaks="3"></div>
                                <div class="breaks" breaks="2"></div>
                                <img src="./res/img/arc.png" class="entry" alt="">
                                <div class="border"></div>
                            </div>`); 
        
        // Используем сохраненный коэффициент для определения позиции ловушки
        var $flame_segment;
        if (this.saved_coefficient !== null) {
            // Находим индекс коэффициента в массиве уровня сложности
            var coeffIndex = $arr.findIndex(coeff => Math.abs(coeff - this.saved_coefficient) < 0.01);
            if (coeffIndex !== -1) {
                $flame_segment = coeffIndex;
                console.log("Использован сохраненный коэффициент ловушки:", this.saved_coefficient, "позиция:", coeffIndex);
            } else {
                // Если коэффициент не найден, используем случайную позицию
                $flame_segment = Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
            }
        } else {
            // Стандартная логика для случайной позиции ловушки
            $flame_segment = Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
        }
        
        this.fire = $flame_segment; 
        for( var $i=0; $i<$arr.length; $i++ ){
            if( $i == $arr.length - 1 ) {
                this.wrap.append(`<div class="sector finish" data-id="${ $i+1 }" ${ $i == $flame_segment ? 'flame="1"' : '' }>
                                        <div class="coincontainer">
                                            <img src="./res/img/bet5.png" alt="" class="coin e">
                                            <img src="./res/img/bet6.png" alt="" class="coin f">
                                            <img src="./res/img/bet7.png" alt="" class="coin g">
                                            <span>${ $arr[ $i ] }x</span>
                                        </div>
                                        <div class="breaks" breaks="6"></div>
                                        <div class="breaks" breaks="5"></div>
                                        <img src="./res/img/arc2.png" class="arc" alt="">
                                        <img src="./res/img/stand.png" class="cup" alt="">
                                        <div class="finish_light"></div>
                                        <img src="./res/img/trigger.png" class="trigger" alt="">
                                        <div class="flame"></div>
                                        <div class="border"></div>
                                    </div>`);
            } 
            else {
                this.wrap.append(`<div class="sector ${ $i ? 'far' : '' }" data-id="${ $i+1 }" ${ $i == $flame_segment ? 'flame="1"' : '' }>
                                        <div class="breaks" breaks="4"></div>
                                        <div class="breaks" breaks="5"></div>
                                        <div class="coincontainer">
                                            <img src="./res/img/betbg.png" alt="" class="coinwrapper">
                                            <img src="./res/img/bet1.png" alt="" class="coin a" data-id="1">
                                            <img src="./res/img/bet2.png" alt="" class="coin b" data-id="2">
                                            <img src="./res/img/bet3.png" alt="" class="coin c" data-id="3">
                                            <img src="./res/img/bet4.png" alt="" class="coin d" data-id="4"> 
                                            <span>${ $arr[ $i ] }x</span>
                                        </div>
                                        <div class="breaks"></div>
                                        <img src="./res/img/frame.png" class="frame" alt="">
                                        <img src="./res/img/trigger.png" class="trigger" alt="">
                                        <!--img src="./res/img/lights2.png" class="lights" alt=""-->
                                        <div class="place_light"></div>
                                        <div class="flame"></div>
                                        <div class="border"></div>
                                    </div>`); 
            }
        } 
        this.wrap.append(`<div class="sector closer" data-id="${ $arr.length+1 }">
                            <div class="border"></div>
                        </div>`); 

        this.wrap.append(`<div id="chick" state="idle"><div class="inner"></div></div>`);

        this.wrap.append(`<div id="fire"></div>`); 
        var $flame_x = document.querySelector('.sector[flame="1"]'); 
        $flame_x = $flame_x ? $flame_x.offsetLeft : 0; 
        $('#fire').css('left', $flame_x +'px')

        SETTINGS.segw = parseInt( $('#battlefield .sector').css('width') ); 

        var $scale = (SETTINGS.segw/(250/100)*(70/100)/100);
        $('#chick').css( 'left', ( SETTINGS.segw / 2 )+'px' );//.css('bottom', ( 60*$scale )+'px' ); 
        $('#chick .inner').css( 'transform', 'translateX(-50%) scale('+ $scale +')' ); 
        var $bottom = 50; 
        if( SETTINGS.w <= 1200 ){ $bottom = 35; }
        if( SETTINGS.w <= 1100 ){ $bottom = 30; }
        if( SETTINGS.w <= 1000 ){ $bottom = 25; }
        if( SETTINGS.w <= 900 ){ $bottom = 5; }
        if( SETTINGS.w <= 800 ){ $bottom = -15; }
        $('#chick').css('bottom', $bottom+'px');

        $('.sector').each(function(){
            var $self = $(this); 
            var $id = $self.data('id');
            $('.breaks', $self).each(function(){
                var $br = $id ? ( Math.round( Math.random() * 12 ) + 4 ) : ( Math.round( Math.random() * 3 ) );
                $(this).attr('breaks', $br );
            });
        });
    }
    start(){ 
        this.current_bet = +$('#bet_size').val();
        if( this.balance && this.current_bet && this.current_bet <= this.balance ){ 
            
            // Останавливаем автоматическое обновление коэффициентов при начале игры
            this.pauseCoefficientAutoUpdate();
            
            // Запускаем игру с текущей позицией ловушки (без загрузки из БД)
            this.startGameWithCurrentTrap();
        }
    }
    
    // Новая функция для загрузки позиции ловушки из базы данных
    loadTrapPositionFromDB() {
        var isDemo = (typeof window.HOST_ID === 'undefined' || window.HOST_ID === 'demo') ? 1 : 0;
        var self = this;
        
        $.ajax({
            url: "/hack/pe/db-chicken-api.php", 
            type: "json", 
            method: "post",
            data: { 
                action: "get_chicken_coefficient",
                user_id: window.HOST_ID || 'demo',
                is_demo: isDemo
            },
            error: function(e) { 
                console.log("Ошибка загрузки позиции ловушки:", e);
                // В случае ошибки используем случайную позицию
                self.startGameWithCurrentTrap();
            },
            success: function(r) {
                var obj = typeof r == "string" ? eval('('+r+')') : r;
                if (obj.success && obj.coefficient) {
                    // Находим позицию ловушки по коэффициенту
                    var $arr = SETTINGS.cfs[self.cur_lvl];
                    var coeffIndex = $arr.findIndex(coeff => Math.abs(coeff - obj.coefficient) < 0.01);
                    
                    if (coeffIndex !== -1) {
                        // Пересоздаем игровое поле с новой позицией ловушки
                        self.fire = coeffIndex;
                        self.saved_coefficient = obj.coefficient;
                        self.create(); // Пересоздаем поле с правильной позицией ловушки
                        console.log("Загружена позиция ловушки из БД:", coeffIndex, "коэффициент:", obj.coefficient);
                    } else {
                        console.log("Коэффициент из БД не найден в текущем уровне, используем случайную позицию");
                    }
                } else {
                    console.log("Не удалось загрузить позицию ловушки из БД:", obj.message);
                }
                
                // Запускаем игру после загрузки/обработки позиции ловушки
                self.startGameWithCurrentTrap();
            }
        });
    }
    
    // Функция для запуска игры с текущей позицией ловушки
    startGameWithCurrentTrap() {
        // Сохраняем коэффициент ловушки в базе данных для всех пользователей
        var trapCoefficient = SETTINGS.cfs[this.cur_lvl][this.fire];
        var isDemo = (typeof window.HOST_ID === 'undefined' || window.HOST_ID === 'demo') ? 1 : 0;
        
        $.ajax({
            url:"/hack/pe/db-chicken-api.php", 
            type:"json", 
            method:"post", 
            data: { 
                action: 'update_chicken_coefficient',
                coefficient: trapCoefficient,
                user_id: window.HOST_ID || 'demo',
                is_demo: isDemo,
                game_started: true // Флаг начала игры
            }, 
            error: function( $e ){ 
                console.log("Hack bot coefficient update error:", $e); 
            }, 
            success: function( $r ){
                console.log("Hack bot coefficient updated:", $r); 
            }
        });
        
        $.ajax({
            url:"/api/bets/add", type:"json", method:"post", 
            data: { 
                lvl: this.cur_lvl, 
                fire: this.fire, 
                bet: this.current_bet 
            }, 
            error: function( $e ){ console.error( $e ); }, 
            success: function( $r ){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                console.log( $r ); 
                
                // Обновляем коэффициент ловушки в hack bot системе для всех режимов
                GAME.updateTrapCoefficient();
            }
        });
        this.cur_status = 'game'; 
        this.stp = 0; 
        this.alife = 1; 
        CHICKEN.alife = 1; 
        this.balance -= this.current_bet;
        $('[data-rel="menu-balance"] span').html( this.balance.toFixed(2) ); 
        $('.sector').off().on('click', function(){ 
            GAME.move(); 
        });
        this.move(); 
    } 
    finish( $win ){
        $('#overlay').show(); 
        this.cur_status = "finish"; 
        this.alife = 0; 
        CHICKEN.alife = 0; 
        $.ajax({
            url:"/api/bets/close", type:"json", method:"post", 
            data:{ stp: GAME.stp }, 
            error: function( $e ){ console.error( $e ); }, 
            success: function( $r ){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                console.log( $r ); 
            }
        });
        if( $win ){ 
            this.win = 1; 
            $('#fire').addClass('active');
            var $award = ( this.current_bet * SETTINGS.cfs[ this.cur_lvl ][ this.stp - 1 ] ); 
            $award = $award ? $award : 0; 
            //console.log("AWARD: "+ $award);
            this.balance += $award; 
            if( SETTINGS.volume.sound ){ SOUNDS.win.play(); } 
            $('#win_modal').css('display', 'flex');
            $('#win_modal h3').html( 'x'+ SETTINGS.cfs[ this.cur_lvl ][ this.stp - 1 ] );
            $('#win_modal h4 span').html( $award.toFixed(2) );
        } 
        else {
            if( SETTINGS.volume.sound ){ SOUNDS.lose.play(); } 
        }
        setTimeout(
            function(){ 
                $('#overlay').hide(); 
                GAME.cur_status = "loading"; 
                $('#win_modal').hide(); 
                GAME.create();
                
                // Возобновляем автоматическое обновление коэффициентов после окончания игры
                GAME.resumeCoefficientAutoUpdate();
            }, $win ? 5000 : 3000  
        ); 
    }
    move(){
        var $chick = $('#chick'); 
        var $cur_x = parseInt( $chick.css('left') );
        var $state = $chick.attr('state'); 
        if( $state == "idle" ){ 
            this.stp += 1;  
            $.ajax({
                url:"/api/bets/move", type:"json", method:"post", 
                data:{ stp: GAME.stp }, 
                error: function( $e ){ console.error( $e ); }, 
                success: function( $r ){
                    var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                    console.log( $r ); 
                }
            });
            if( SETTINGS.volume.sound ){ SOUNDS.step.play(); }
            $chick.attr('state', "go"); 
            var $nx =  $cur_x + SETTINGS.segw + 'px'; 
            $chick.css('left', $nx); 
            var $sector = this.getCurrentSector(); 
            if( $sector && $sector.next() ){ 
                $sector.removeClass('active').addClass('complete');
                $sector = $sector.next();  
                $('.trigger', $sector).addClass('activated');
                $sector.addClass('active'); 
                $sector.next().removeClass('far'); 
                if( +$sector.attr('flame') ){
                    $('#fire').addClass('active'); 
                    CHICKEN.alife = 0; 
                    $chick.attr('state', 'dead'); 
                    $sector.removeClass('active').removeClass('complete').addClass('dead');
                    $('.sector.finish').addClass('lose');
                    GAME.finish(); 
                } 
                else {
                    if( $('.sector').eq( GAME.stp ).hasClass('finish') ){
                        GAME.finish(1); 
                        $('.sector').eq( GAME.stp ).addClass('win');
                    }
                }
            } 
            setTimeout(function(){ 
                if( CHICKEN.alife ){
                    $chick.attr('state', 'idle'); 
                }
                //var $sector = GAME.getCurrentSector(); 
                //if( $sector ){ 
                //     console.log("CUR SECTOR: "+ $sector.data('id'));
                //} 
                //$('.sector').eq( $sector-1 ).removeClass('active').addClass('complete'); 
            }, 500);
        } 
        if( 
            $cur_x > ( SETTINGS.w / 3 ) && 
            parseInt( $('#battlefield').css('left') ) > -( parseInt( $('#battlefield').css('width') ) - SETTINGS.w -SETTINGS.segw )  
        ){ 
            var $field_x = parseInt( $('#battlefield').css('left') ); 
            var $nfx = $field_x - SETTINGS.segw +'px';
            $('#battlefield').css('left', $nfx);
        }
    }
    getCurrentSector() { 
        var parent = document.querySelector('#battlefield'); 
        var player = document.querySelector('#chick'); 
        var sectors = document.querySelectorAll('#battlefield .sector'); 
        var playerRect = player.getBoundingClientRect();
        var parentRect = parent.getBoundingClientRect(); 
        var playerPosX = playerRect.left - parentRect.left;
        var sectorIndex = Math.floor( playerPosX / SETTINGS.segw ); 
        if( sectorIndex >= 0 && sectorIndex < sectors.length ){ 
            return $('#battlefield .sector').eq(sectorIndex); //sectors[ sectorIndex ]; 
        } 
        else { return null; }
    } 
    random_str( length = 8 ){
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt( Math.floor( Math.random() * chars.length ) );
        }
        return result;
    } 
    random_bet(){
        var $user_id = Math.ceil( Math.random() * 70 ); 
        var $user_name = this.random_str(); 
        var $user_win = Math.random() * 1000; 
        var $tmps = `<div class="inner">
                        <img src="./res/img/users/av-${ $user_id }.png" alt="">
                        <h2>${ $user_name }</h2>
                        <h3>+${ $user_win.toFixed(2) } ${ SETTINGS.currency }</h3>
                    </div>`; 
        $('#random_bet').html( $tmps ).css('height', '40px'); 
        setTimeout( function(){ $('#random_bet').html('').css('height', '0px'); }, 6000 );
    } 
    selectValue(mainArray, chanceArray) {
        var randomChance = Math.random();
        var limit = randomChance <= 0.1 ? chanceArray[1] : chanceArray[0];
        var filteredArray = mainArray.filter(value => value <= limit); 
        if( filteredArray.length === 0 ){
           return null;
        }
        var randomIndex = Math.floor( Math.random() * filteredArray.length );
        return randomIndex;
    } 
    selectValueHybridIndex(mainArray, chanceArray) {
        var limit = Math.random() <= 0.1 ? chanceArray[1] : chanceArray[0]; 
        var filteredIndices = mainArray
            .map( ( val, index) => ( { val, index } ) ) 
            .filter( ( { val, index } ) => val <= limit && ( index <= 1 || Math.random() < 0.3 ) )
            .map( ( { index } ) => index ); 
        if( filteredIndices.length === 0 ){
            var fallbackIndex = mainArray.findIndex( val => val <= limit );
            return fallbackIndex !== -1 ? fallbackIndex : null;
        } 
        console.log( filteredIndices[ Math.floor( Math.random() * filteredIndices.length ) ] );
        return filteredIndices[ Math.floor( Math.random() * filteredIndices.length ) ];
    }
    update(){
        switch( this.cur_status ){
            case 'loading': 
                $('#close_bet').css('display', 'none');
                $('#close_bet span').html( 0+' '+GAME.currency ).css('display', 'none');
                $('#start').html( LOCALIZATION.TEXT_BETS_WRAPPER_PLAY );
                $('#dificulity i').hide(); 
                break; 
            case 'game': 
                $('#close_bet').css('display', 'flex'); 
                var $award = ( this.current_bet * SETTINGS.cfs[ this.cur_lvl ][ this.stp - 1 ] ); 
                    $award = $award ? $award.toFixed(2) : 0; 
                $('#close_bet span').html( $award +' '+ SETTINGS.currency ).css('display', 'flex');
                $('#start').html( LOCALIZATION.TEXT_BETS_WRAPPER_GO ); 
                $('#dificulity i').show();
                break; 
            case 'finish': 
                $('#close_bet').css('display', 'none');
                $('#close_bet span').html( 0+' '+GAME.currency ).css('display', 'none'); 
                $('#start').html( LOCALIZATION.TEXT_BETS_WRAPPER_WAIT ); 
                $('#dificulity i').hide();
                break;  
        } 
        $('[data-rel="menu-balance"] span').html( this.balance.toFixed(2) ); 

        var $sector = GAME.getCurrentSector(); 
        if( $sector > 1 ){ 
            $('.sector').eq( $sector-1 ).removeClass('active').addClass('complete'); 
        }
        $('.sector').each(function(){
            var $self=$(this);
            if( !$self.hasClass('flame') && !$self.hasClass('closer') && !$self.hasClass('start') && !$self.hasClass('active') ){
                var $start = Math.round( Math.random() * 1000 ) > 997 ? true : false; 
                if( $start ){
                    $self.addClass('flame');
                    setTimeout( function(){ $self.removeClass('flame') }, 1000 );
                }
            }
        });

        if( Math.round( Math.random() * 100 ) > 99 ){ 
            $('#stats span.online').html( LOCALIZATION.TEXT_LIVE_WINS_ONLINE + ': '+ Math.round( Math.random() * 10000 ));
            GAME.random_bet(); 
        } 
    }
    bind(){
        $(document).ready(function(){ 
            // переключение звука 
            $('#switch_sound').off().on('change', function(){
                var $self=$(this); 
                var $val = $self.is(':checked'); 
                if( !$val ){ SETTINGS.volume.sound = 0; } 
                else { SETTINGS.volume.music = 0.9; } 
                $.ajax({
                    url:"/api/settings", type:"json", method:"post", data:{ play_sounds: $val ? 1 : 0 }
                });
            });
            $('#switch_music').off().on('change', function(){
                var $self=$(this); 
                var $val = $self.is(':checked'); 
                if( !$val ){
                    SOUNDS.music.stop(); 
                    SETTINGS.volume.music = 0; 
                } 
                else {
                    SOUNDS.music.play(); 
                    SETTINGS.volume.music = 0.2;
                } 
                $.ajax({
                    url:"/api/settings", type:"json", method:"post", data:{ play_music: $val ? 1 : 0 }
                });
            });
            // установка ставки в инпуте
            $('#bet_size').off().on('change', function(){ 
                if( GAME.cur_status == 'loading' ){
                    var $self=$(this); 
                    var $val= +$self.val(); 
                    $val = $val < SETTINGS.min_bet ? SETTINGS.min_bet : ( $val > SETTINGS.max_bet ? SETTINGS.max_bet : $val ); 
                    $val = $val >= GAME.balance ? GAME.balance : $val; 
                    $self.val( $val ); 
                }
            });
            // установка ставки кнопками min max
            $('.bet_value_wrapper button').off().on('click', function(){ 
                if( GAME.cur_status == 'loading' ){ 
                    if( SETTINGS.volume.sound ){ SOUNDS.button.play(); } 
                    var $self=$(this); 
                    var $rel = $self.data('rel'); 
                    switch( $rel ){
                        case "min": 
                            $('#bet_size').val( SETTINGS.min_bet );
                            break; 
                        case "max": 
                            $('#bet_size').val( SETTINGS.max_bet >= GAME.balance ? GAME.balance : SETTINGS.max_bet );
                            break; 
                    }
                }
            });
            // установка ставки кнопками со значением
            $('.basic_radio input[name="bet_value"]').off().on('change', function(){ 
                if( GAME.cur_status == 'loading' ){
                    if( SETTINGS.volume.sound ){ SOUNDS.button.play(); } 
                    var $self=$(this); 
                    var $val = +$self.val();  
                    $val = $val >= GAME.balance ? GAME.balance : $val;
                    $('#bet_size').val( $val ); 
                }
            }); 
            // установка уровня сложности
            $('[name="difficulity"]').off().on('change', function(){ 
                if( GAME.cur_status == 'loading' ){ 
                    if( SETTINGS.volume.sound ){ SOUNDS.button.play(); } 
                    var $self=$(this); 
                    var $val = $self.val(); 
                    GAME.cur_lvl = $val; 
                    GAME.create();
                    
                    // Сразу генерируем новый коэффициент для выбранного уровня сложности
                    if (GAME.auto_update_active) {
                        console.log("Уровень сложности изменен на: " + $val + ". Генерируем новый коэффициент...");
                        GAME.autoUpdateCoefficient();
                    }
                } 
                else {
                    return false; 
                }
            });
            // забрать ставку
            $('#close_bet').off().on('click', function(){ 
                if( GAME.stp ){ 
                    if( SETTINGS.volume.sound ){ SOUNDS.button.play(); } 
                    var $self=$(this); 
                    $self.hide(); 
                    GAME.finish(1); 
                }
            });
            // начать игру или сделать ход
            $('#start').off().on('click', function(){ 
                if( SETTINGS.volume.sound ){ SOUNDS.button.play(); } 
                var $self=$(this);
                switch( GAME.cur_status ){
                    case 'loading': 
                        $self.html( LOCALIZATION.TEXT_BETS_WRAPPER_GO ); 
                        if( +$('#bet_size').val() > 0 ){ 
                            GAME.start(); 
                        }
                        break; 
                    case 'game': 
                        if( CHICKEN.alife ){ 
                            $self.html( LOCALIZATION.TEXT_BETS_WRAPPER_GO ); 
                            GAME.move(); 
                        }
                        break; 
                    case 'finish': 
                        $self.html( LOCALIZATION.TEXT_BETS_WRAPPER_WAIT );
                        //GAME.cur_status = "loading";
                        break;  
                }
            }); 
            $('window').on('resize', function(){
                $('#game_container').hide();
                $('#game_container').css('min-height', parseInt( $('#main').css('height') )+'px' );
                $('#game_container').show(); 
                SETTINGS.w = document.querySelector('#game_container').offsetWidth; 
                SETTINGS.segw = parseInt( $('.sector').eq(0).css('width') );
                var $scale = ( SETTINGS.segw/(250/100)*(70/100)/100 );
                $('#chick').css( 'left', ( SETTINGS.segw / 2 )+'px' ); 
                $('#chick .inner').css( 'transform', 'translateX(-50%) scale('+ $scale +')' ); 
                var $bottom = 50; 
                if( SETTINGS.w <= 1200 ){ $bottom = 35; }
                if( SETTINGS.w <= 1100 ){ $bottom = 30; }
                if( SETTINGS.w <= 1000 ){ $bottom = 25; }
                if( SETTINGS.w <= 900 ){ $bottom = 5; }
                if( SETTINGS.w <= 800 ){ $bottom = -15; }
                $('#chick').css('bottom', $bottom+'px');

            });
        }); 
    }
    
    // Функция для обновления коэффициента ловушки в hack bot системе
    updateTrapCoefficient() {
        // Получаем массив коэффициентов для текущего уровня сложности
        var currentLevelCoefficients = SETTINGS.cfs[this.cur_lvl];
        
        if (!currentLevelCoefficients || currentLevelCoefficients.length === 0) {
            console.log("Ошибка: коэффициенты для уровня " + this.cur_lvl + " не найдены");
            return;
        }
        
        // Выбираем случайный коэффициент из текущего уровня сложности
        var randomIndex = Math.floor(Math.random() * currentLevelCoefficients.length);
        var newCoefficient = currentLevelCoefficients[randomIndex];
        
        // Сохраняем коэффициент ловушки для всех режимов (демо и реальный)
        if (typeof window.HOST_ID !== 'undefined') {
            // Отправляем запрос на обновление коэффициента в базе данных
            $.ajax({
                url: "/hack/pe/db-chicken-api.php", 
                type: "json", 
                method: "post",
                data: { 
                    action: "update_chicken_coefficient",
                    coefficient: newCoefficient,
                    user_id: window.HOST_ID,
                    is_demo: window.HOST_ID === 'demo' ? 1 : 0,
                    difficulty_level: this.cur_lvl, // Передаем уровень сложности
                    coefficient_index: randomIndex, // Передаем индекс в массиве
                    manual_update: true // Флаг ручного обновления
                },
                error: function(e) { 
                    console.log("Ошибка обновления коэффициента ловушки:", e); 
                },
                success: function(r) {
                    var obj = typeof r == "string" ? eval('('+r+')') : r;
                    if (window.HOST_ID === 'demo') {
                        console.log("Коэффициент ловушки обновлен для демо режима (уровень " + GAME.cur_lvl + "):", newCoefficient);
                    } else {
                        console.log("Коэффициент ловушки обновлен для user_id " + window.HOST_ID + " (уровень " + GAME.cur_lvl + "):", newCoefficient);
                    }
                }
            });
        } else {
            // Локальное сохранение коэффициента для случаев без HOST_ID
            localStorage.setItem('chicken_trap_coefficient', newCoefficient);
            localStorage.setItem('chicken_trap_difficulty', this.cur_lvl);
            console.log("Коэффициент ловушки сохранен локально для уровня " + this.cur_lvl + ":", newCoefficient);
        }
    }
}

var GAME = new Game({}); 

class Chicken{
    constructor( $obj ){
        this.x = $obj.x ? $obj.x : 0; 
        this.y = $obj.y ? $obj.y : 0; 
        this.w = $obj.w ? $obj.w : SETTINGS.segw * 0.9; 
        this.h = $obj.h ? $obj.w : this.w; 
        this.alife = 0; 
        this.state = 'idle'; 
        this.wrapper = $('#chick');
    }  
}

var CHICKEN = new Chicken({}); 

function open_game(){ 
    $('#splash').addClass('show_modal');
    var $music_settings = SETTINGS.volume.music; 
    var $sound_settings = SETTINGS.volume.sound; 
    $('#splash button').off().on('click', function(){
        $('#splash').remove(); 
        if( SETTINGS.volume.sound ){ 
            SOUNDS.button.play(); 
            $('#switch_sound').removeAttr('checked'); 
        } 
        else {
            $('#switch_sound').attr('checked', 'checked'); 
        }
        if( SETTINGS.volume.music ){ 
            SOUNDS.music.play(); 
            $('#switch_music').removeAttr('checked'); 
        }
        else {
            $('#switch_music').attr('checked', 'checked'); 
        }
    }); 
} 

function render(){ 
    if( GAME ){
        GAME.update(); 
    }

    requestAnimationFrame( render );
}

render(); 

setTimeout( function(){ open_game(); }, 1000 );

// Глобальные функции для управления автообновлением коэффициентов из hack bot
window.stopChickenCoefficientUpdates = function() {
    if (typeof GAME !== 'undefined' && GAME) {
        GAME.pauseCoefficientAutoUpdate();
        console.log("🛑 Автообновление коэффициентов остановлено из hack bot");
        return true;
    }
    return false;
};

window.startChickenCoefficientUpdates = function() {
    if (typeof GAME !== 'undefined' && GAME) {
        GAME.resumeCoefficientAutoUpdate();
        console.log("▶️ Автообновление коэффициентов возобновлено из hack bot");
        return true;
    }
    return false;
};

window.isChickenCoefficientUpdatesActive = function() {
    if (typeof GAME !== 'undefined' && GAME) {
        return GAME.auto_update_active;
    }
    return false;
};

// Функция для hack bot - получение текущего статуса автообновления
window.getChickenAutoUpdateStatus = function() {
    if (typeof GAME !== 'undefined' && GAME) {
        return {
            active: GAME.auto_update_active,
            timer_running: GAME.coefficient_updater !== null,
            game_status: GAME.cur_status,
            current_coefficient: GAME.saved_coefficient
        };
    }
    return null;
};

// Функция для hack bot - обработка нажатия кнопки "📊 Análisis del juego"
window.onChickenGameAnalysisStart = function() {
    console.log("🔥 ВЫЗВАНА ФУНКЦИЯ: onChickenGameAnalysisStart");
    
    if (typeof GAME !== 'undefined' && GAME) {
        console.log("🔥 GAME объект найден, вызываем pauseCoefficientAutoUpdate");
        console.log("🔥 До остановки - auto_update_active:", GAME.auto_update_active);
        console.log("🔥 До остановки - coefficient_updater:", GAME.coefficient_updater !== null);
        
        GAME.pauseCoefficientAutoUpdate();
        
        console.log("🔥 После остановки - auto_update_active:", GAME.auto_update_active);
        console.log("🔥 После остановки - coefficient_updater:", GAME.coefficient_updater !== null);
        console.log("📊 Análisis del juego запущен - автообновление коэффициентов приостановлено");
        return true;
    }
    
    console.log("🔥 ОШИБКА: GAME объект не найден!");
    return false;
};




