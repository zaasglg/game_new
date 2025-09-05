console.log('Game2.js loaded!');
console.log('window.CFS:', window.CFS);
console.log('DOM ready state:', document.readyState);
console.log('Game container:', document.querySelector('#game_container'));

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
    cfs: window.CFS || {
        easy: [ 1.03, 1.07, 1.12, 1.17, 1.23, 1.29, 1.36, 1.44, 1.53, 1.63 ], 
        medium: [ 1.12, 1.28, 1.47, 1.70, 1.98, 2.33, 2.76, 3.32, 4.03, 4.96 ],  
        hard: [ 1.23, 1.55, 1.98, 2.56, 3.36, 4.49, 5.49, 7.53, 10.56, 15.21 ], 
        hardcore: [ 1.63, 2.80, 4.95, 9.08, 15.21, 30.12, 62.96, 140.24, 337.19, 890.19 ]
    },  
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
        // Определяем режим игры (demo или real)
        this.gameMode = this.getGameMode();
        console.log('Constructor - gameMode:', this.gameMode);
        console.log('Constructor - window.GAME_MODE:', window.GAME_MODE);
        console.log('Constructor - window.USER_ID:', window.USER_ID);
        
        // Устанавливаем начальный баланс (сначала демо, потом загрузим реальный)
        this.balance = window.DEMO_BALANCE || 500.00;
        
        // Загружаем реальный баланс асинхронно если нужно
        if (this.gameMode === 'real') {
            console.log('Loading real balance...');
            this.loadRealBalance();
        } else {
            console.log('Using demo balance:', this.balance);
        }
        
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
        this.create(); 
        this.bind(); 
        $('#game_container').css('min-height', parseInt( $('#main').css('height') )+'px' );
    } 
    
    getGameMode() {
        // Проверяем URL параметры или localStorage для определения режима
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        let mode = urlParams.get('mode') || localStorage.getItem('gameMode') || 'demo';
        
        // Если есть user_id, автоматически переключаемся в реальный режим
        if (userId && mode === 'demo') {
            mode = 'real';
        }
        
        console.log('getGameMode() - URL params:', urlParams.toString());
        console.log('getGameMode() - user_id:', userId);
        console.log('getGameMode() - mode from URL:', urlParams.get('mode'));
        console.log('getGameMode() - final mode:', mode);
        return mode;
    }
    
    getRealBalance() {
        // Получаем user_id из URL или других источников
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        
        if (userId) {
            // Делаем синхронный запрос для получения реального баланса
            let realBalance = 500.00; // fallback
            
            $.ajax({
                url: './get_real_balance.php',
                method: 'GET',
                data: { user_id: userId },
                async: false, // синхронный запрос
                success: function(response) {
                    if (response.success) {
                        realBalance = response.balance;
                    }
                },
                error: function() {
                    console.log('Error getting real balance, using demo balance');
                }
            });
            
            return realBalance;
        }
        
        return window.DEMO_BALANCE || 500.00;
    }
    
    loadRealBalance() {
        // Асинхронно загружаем реальный баланс
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        
        console.log('loadRealBalance() - userId:', userId);
        
        if (userId) {
            console.log('Fetching real balance for user:', userId);
            fetch('./get_real_balance.php?user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    console.log('Real balance API response:', data);
                    if (data.success) {
                        this.balance = data.balance;
                        console.log('Balance updated to:', this.balance);
                        this.updateBalanceDisplay();
                    }
                })
                .catch(error => {
                    console.log('Error loading real balance:', error);
                });
        } else {
            console.log('No user_id found, staying with demo balance');
        }
    }
    
    updateBalanceDisplay() {
        // Ждем, пока элемент баланса станет доступен
        const balanceElement = $('[data-rel="menu-balance"] span');
        
        if (balanceElement.length > 0) {
            const currentValue = parseFloat(balanceElement.html()) || 0;
            const newValue = parseFloat(this.balance.toFixed(2));
            
            // Обновляем только если значение действительно изменилось
            if (currentValue !== newValue) {
                balanceElement.html(this.balance.toFixed(2));
                console.log('Balance updated from', currentValue, 'to', newValue);
            }
        }
    }
    
    create(){
        this.wrap.html('').css('left', 0);
        
        // Сбрасываем позицию камеры при создании новой игры
        this.resetCameraPosition();
        
        // Обновляем баланс при создании игры
        this.updateBalanceDisplay();
        // Обновляем cfs если window.CFS стал доступен
        if (window.CFS && !SETTINGS.cfs) {
            SETTINGS.cfs = window.CFS;
        }
        var $arr = SETTINGS.cfs && SETTINGS.cfs[this.cur_lvl] ? SETTINGS.cfs[this.cur_lvl] : [1.03, 1.07, 1.12, 1.17, 1.23]; 
        this.wrap.append(`<div class="sector start" data-id="0">
                                <div class="breaks" breaks="3"></div>
                                <div class="breaks" breaks="2"></div>
                                <img src="./res/img/arc.png" class="entry" alt="">
                                <div class="border"></div>
                            </div>`); 
        var $flame_segment = //this.selectValueHybridIndex( SETTINGS.cfs[ this.cur_lvl ], SETTINGS.chance );
            // Добавляем возможность сгореть на первом шаге
            // 20% шанс сгореть на первом шаге (позиция 0)
            // 80% шанс использовать обычную логику
            Math.random() * 100 < 20 ? 0 : Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
        this.fire = $flame_segment; 
        for( var $i=0; $i<$arr.length; $i++ ){
            if( $i == $arr.length - 1 ){
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

        // Инициализация для мобильных устройств
        if (window.innerWidth <= 760) {
            $('#battlefield').css('left', '0px').data('transform', 0);
            console.log('Mobile mode activated, battlefield initialized with transform support');
        }

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
                }
            });
            this.cur_status = 'game'; 
            this.stp = 0; 
            this.alife = 1; 
            CHICKEN.alife = 1; 
            this.balance -= this.current_bet;
            this.updateBalanceDisplay(); 
            $('.sector').off().on('click', function(){ 
                GAME.move(); 
            });
            this.move(); 
        }
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
        
        // Сбрасываем позицию камеры при завершении игры
        this.resetCameraPosition();
        
        setTimeout(
            function(){ 
                $('#overlay').hide(); 
                GAME.cur_status = "loading"; 
                $('#win_modal').hide(); 
                GAME.create();  
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
        // Улучшенная логика прокрутки для всех устройств
        if( $cur_x > ( SETTINGS.w / 3 ) ) { 
            var $battlefield = $('#battlefield');
            var $battlefield_width = parseInt( $battlefield.css('width') );
            var maxScroll = -($battlefield_width - SETTINGS.w - SETTINGS.segw);
            
            // Используем transform для лучшей производительности на мобильных
            if (window.innerWidth <= 760) {
                var currentTransform = $battlefield.data('transform') || 0;
                var newTransform = currentTransform - SETTINGS.segw;
                newTransform = Math.max(newTransform, maxScroll);
                
                $battlefield.css('transform', 'translateX(' + newTransform + 'px)');
                $battlefield.data('transform', newTransform);
                console.log('Mobile scroll: transform =', newTransform);
            } else {
                // Для десктопа используем CSS left как раньше
                var $field_x = parseInt( $battlefield.css('left') ); 
                if ($field_x > maxScroll) {
                    var $nfx = $field_x - SETTINGS.segw +'px';
                    $battlefield.css('left', $nfx);
                }
            }
        }
    }
    getCurrentSector() { 
        var parent = document.querySelector('#battlefield'); 
        var player = document.querySelector('#chick'); 
        var sectors = document.querySelectorAll('#battlefield .sector'); 
        var playerRect = player.getBoundingClientRect();
        var parentRect = parent.getBoundingClientRect(); 
        
        // Учитываем transform если используется на мобильных устройствах
        var playerPosX = playerRect.left - parentRect.left;
        if (window.innerWidth <= 760) {
            var $battlefield = $('#battlefield');
            var transform = $battlefield.data('transform') || 0;
            playerPosX = playerPosX - transform;
        }
        
        var sectorIndex = Math.floor( playerPosX / SETTINGS.segw ); 
        if( sectorIndex >= 0 && sectorIndex < sectors.length ){ 
            return $('#battlefield .sector').eq(sectorIndex); //sectors[ sectorIndex ]; 
        } 
        else { return null; }
    } 
    
    resetCameraPosition() {
        var $battlefield = $('#battlefield');
        
        // Добавляем плавную анимацию для возврата камеры
        $battlefield.css('transition', 'all 0.8s ease-out');
        
        // Сбрасываем позицию для десктопа
        $battlefield.css('left', '0px');
        
        // Сбрасываем transform для мобильных устройств
        if (window.innerWidth <= 760) {
            $battlefield.css('transform', 'translateX(0px)');
            $battlefield.data('transform', 0);
            console.log('Camera position reset for mobile with animation');
        }
        
        // Убираем transition после анимации
        setTimeout(function() {
            $battlefield.css('transition', 'all 0.5s linear');
        }, 800);
        
        console.log('Camera position reset to initial state with smooth animation');
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
}

// Инициализируем игру только после загрузки DOM
$(document).ready(function() {
    // Убеждаемся что window.CFS загружен
    if (window.CFS) {
        SETTINGS.cfs = window.CFS;
    }
    
    // Создаем игру
    window.GAME = new Game({}); 
    
    // Запускаем цикл рендера
    render();
});

var GAME = null; 

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
    if( window.GAME ){
        window.GAME.update(); 
    }

    requestAnimationFrame( render );
}

