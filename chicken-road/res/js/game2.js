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
        sound: +$('body').data('sound') ? 0.9 : 0
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
    segw: parseInt( $('#battlefield .sector').css('width') ),
    ws_url: 'wss://valor-games.co/ws'  // WebSocket URL for trap generation
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
        this.traps = null; // for WebSocket traps
        this.ws_attempts = 0;
        this.ws = new WebSocket(SETTINGS.ws_url);
        this.ws.onopen = () => { 
            console.log('Connected to WebSocket for traps'); 
            this.ws.send(JSON.stringify({type: 'set_level', level: this.cur_lvl}));
        };
        this.ws.onmessage = (event) => { 
            console.log('Received WebSocket message:', event.data);
            this.handleWSMessage(event); 
        };
        this.ws.onerror = (error) => { 
            console.error('WebSocket error:', error); 
        };
        this.ws.onclose = () => { 
            console.log('WebSocket closed'); 
        };
        this.create(); 
        this.bind(); 
        $('#game_container').css('min-height', parseInt( $('#main').css('height') )+'px' );
    } 
    handleWSMessage(event) {
        var data = JSON.parse(event.data);
        console.log('Handling WebSocket message:', data);
        if (data.type === 'traps_all_levels' && data.traps) {
            // traps: { easy: [n], medium: [n], ... }
            var trapsForLevel = data.traps[this.cur_lvl];
            if (trapsForLevel && trapsForLevel.length > 0) {
                this.traps = trapsForLevel;
                // Always update board with new traps
                this.createBoard();
                this.updateTraps();
            }
        } else if (data.type === 'traps') {
            console.log('Updating traps:', data.traps);
            this.traps = data.traps;
            this.createBoard();
            this.updateTraps();
        } else if (data.type === 'game_traps') {
            console.log('Game traps received:', data.traps);
            this.traps = data.traps;
            this.createBoard();
            this.updateTraps();
        }
    }
    create(){
        this.traps = null;
        this.ws_attempts = 0;
        this.wrap.html('').css('left', 0);
        // Создаем поле сразу, не ждем WebSocket
        this.createBoard();
        // Если WebSocket подключен, всегда отправляем set_level и сразу получаем ловушки по новому формату
        if (this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify({type: 'set_level', level: this.cur_lvl}));
        }
    }
    createBoard(){
        var $arr = SETTINGS.cfs[ this.cur_lvl ]; 
        this.stp = 0; // Reset step on new board
        this.alife = 0;
        this.win = 0;
        this.fire = 0;
        // Remove old chick and fire if present
        $('#chick').remove();
        $('#fire').remove();
        this.wrap.html('');
        this.wrap.css('left', 0); // Reset camera position
        this.wrap.append(`<div class="sector start" data-id="0">
                                <div class="breaks" breaks="3"></div>
                                <div class="breaks" breaks="2"></div>
                                <img src="./res/img/arc.png" class="entry" alt="">
                                <div class="border"></div>
                            </div>`); 
        var flameSegments = this.traps && this.traps.length > 0 ? this.traps : [];
        this.fire = flameSegments.length > 0 ? flameSegments[0] : 0;
        for( var $i=0; $i<$arr.length; $i++ ){
            // Determine if this sector is a flame
            var isFlame = flameSegments.includes($i+1);
            var coeff = $arr[$i];
            this.wrap.append(`<div class="sector${ $i == $arr.length-1 ? ' finish' : ($i ? ' far' : '') }" data-id="${ $i+1 }"${ isFlame ? ' flame="1"' : '' }>
                <div class="coincontainer">
                    ${$i == $arr.length-1 ? `
                        <img src="./res/img/bet5.png" alt="" class="coin e">
                        <img src="./res/img/bet6.png" alt="" class="coin f">
                        <img src="./res/img/bet7.png" alt="" class="coin g">
                    ` : `
                        <img src="./res/img/betbg.png" alt="" class="coinwrapper">
                        <img src="./res/img/bet1.png" alt="" class="coin a" data-id="1">
                        <img src="./res/img/bet2.png" alt="" class="coin b" data-id="2">
                        <img src="./res/img/bet3.png" alt="" class="coin c" data-id="3">
                        <img src="./res/img/bet4.png" alt="" class="coin d" data-id="4">
                    `}
                    <span>${ coeff }x</span>
                </div>
                ${$i == $arr.length-1 ? `
                    <div class="breaks" breaks="6"></div>
                    <div class="breaks" breaks="5"></div>
                    <img src="./res/img/arc2.png" class="arc" alt="">
                    <img src="./res/img/stand.png" class="cup" alt="">
                    <div class="finish_light"></div>
                    <img src="./res/img/trigger.png" class="trigger" alt="">
                    <div class="flame"></div>
                    <div class="border"></div>
                ` : `
                    <div class="breaks" breaks="4"></div>
                    <div class="breaks" breaks="5"></div>
                    <div class="breaks"></div>
                    <img src="./res/img/frame.png" class="frame" alt="">
                    <img src="./res/img/trigger.png" class="trigger" alt="">
                    <div class="place_light"></div>
                    <div class="flame"></div>
                    <div class="border"></div>
                `}
            </div>`);
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
        $('#chick').css( 'left', ( SETTINGS.segw / 2 )+'px' );
        $('#chick .inner').css( 'transform', 'translateX(-50%) scale('+ $scale +')' ); 
        var $bottom = 50; 
        if( SETTINGS.w <= 1200 ){ $bottom = 35; }
        if( SETTINGS.w <= 1100 ){ $bottom = 30; }
        if( SETTINGS.w <= 1000 ){ $bottom = 25; }
        if( SETTINGS.w <= 900 ){ $bottom = 5; }
        if( SETTINGS.w <= 800 ){ $bottom = -15; }
        $('#chick').css('bottom', $bottom+'px');

        // Reset all sector classes
        $('.sector').removeClass('active complete dead win lose');
        // Set start sector as active
        $('.sector.start').addClass('active');

        $('.sector').each(function(){
            var $self = $(this); 
            var $id = $self.data('id');
            $('.breaks', $self).each(function(){
                var $br = $id ? ( Math.round( Math.random() * 12 ) + 4 ) : ( Math.round( Math.random() * 3 ) );
                $(this).attr('breaks', $br );
            });
        });
    }
    createFallback(){
        var $arr = SETTINGS.cfs[ this.cur_lvl ]; 
        this.wrap.append(`<div class="sector start" data-id="0">
                                <div class="breaks" breaks="3"></div>
                                <div class="breaks" breaks="2"></div>
                                <img src="./res/img/arc.png" class="entry" alt="">
                                <div class="border"></div>
                            </div>`); 
        var $flame_segment;
        if (window.GAME_CONFIG && window.GAME_CONFIG.is_real_mode && Math.random() < 0.2) {
            $flame_segment = 1;
        } else {
            $flame_segment = Math.ceil( Math.random() * SETTINGS.chance[ this.cur_lvl ][ Math.round( Math.random() * 100  ) > 95 ? 1 : 0 ] );
        }
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
        if( this.current_bet && this.current_bet <= (this.balance + this.current_bet) && this.current_bet > 0 ){ 
            this.cur_status = 'game'; 
            this.stp = 0; 
            this.alife = 1; 
            CHICKEN.alife = 1; 
            this.game_result_saved = false; // Сбрасываем флаг для новой игры
            this.balance -= this.current_bet;
            $('[data-rel="menu-balance"] span').html( this.balance.toFixed(2) ); 
            updateBalanceOnServer(this.balance);
            $('.sector').off().on('click', function(){ 
                GAME.move(); 
            });
            // Уведомляем сервер о начале игры
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                this.ws.send(JSON.stringify({type: 'game_start'}));
            }
            
            // Balance updated above
            this.move(); 
        }
    } 
    finish( $win ){
        $('#overlay').show(); 
        this.cur_status = "finish"; 
        this.alife = 0; 
        CHICKEN.alife = 0; 
        // Уведомляем сервер об окончании игры
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify({type: 'game_end'}));
        }
        var $award = 0;
        if( $win ){ 
            this.win = 1; 
            $('#fire').addClass('active');
            $award = ( this.current_bet * SETTINGS.cfs[ this.cur_lvl ][ this.stp - 1 ] ); 
            $award = $award ? $award : 0; 
            this.balance += $award; 
            this.balance = Math.round(this.balance * 100) / 100; // Округляем до 2 знаков
            $('[data-rel="menu-balance"] span').html( this.balance.toFixed(2) );
            updateBalanceOnServer(this.balance);
            if( SETTINGS.volume.sound ){ SOUNDS.win.play(); } 
            $('#win_modal').css('display', 'flex');
            $('#win_modal h3').html( 'x'+ SETTINGS.cfs[ this.cur_lvl ][ this.stp - 1 ] );
            $('#win_modal h4 span').html( $award.toFixed(2) );
        } 
        else {
            if( SETTINGS.volume.sound ){ SOUNDS.lose.play(); } 
        }
        // Сохраняем результат игры в базе данных только один раз
        if (!this.game_result_saved) {
            this.game_result_saved = true;
            saveGameResult($win ? 'win' : 'lose', this.current_bet, $award, this.balance);
        }
        setTimeout(
            function(){ 
                $('#overlay').hide(); 
                $('#win_modal').hide(); 
                $('[data-rel="menu-balance"] span').html( GAME.balance.toFixed(2) );
                GAME.cur_status = "loading"; 
                GAME.game_result_saved = false; // Сбрасываем флаг для новой игры
                // Не пересоздаём поле и не сбрасываем ловушки, чтобы использовать те же traps до следующего обновления от WebSocket
                GAME.createBoard();
            }, $win ? 5000 : 3000  
        ); 
    }
    move(){
        var $chick = $('#chick'); 
        var $cur_x = parseInt( $chick.css('left') );
        var $state = $chick.attr('state'); 
        if( $state == "idle" ){
            this.stp += 1;
            if( SETTINGS.volume.sound ){ SOUNDS.step.play(); }
            $chick.attr('state', "go");
            // Move chick to next sector
            var $nx = $cur_x + SETTINGS.segw;
            $chick.css('left', $nx + 'px');
            // Camera logic: center chick if possible
            var $fieldWidth = $('#battlefield').width();
            var $containerWidth = SETTINGS.w;
            var $left = parseInt($('#battlefield').css('left')) || 0;
            var $chickCenter = $nx + (SETTINGS.segw/2);
            var $targetLeft = $containerWidth/2 - $chickCenter;
            // Clamp so field doesn't scroll out of bounds
            var $maxLeft = 0;
            var $minLeft = $containerWidth - $fieldWidth;
            if ($targetLeft > $maxLeft) $targetLeft = $maxLeft;
            if ($targetLeft < $minLeft) $targetLeft = $minLeft;
            $('#battlefield').css('left', $targetLeft + 'px');
            // Highlight sectors
            var $prevSector = $('.sector').removeClass('active');
            var $sector = $('.sector').eq(this.stp);
            $('.sector').removeClass('active');
            if(this.stp > 0) $('.sector').eq(this.stp-1).addClass('complete');
            $sector.addClass('active');
            $sector.next().removeClass('far');
            $('.trigger', $sector).addClass('activated');
            // Check for flame
            if( +$sector.attr('flame') ){
                $('#fire').addClass('active');
                CHICKEN.alife = 0;
                $chick.attr('state', 'dead');
                $sector.removeClass('active').removeClass('complete').addClass('dead');
                $('.sector.finish').addClass('lose');
                GAME.finish();
            } else {
                if( $sector.hasClass('finish') ){
                    GAME.finish(1);
                    $sector.addClass('win');
                }
            }
            setTimeout(function(){
                if( CHICKEN.alife ){
                    $chick.attr('state', 'idle');
                }
            }, 500);
        }
    }
    getCurrentSector() { 
        var parent = document.querySelector('#battlefield'); 
        var player = document.querySelector('#chick'); 
        if (!player) return null;
        var sectors = document.querySelectorAll('#battlefield .sector'); 
        var playerRect = player.getBoundingClientRect();
        var parentRect = parent.getBoundingClientRect(); 
        var playerPosX = playerRect.left - parentRect.left;
        var sectorIndex = Math.floor( playerPosX / SETTINGS.segw ); 
        if( sectorIndex >= 0 && sectorIndex < sectors.length ){ 
            return sectorIndex; 
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
        // Обновляем отображение баланса только если игра не в состоянии финиша
        if( this.cur_status !== 'finish' ){
            $('[data-rel="menu-balance"] span').html( this.balance.toFixed(2) );
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
                $.post('./api.php', { action: 'save_sound_settings', sound: $val ? 1 : 0 });
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
                $.post('./api.php', { action: 'save_music_settings', music: $val ? 1 : 0 });
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
                    if (GAME.ws && GAME.ws.readyState === WebSocket.OPEN) {
                        GAME.ws.send(JSON.stringify({type: 'set_level', level: GAME.cur_lvl}));
                    }
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
    updateTraps(){
        $('.sector').removeAttr('flame');
        if (this.traps) {
            this.traps.forEach(index => {
                $('.sector').eq(index).attr('flame', '1');
            });
        }
        var $flame_x = document.querySelector('.sector[flame="1"]'); 
        $flame_x = $flame_x ? $flame_x.offsetLeft : 0; 
        $('#fire').css('left', $flame_x +'px');
        this.fire = this.traps && this.traps.length > 0 ? this.traps[0] : 0;
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

function updateBalanceOnServer(balance) {
    if (!window.GAME_CONFIG.is_real_mode || !window.GAME_CONFIG.user_id) {
        console.log('Demo mode - not updating server balance');
        return;
    }
    
    fetch('./api.php?controller=users&action=update_balance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: window.GAME_CONFIG.user_id,
            balance: balance
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            console.log('Balance updated on server:', data);
        } catch (e) {
            console.error('Invalid JSON response:', text);
        }
    })
    .catch(error => {
        console.error('Failed to update balance on server:', error);
    });
}

function saveGameResult(result, bet, award, balance) {
    if (!window.GAME_CONFIG.is_real_mode || !window.GAME_CONFIG.user_id) {
        console.log('Demo mode - not saving game result');
        return;
    }
    
    fetch('./api.php?controller=users&action=save_game_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: window.GAME_CONFIG.user_id,
            balance: balance,
            bet_amount: bet,
            win_amount: award,
            game_result: result
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            console.log('Game result saved:', data);
            if (data.success && data.balance_national) {
                // Отправляем баланс в национальной валюте родительскому окну
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({
                        type: 'balanceUpdated',
                        balance: parseFloat(data.balance_national).toFixed(2),
                        userId: window.GAME_CONFIG.user_id
                    }, '*');
                }
            }
        } catch (e) {
            console.error('Invalid JSON response:', text);
        }
    })
    .catch(error => {
        console.error('Failed to save game result:', error);
    });
}

setTimeout( function(){ open_game(); }, 1000 );