setTimeout( function(){ open_game(); }, 1000 );

// Улучшенная поддержка мобильной прокрутки
function setupMobileScrolling() {
    if (window.innerWidth <= 760) {
        let battlefield = document.getElementById('battlefield');
        if (!battlefield) return;
        
        // Добавляем поддержку swipe жестов для мобильных устройств
        let startX = 0;
        let startY = 0;
        let isHorizontalScrolling = false;
        let isVerticalScrolling = false;
        
        battlefield.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isHorizontalScrolling = false;
            isVerticalScrolling = false;
        }, { passive: true });
        
        battlefield.addEventListener('touchmove', function(e) {
            if (!isHorizontalScrolling && !isVerticalScrolling) {
                let currentX = e.touches[0].clientX;
                let currentY = e.touches[0].clientY;
                let deltaX = Math.abs(currentX - startX);
                let deltaY = Math.abs(currentY - startY);
                
                // Определяем направление прокрутки
                if (deltaX > deltaY && deltaX > 10) {
                    isHorizontalScrolling = true;
                    // Только для горизонтальной прокрутки предотвращаем default
                    e.preventDefault();
                } else if (deltaY > deltaX && deltaY > 10) {
                    isVerticalScrolling = true;
                    // Для вертикальной прокрутки разрешаем default поведение
                }
            } else if (isHorizontalScrolling) {
                // Предотвращаем только горизонтальную прокрутку
                e.preventDefault();
            }
            // Для вертикальной прокрутки ничего не делаем (разрешаем default)
        }, { passive: false });
        
        // Обеспечиваем smooth scrolling
        battlefield.style.scrollBehavior = 'smooth';
        
        // Добавляем отладку для проверки прокрутки
        console.log('Mobile scrolling setup completed');
        console.log('Vertical scroll enabled:', document.body.style.overflowY !== 'hidden');
        console.log('Page height:', document.body.scrollHeight);
        console.log('Viewport height:', window.innerHeight);
    }
}

// Вызываем функцию настройки мобильной прокрутки
document.addEventListener('DOMContentLoaded', setupMobileScrolling);

// Также вызываем при изменении размера окна
window.addEventListener('resize', function() {
    setTimeout(setupMobileScrolling, 100);
});




