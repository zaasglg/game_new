var SETTINGS = {
    w: document.querySelector('#game_field').offsetWidth, //$('#canvas').width(), 
    h: document.querySelector('#game_field').offsetHeight, //$('#canvas').height(), 
    start: {
        x: 20, 
        y: 400  // Fixed position instead of dynamic calculation
    }, 
    timers: { 
        loading: 15000, 
        flight: 300000, 
        finish: 10000 
    }, 
    volume: {
        active: +$('body').attr('data-sound'), 
        music: 0.2, 
        sound: 0.9
    }, 
    currency: $('body').attr('data-currency') ? $('body').attr('data-currency')  : "USD" 
}

var $canvas = document.querySelector("#canvas");
var $ctx = $canvas.getContext("2d");
$canvas.width = SETTINGS.w; 
$canvas.height = SETTINGS.h; 

var SOUNDS = {
    music: new Howl({
        src: ['res/sfx/bg_music.mp3'], 
        //autoplay: true, 
        preload: true, 
        html5: true, 
        loop: true, 
        volume: SETTINGS.volume.music 
    }), 
    sounds: new Howl({
        src: ['res/sfx/sprite_audio.mp3'], 
        "sprite": {
            "away": [
                1700,
                3000
            ],
            "start": [
                6500,
                1000
            ],
            "win": [
                9000,
                1000
            ]
        }, 
        preload: true, 
        html5: true, 
        loop: false, 
        volume: SETTINGS.volume.sound
    })
}

var IMAGES = "res/img/"; 
var $plane_image = [ 
    new Image(), 
    new Image(),
    new Image(),
    new Image() 
];
$plane_image[0].src = IMAGES+'plane-0.png'; 
$plane_image[1].src = IMAGES+'plane-1.png'; 
$plane_image[2].src = IMAGES+'plane-2.png'; 
$plane_image[3].src = IMAGES+'plane-3.png'; 

class Helpers {
    constructor( obj ){ } 
    distance( A, B ){
        var $distance = Math.sqrt( Math.pow( A.x - B.x, 2 ) + Math.pow( A.y - B.y, 2 ) ); 
        return $distance; 
    }
    len( $v ){
        return Math.sqrt( $v.x * $v.x + $v.y * $v.y + 0 );
    } 
    normalize( $v ){ 
        var $len = this.len( $v );
        var $res = { x: ( $v.x / $len ), y: ( $v.y / $len ), z:0 }
        return $res;
    } 
}

var HELPERS = new Helpers({}); 

class Sprite {
    constructor( obj ){
        this.timer = new Date().getTime();
        this.current = 0; 
        this.ctx = obj.ctx;
        this.images = obj.images;
        this.width = obj.width;
        this.height = obj.height; 
        this.speed = obj.speed; 
    } 
    update( obj ){
        var $timer = new Date().getTime(); 
        var $delta = $timer - this.timer; 
        if( $delta >= this.speed ){
            this.current += 1; 
            if( this.current == this.images.length ){ this.current = 0; }
            this.timer = $timer; 
        }
        this.draw( obj ); 
    }
    draw( obj ){
        this.ctx.drawImage(
            this.images[ this.current ],
            obj.x,
            obj.y,
            this.width,
            this.height 
        );
    }
} 

class Chart {
    constructor( obj ){ 
        this.ctx = obj.ctx; 
        this.sx = obj.sx;       // start.x
        this.sy = obj.sy;       // start.y
        this.ax = obj.ax;       // arc.x
        this.ay = obj.ay;       // arc.y 
        this.fx = obj.fx;       // finish.x
        this.fy = obj.fy;       // finish.y
        this.fill = obj.fill ? obj.fill : 'rgba(255, 0, 0, 0.1)'; 
        this.stroke = obj.stroke ? obj.stroke : "red"; 
        this.w = obj.w ? obj.w : 5; 
        this.line = obj.line ? obj.line : 1; 
    }
    update( obj ){
        this.fx = obj.x; 
        this.fy = obj.y; 
        this.ax = ( this.fx - this.sx ) / 2; 
        this.ay = ( SETTINGS.h - 20 ); 
        this.draw();
    }
    draw(){
        // fill
        this.ctx.beginPath();
        this.ctx.moveTo( this.sx, this.sy );
        this.ctx.quadraticCurveTo( this.ax, this.ay, this.fx, this.fy ); 
        this.ctx.lineTo( this.fx, this.sy ); 
        this.ctx.closePath(); 
        this.ctx.fillStyle = this.fill;
        this.ctx.fill();
        // arc
        this.ctx.beginPath();
        this.ctx.moveTo( this.sx, this.sy );
        this.ctx.quadraticCurveTo( this.ax, this.ay, this.fx, this.fy );
        this.ctx.strokeStyle = this.stroke;
        this.ctx.lineWidth = this.w;
        this.ctx.stroke();
        // triangle
        this.ctx.beginPath();
        this.ctx.moveTo( this.fx, this.fy );
        this.ctx.lineTo( this.fx, this.sy );
        this.ctx.lineTo( this.sx, this.sy );
        this.ctx.strokeStyle = this.stroke;
        this.ctx.lineWidth = this.line;
        this.ctx.stroke(); 
    }
}

class Plane {
    constructor( obj ){ 
        this.ctx = obj.ctx; 
        this.x = obj.x; 
        this.y = obj.y; 
        this.w = obj.w; 
        this.h = obj.h; 
        this.sx = obj.sx ? obj.sx : -Math.round( this.w * 0.05 );  
        this.sy = obj.sy ? obj.sy : -Math.round( this.w * 0.45 ); 
        this.img = new Sprite({
            ctx: $ctx,
            images: $plane_image,
            width: this.w,
            height: this.h, 
            speed: 150  // Increased from 100 to 150 for slower sprite animation
        });  
        this.chart = obj.chart; 
        this.vel = 1.5;  // Reduced from 3 to 1.5
        this.status = "idle"; 
        this.route = [
            { x:SETTINGS.w-( SETTINGS.w*0.20 ), y:SETTINGS.h*0.5 }, 
            { x:SETTINGS.w-( SETTINGS.w*0.23 ), y:SETTINGS.h*0.45 }, 
            { x:SETTINGS.w-( SETTINGS.w*0.20 ), y:SETTINGS.h*0.5 }, 
            { x:SETTINGS.w-( SETTINGS.w*0.18 ), y:SETTINGS.h*0.55 }, 
            { x:SETTINGS.w-( SETTINGS.w*0.20 ), y:SETTINGS.h*0.5 }, 
            { x:( SETTINGS.w*100 ), y:SETTINGS.h*0.5 }
        ];
        this.pos = 0; 
        this.trace = obj.trace ? obj.trace : true;
    } 
    move( $dir, $speed ){ 
        var $vector = { x: ( $dir.x - this.x ), y: ( $dir.y - this.y ), z: 0 }
        let V = HELPERS.normalize( $vector ); 
        this.x += V.x * $speed; 
        this.y += V.y * $speed; 
    }
    update( obj ){ 
        if( this.status == "move" ){
            if( HELPERS.distance( { x:this.x, y:this.y }, { x:this.route[ this.pos ].x, y:this.route[ this.pos ].y } ) > 5 ){
                this.move({ x:this.route[ this.pos ].x, y:this.route[ this.pos ].y }, ( !this.pos ? this.vel : ( this.pos > 4 ? this.vel*3 : 0.8 ) ) );  // Reduced multiplier from 10 to 3, and slow speed from 1 to 0.8
            }  
            else {
                this.pos += 1; 
                if( this.pos >= this.route.length ){ 
                    this.pos = 0; 
                    //this.status = "idle"; 
                } 
                if( this.pos > 4 ){ this.pos = 1; }
            }
        } 
        if( this.trace ){ 
            this.chart.update({ x:this.x, y:this.y }); 
        }
        this.img.update({ x:this.x+this.sx, y:this.y+this.sy }); 
        if( this.trace && 2 == 3 ){ 
            this.ctx.closePath();
            this.ctx.beginPath(); 
            this.ctx.lineWidth = 1; 
            this.ctx.strokeStyle = "blue"; 
            this.ctx.fillStyle = "blue"; 
            this.ctx.arc( this.x, this.y, 5, 0*(3.14/180), 360*(3.14/180), false ); 
            this.ctx.fill(); 
            this.ctx.stroke(); 
            this.ctx.closePath(); 
        }
    }
} 

class Game {
    constructor( obj ){ 
        this.user_bets = [0,0];  
        this.autoplay = [{},{}]; 
        this.current_bets = []; 
        this.max_bet = 500; 
        this.generic_chanse = 99.2;
        this.factor = 11; 
        this.timer = new Date().getTime(); 
        this.timers = SETTINGS.timers; 
        this.status = "loading"; 
        this.cur_cf = 1.0; 
        this.win_cf = 2.56; 
        this.new_delta = 0; 
        var $vics = document.querySelectorAll('[data-rel="currency"]'); 
        if( $vics && $vics.length ){
            for( var $vic of $vics ){
                $vic.innerHTML = SETTINGS.currency;
                $vic.value = SETTINGS.currency;
            }
        }
        //$('[data-rel="currency"]').html( SETTINGS.currency ).val( SETTINGS.currency );
        this.game_create({}); 
        this.get_history(); 
        this.get_bets({ user:$user.uid, sort:'id', dir:'desc' });
        this.bind(); 
        document.querySelector('#loading_level').style.display = 'flex'; 
        document.querySelector('#process_level').style.display = 'none';
        document.querySelector('#complete_level').style.display = 'none';
        //$('#loading_level').css('display','flex'); 
        //$('#process_level').css('display', 'none');
        //$('#complete_level').css('display', 'none');
        render();
    }
    bind(){
        console.log("bind() function started");
        console.log("Found", $('#actions_wrapper .make_bet').length, "make_bet buttons");
        
        // Reset all buttons to initial state
        $('.make_bet').each(function(){
            var $btn = $(this);
            $btn.removeClass('danger').removeClass('warning').attr('data-id', 0);
            $('span', $btn).html(LOCALIZATION.make_bet_generic_bet);
            $('h2', $btn).css('display','flex'); 
            $('h3', $btn).hide();
            console.log("Button", $btn.attr('data-src'), "reset to bet mode");
        });
        // звук 
        $('#sound_switcher').off().on('click', function(){
            if( SETTINGS.volume.active ){
                SETTINGS.volume.active = 0; 
                $('#sound_switcher').addClass('off');
                SOUNDS.music.stop(); 
            }
            else {
                SETTINGS.volume.active = 1; 
                $('#sound_switcher').removeClass('off');
                SOUNDS.music.play(); 
            } 
            $('body').attr('data-sound', SETTINGS.volume.active);
            $.ajax({
                url:"index.php?route=api/settings", type:"json", method:"post", data:{ play_sounds: SETTINGS.volume.active }
            });
        });
        // модалка для победы
        $('#modal_wrapper .close').off().on('click', function(){
            $('#modal_wrapper').removeClass('active');
        });
        // модалка автоигры 
        $('.footer .autoplay').off().on('click', function(){ 
            $('#autoplay_modal').css('display', 'flex').attr('data-id', $(this).data('id')); 
        });
        $('#autoplay_modal').off().on('click', function(){
            //$('#autoplay_modal').hide(); 
        });
        $('#autoplay_modal .close').off().on('click', function(){
            $('#autoplay_modal').hide(); 
        });
        $('.modal .modal-content').off().on('click', function(e){
            //e.preventDefault(); 
            //e.stopPropagation(); 
            //$('#autoplay_modal').css('display','flex');
        });
        $('#reset_autoplay').off().on('click', function(){
            $('#autoplay_modal .ranger input[type="text"]').val(0);
            $('#autoplay_modal .switchers input[type="checkbox"]').prop('checked', false);
            $('#autoplay_modal .rounds-wrap label input[type="radio"]').prop('checked', false)
        });
        $('#save_autoplay').off().on('click', function(){
            var $id = +$('#autoplay_modal').attr('data-id');
            var $data = {
                id: $id, 
                bet: 0, 
                rounds: +$('#autoplay_modal [name="numrounds"]:checked').val(), 
                numrounds: 0, 
                isisdecrease: $('#autoplay_modal [name="isdecreases"]').is(':checked'), 
                decrease: +$('#autoplay_modal [name="decreases"]').val(), 
                isincrease: $('#autoplay_modal [name="isincreases"]').is(':checked'), 
                increase: +$('#autoplay_modal [name="increases"]').val(), 
                iswins: $('#autoplay_modal [name="iswins"]').is(':checked'), 
                wins: +$('#autoplay_modal [name="wins"]').val(), 
                numwins: 0 
                //iscashout: $('[name="cashout_switcher"][data-id="'+$id+'"]').is(':checked'), 
                //cashout: +$('[name="cashout_value"][data-id="'+$id+'"]').val() 
            } 
            console.log("AutoPlay :", $data); 
            $game.autoplay[($id-1)] = $data; 
            $('#autoplay_modal').hide(); 
            //$('.actions_field[data-id="'+$id+'"] .footer').addClass('active'); 
            $('.make_bet[data-src="'+$id+'"]').attr('disabled','disabled');
            $('.actions_field[data-id="'+$id+'"] .autoplay').html( LOCALIZATION.autobet_generic_stop + " ("+ $data.rounds +")").addClass('active').off().on('click', function(){
                var $self=$(this); 
                var $id=$self.data('id'); 
                $game.autostop({ id: $id });
            });
            // clear modal 
            $('#autoplay_modal .ranger input[type="text"]').val(0);
            $('#autoplay_modal .switchers input[type="checkbox"]').prop('checked', false);
            $('#autoplay_modal .rounds-wrap label input[type="radio"]').prop('checked', false)
        });
        // кнопки в менюхах
        $('.base_menu li').off().on('click', function(){
            var $self=$(this); 
            var $wrap=$self.parent(); 
            $('li', $wrap).removeClass('active'); 
            $self.addClass('active');
            var $auto_id = +$self.attr('data-id');
            if( $auto_id ){ $game.autostop({id:$auto_id}); }
        }); 
        // кнопки +- 
        $('.ranger button').off().on('click', function(){
            var $self=$(this); 
            var $dir=$self.data('dir'); 
            var $wrap=$self.parent();
            var $input = $('input:text', $wrap);
            var $val = +$input.val(); 
            var $res = $dir == "plus" ? ($val + 0.5) : ($val - 0.5); 
            $res = $res < 0.5 ? 0.5 : ( $res > 100 ? 100 : $res ); 
            $input.val($res); 
        }); 
        // изменение ставки кнопками +- 
        $('#actions_wrapper .actions_field .ranger button').off().on('click', function(){
            var $self=$(this); 
            var $dir=$self.data('dir'); 
            var $wrap=$self.parent();
            var $card=$wrap.parent().parent(); 
            var $input = $('input:text', $wrap);
            var $val = +$input.val(); 
            var $res = $dir == "plus" ? ($val + 0.5) : ($val - 0.5); 
            $res = $res < 0.5 ? 0.5 : ( $res > 100 ? 100 : $res ); 
            $input.val($res);
            $('[data-rel="current_bet"]', $card).val( $res ).html( $res );
        }); 
        // изменение ставки вводом
        $('#actions_wrapper .actions_field .ranger input').off().on('keyup', function(){
            var $self=$(this); 
            var $wrap=$self.parent();
            var $card=$wrap.parent().parent().parent(); 
            var $val = +$self.val(); 
            $val = $val < 0.5 ? 0.5 : ( $val > $game.max_bet ? $game.max_bet : $val ); 
            $self.val( $val ); 
            $('[data-rel="current_bet"]', $card).val( $val ).html( $val );
        }); 
        // изменение ставки кнопками с ценой
        $('#actions_wrapper .actions_field .fast_bet').off().on('click', function(){
            var $self=$(this); 
            var $wrap=$self.parent().parent().parent(); 
            var $val = parseFloat( $self.text() ); 
            var $cur = parseFloat( $('input:text', $wrap).val() ); 
            //if( $cur < $val || $cur % $val ){ $val = $val; } 
            //else { $val = $cur + $val; } 
            if( $self.attr('active') ){ $val = $cur + $val; } 
            $('.fast_bet').removeAttr('active');
            $self.attr('active', 1); 
            $val = $val < 0.5 ? 0.5 : ( $val > $game.max_bet ? $game.max_bet : $val ); 
            $('input:text', $wrap).val( $val );
            $('[data-rel="current_bet"]', $wrap).val( $val ).html( $val );
        }); 
        // включение автовывода
        $('#actions_wrapper .actions_field .auto_out_switcher input').off().on('change', function(){
            var $self=$(this); 
            var $checked = $self.is(':checked'); 
            var $wrap=$self.parent().parent();
            var $input = $('input:text', $wrap); 
        });
        // сделать ставку 
        console.log("Setting up make_bet click handlers");
        $('#actions_wrapper .make_bet').off().on('click', function(){
            console.log("make_bet click handler triggered");
            var $self=$(this); 
            var $id = parseInt( $self.attr('data-id') ); 
            var $src = parseInt( $self.attr('data-src') ); 
            var $wrap = $self.parent().parent(); 
            var $bet = parseFloat( $('input:text', $wrap).val() );
            console.log("Button clicked - ID:", $id, "SRC:", $src, "BET:", $bet, "Status:", $game.status); 
            switch( $game.status ){
                case "flight": 
                    if( $id ){ 
                        $('span', $self).html(LOCALIZATION.make_bet_generic_bet); 
                        $self.removeClass('danger').removeClass('warning');
                        $('h3', $self).hide(); 
                        $('h2', $self).css('display','flex'); 
                        $game.bet_complete({ id:$id, cf:parseFloat( $game.cur_cf ), type:'manual', src:$src }); 
                        $game.modal({ cf:parseFloat( $game.cur_cf ), result:( $bet * parseFloat( $game.cur_cf ) ), bet:$bet });
                        $self.attr('data-id', 0);
                    } 
                    else {
                        // Размещаем ставку на следующий раунд
                        console.log("Placing bet for next round - Button", $src, "Bet:", $bet);
                        $game.user_bets[ $src-1 ] = $bet; 
                        $game.bet_add({ type:"manual", src:$src, bet:$bet });
                        $self.removeClass('warning').removeClass('danger'); 
                        $('span', $self ).html(LOCALIZATION.make_bet_generic_bet);
                        $('h2', $self).css('display','flex'); 
                        $('h3', $self).css('display','flex'); // Показываем "Следующий раунд"
                        $('.actions_field[data-id="'+$src+'"]').addClass('disabled'); 
                        $('.actions_field[data-id="'+$src+'"] .fast_bet').attr('disabled', "disabled"); 
                        $('.actions_field[data-id="'+$src+'"] .autoplay').attr('disabled','disabled');
                    }
                    break; 
                case "finish": 
                    break; 
                case "loading": 
                    if( $id ){ 
                        $game.bet_edit({ id:$id, src:$src, status:5 });
                        $self.removeClass('danger').removeClass('warning'); 
                        $game.user_bets[ $src-1 ] = 0; 
                        $self.attr('data-id', 0);
                        $('span', $self ).html(LOCALIZATION.make_bet_generic_bet);
                        $('h2', $self).css('display','flex'); 
                        $('h3', $self).hide();  
                        $('.actions_field[data-id="'+$src+'"]').removeClass('disabled'); 
                        $('.actions_field[data-id="'+$src+'"] .fast_bet').attr('disabled', "disabled"); 
                        $('.actions_field[data-id="'+$src+'"] .autoplay').attr('disabled','disabled');
                    }
                    else { 
                        $game.user_bets[ $src-1 ] = $bet; 
                        $game.bet_add({ type:"manual", src:$src, bet:$bet });
                        $self.addClass('danger').removeClass('warning'); 
                        $('span', $self ).html(LOCALIZATION.make_bet_generic_bet);
                        $('h2', $self).css('display','flex'); 
                        $('h3', $self).hide(); 
                        $('.actions_field[data-id="'+$src+'"]').addClass('disabled'); 
                        $('.actions_field[data-id="'+$src+'"] .fast_bet').attr('disabled', "disabled"); 
                        $('.actions_field[data-id="'+$src+'"] .autoplay').attr('disabled','disabled');
                    }
                    break;
            }
        }); 
    }
    update( obj ){
        var $timer = new Date().getTime(); 
        var $delta = $timer - this.timer;// + this.new_delta; 
        var $change = false; 
        //console.log( $delta );
        if( $delta >= SETTINGS.timers[ this.status ] ){
            $change = true; 
            this.timer = $timer; 
            this.new_delta = 0;
        }
        switch( this.status ){
            case "loading": 
                if( $change ){ 
                    //this.loading_to_flying({ cf:this.win_cf, delta:SETTINGS.timers.flight });
                } 
                else { 
                    var $freq = +$('#loading_level .progresser').data('freq'); 
                    $freq = $freq - ( $delta / ( +SETTINGS.timers.loading / 100 ) );
                    $freq = $freq < 1 ? 1 : $freq; 
                    $('#loading_level .progresser').css('width', $freq+"%").attr('data-freq',$freq); 
                    this.bet_generic();
                }
                break;
            case "flight":
                if( this.cur_cf >= this.win_cf ){ 
                    this.flying_to_finish({ cf:this.win_cf, delta:SETTINGS.timers.flight }); 
                    // BUTTONS - remove this global update that affects all buttons
                    // $('.make_bet span').html(LOCALIZATION.make_bet_generic_cancel); 
                    // $('.make_bet h3').css('display','flex'); 
                    // $('.make_bet h2').hide(); 
                    // $('.make_bet').addClass('danger').removeClass('warning').attr('data-id', 0); 
                } 
                else { 
                    this.cur_cf = 1 + 0.5 * ( Math.exp( ( $delta / 1000 )  / 5 ) - 1 );
                    if( this.cur_cf >= 2 ){ $('#process_level .current').attr('data-amount',2); }  
                    if( this.cur_cf >= 4 ){ $('#process_level .current').attr('data-amount',3); }
                    $('#process_level .current').html( this.cur_cf.toFixed(2)+"x"); 
                    this.autocheck(); 
                    var $total_wins = 0; 
                    for( var $u of this.current_bets ){
                        if( this.cur_cf >= $u.cf ){ 
                            $u.win = true; 
                            var $line = $('#current_bets_list ul li[data-uid="'+ $u.uid +'"]'); 
                            if( !$line.hasClass('active') ){
                                $line.addClass('active'); 
                                $('.betx', $line).html( ( +$u.cf ).toFixed(2) ).addClass( +$u.cf > 6 ? 'high' : ( +$u.cf > 2 ? 'mid' : '' ) );
                                $('.win', $line).html( ( +$u.cf * +$u.amount ).toFixed(2) ); 
                            }
                            $total_wins += parseFloat( +$u.cf * +$u.amount ); 
                        }
                    } 
                    $('#actions_wrapper .make_bet.warning').each(function(){ 
                        var $self=$(this); 
                        var $bet_id = parseInt( $self.attr('data-id') ); 
                        if( $bet_id ){
                            var $src = parseInt( $self.attr('data-src') );
                            var $wrap=$self.parent().parent().parent().parent(); 
                            var $bet = parseFloat( $('input[type="text"]', $wrap).val() ); 
                            var $cf = parseFloat( $game.cur_cf ); 
                            var $result = ( $bet * $cf ).toFixed(2); 
                            var $cash_out = parseFloat( $('[name="cashout_value"]', $wrap).val() );
                            $('h2 [data-rel="current_bet"]', $self).html( $result ); 
                            if( $('[name="cashout_switcher"]', $wrap).is(':checked') ){ 
                                if( $cash_out <= $cf ){ $self.click(); }
                            } 
                        }
                    });
                    $('#bets_wrapper .info_window [data-rel="bets"] .label').html( ( $total_wins * this.factor ).toFixed(2) ); 
                    var $players = $('#current_bets_list ul li').length; 
                    var $winners = $('#current_bets_list ul li.active').length ; 
                    var $perc = $winners / ( $players / 100 )
                    $('#bets_wrapper .info_window [data-rel="bets"] .cur').html( $winners*this.factor ); 
                    $('#bets_wrapper .progresser').css('width', $perc+'%');
                }
                break; 
            case "finish": 
                if( $change ){ 
                    //this.finish_to_loading({ cf:this.win_cf, delta:SETTINGS.timers.flight }); 
                }
                break; 
        }
    }
    autostart(){
        for( var $auto of this.autoplay ){ 
            var $play = false; 
            if( +$auto.id ){ 
                // проверяем не упали ли ниже плинтуса 
                if( $auto.isisdecrease ){
                    if( $auto.decrease < $user.balance ){ $play = true; } 
                    else { $play = false; this.autostop({id:$auto.id}); }
                }
                // проверяем не переработали ли 
                if( $auto.isincrease ){
                    if( $auto.increase > $user.balance ){ $play = true; } 
                    else { $play = false; this.autostop({id:$auto.id}); }
                } 
                // проверяем общий выигрыш 
                if( $auto.iswins ){
                    if( $auto.wins > $auto.numwins ){ $play = true; }
                    else { $play = false; this.autostop({id:$auto.id}); }
                }
                // проверяем не кончились ли попытки
                if( $auto.rounds > $auto.numrounds ){ $play = true; } 
                else { $play = false; this.autostop({id:$auto.id}); }
                // 
                if( $play ){ 
                    var $wrap = $('.actions_field[data-id="'+$auto.id+'"]');
                    var $bet = parseFloat( $('.ranger input[type="text"]', $wrap).val() ); 
                    var $is_cash_out = $('[name="cashout_switcher"]', $wrap).is(':checked');
                    var $cash_out = parseFloat( $('[name="cashout_value"]', $wrap).val() ); 
                    if( $bet && $is_cash_out && $cash_out ){
                        // генерим ставку
                        this.bet_add({ type: "auto", src: $auto.id, bet: $bet }); 
                        $('.autoplay[data-id="'+$auto.id+'"]').html(LOCALIZATION.autobet_generic_stop+' ('+( $auto.rounds - $auto.numrounds )+')'); 
                        $auto.numrounds += 1; 
                        console.log("Make auto bet: ",{ type: "auto", src: $auto.id, bet: $bet });
                    } 
                    else {
                        // игра пропускается, готовы не все данные
                        console.log("Wait data for bet for autoplay "+$auto.id);
                    }
                }
            } 
        }
    } 
    autocheck(){
        for( var $auto of this.autoplay ){ 
            if( +$auto.bet ){
                var $wrap = $('.actions_field[data-id="'+ $auto.id +'"]'); 
                var $bet = parseFloat( $('.ranger input[type="text"]', $wrap).val() ); 
                var $cash_out = $('[name="cashout_switcher"]', $wrap).is(':checked'); 
                var $out_cf = parseFloat( $('[name="cashout_value"]', $wrap).val() ); 
                var $cf = parseFloat( this.cur_cf ); 
                var $result = $bet * $cf;
                if( $out_cf <= $cf ){
                    this.bet_complete({ id:$auto.bet, cf:$cf, type:'auto', src:$auto.id }); 
                    $('.autoplay[data-id="'+$auto.id+'"]').html(LOCALIZATION.autobet_generic_stop+' ('+( $auto.rounds - $auto.numrounds )+')'); 
                    this.autoplay[ (+$auto.id-1) ].bet = 0; 
                    this.autoplay[ (+$auto.id-1) ].numwins += $result; 
                    this.modal({ cf:$cf, result:$result }); 
                }
            }
        }
    }
    autostop( $data ){
        var $id = +$data.id; 
        if( $id ){
            this.bet_edit({ id:( this.autoplay[ ($id-1) ].bet ), src: $id, status: 5 });  
            this.autoplay[ ($id-1) ] = {
                id: 0, 
                bet: 0, 
                rounds: 0, 
                numrounds: 0, 
                isisdecrease: 0, 
                decrease: 0, 
                isincrease:0, 
                increase: 0, 
                iswins: 0, 
                wins: 0, 
                numwins: 0 
            } 
            $('.autoplay[data-id="'+$id+'"]').html(LOCALIZATION.autobet_generic_autoplay).removeClass('active').off().on('click', function(){
                $('#autoplay_modal').css('display', 'flex').attr('data-id', $(this).data('id'));
            });
            //$('.actions_field[data-id="'+$id+'"]').removeClass('disabled');
            $('make_bet[data-src="'+$id+'"]').removeAttr('disabled');
            //$('.actions_field[data-id="'+$id+'"] .fast_bet').removeAttr('disabled');
        }
    }
    
    // DEPRECATED
    game_create( $data ){
        $.ajax({
            url: "index.php?route=api/games/search", 
            type:"json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj.id ){ 
                    $game.win_cf = +$obj.amount; 
                    var $delta = ( 15 - +$obj.delta ) * 1000; ; 
                    $delta = $delta <= 0 ? 100 : $delta;
                    $game.timers.loading = $delta; 
                    $game.new_delta = 0; // +$obj.delta < 0 ? Math.abs(+$obj.delta) : 0; 
                }
            }
        });
    } 
    // DEPRECATED 
    game_start( $data ){
        $.ajax({
            url: "index.php?route=api/games/edit", 
            type:"json", 
            method: "post", 
            data:{ status:2 }, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj.success ){ 

                }
            }
        });
    } 
    // DEPRECATED
    game_close( $data ){
        $.ajax({
            url: "index.php?route=api/games/close", 
            type:"json", 
            method: "post", 
            data:{ status:7 }, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj.success ){ }
            }
        });
    }
    
    clear_level( $data ){
        var $cf = $data.cf ? +$data.cf : +this.win_cf; 
        $('#last_cf').html( $cf+'x').removeClass('low').removeClass('mid').removeClass('high'); 
        $('#last_cf').addClass( $cf >= 5 ? 'high' : ( $cf >= 2 ? 'mid' : 'low' ) ); 
        var $wrap = $('#previous_bets_list ul');
        $wrap.html(``); 
        if( this.current_bets && this.current_bets.length ){ 
            for( var $u of this.current_bets ){
                var $tmps = `<li data-uid="${ $u.uid }" class="${ $cf >= $u.cf ? 'active' : '' }"> 
                                    <div class="user"><img src="res/img/users/av-${ $u.img }.png" alt=""><span>${ $u.name }</span></div> 
                                    <div class="bet">${ ( $u.amount ).toFixed(2) }</div> 
                                    <div class="betx">${ ( $u.cf ).toFixed(2) }</div> 
                                    <div class="win">${ $cf >= $u.cf ? ( ( $u.cf * $u.amount ).toFixed(2) ) : 0 }</div> 
                                </li>`; 
                $wrap.append( $tmps ); 
            }
        } 
        this.get_bets({ user:$user.uid, sort:'id', dir:'desc' });
    } 
    bet_add( $data ){
        $.ajax({
            url: "index.php?route=api/bets/add", 
            type: "json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                console.log("Bet add response:", $obj, "for src:", $data.src);
                if( $obj.success ){ 
                    $game.user_bets[ $data.src-1 ] = +$obj.success; 
                    console.log("Updated user_bets:", $game.user_bets, "src:", $data.src);
                    if( $data['type'] == "manual" ){ 
                        $('.make_bet[data-src="'+ $data.src +'"]').attr('data-id', $obj.success); 
                        console.log("Set data-id", $obj.success, "for button with data-src:", $data.src);
                    } 
                    else {
                        $game.autoplay[ ($data.src-1) ].bet = +$obj.success;
                        //$('.auto_out_switcher input[data-src="'+ $data.src +'"]').attr('data-id', $obj.success); 
                    }
                } 
                if( $obj.balance ){
                    var $balance = parseFloat( $obj.balance ); 
                    $('[data-rel="balance"]').val( $balance ).html( $balance );
                } 
                if( $obj.error ){
                    if( $data.type == "manual" ){
                        var $btn = $('.make_bet[data-src="'+$data.src+'"]');
                        $btn.removeClass('danger').removeClass('warning'); 
                        $game.user_bets[ $data.src-1 ] = 0; 
                        $btn.attr('data-id', 0);
                        $('span', $btn ).html(LOCALIZATION.make_bet_generic_bet);
                        $('h2', $btn).css('display','flex'); 
                        $('h3', $btn).hide();  
                        $('.actions_field[data-id="'+$data.src+'"]').removeClass('disabled'); 
                        $('.actions_field[data-id="'+$data.src+'"] .fast_bet').attr('disabled', "disabled"); 
                        $('.actions_field[data-id="'+$data.src+'"] .autoplay').attr('disabled','disabled'); 
                    } 
                    if( $data.type == "auto" ){
                        $('.actions_field[data-id="'+$data.src+'"] .autoplay').click(); 
                    }
                }
            }
        });
    }
    bet_edit( $data ){ 
        $.ajax({
            url: "index.php?route=api/bets/edit", 
            type: "json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj.success ){ 
                    $game.user_bets[ $data.src-1 ] = 0; 
                    if( $data['type'] == "manual" ){ $('.make_bet[data-src="'+ $data.src +'"]').attr('data-id', 0); } 
                    else { $('.auto_out_switcher input[data-src="'+ $data.src +'"]').attr('data-id', 0); }
                }
                if( $obj.balance ){
                    var $balance = parseFloat( $obj.balance ); 
                    $('[data-rel="balance"]').val( $balance ).html( $balance );
                }
            }
        });
    }
    bet_complete( $data ){ 
        $.ajax({
            url: "index.php?route=api/bets/close", 
            type: "json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj.success ){ 
                    $game.user_bets[ $data.src-1 ] = 0; 
                    if( $data['type'] == "manual" ){ $('.make_bet[data-src="'+ $data.src +'"]').attr('data-id', 0); } 
                    else { $('.auto_out_switcher input[data-src="'+ $data.src +'"]').attr('data-id', 0); }
                } 
                if( $obj.balance ){
                    var $balance = parseFloat( $obj.balance ); 
                    $('[data-rel="balance"]').val( $balance ).html( $balance );
                }
                $game.get_bets({ user:$user.uid, sort:'id', dir:'desc' });
            }
        });
    }
    bet_generic( $data ){
        if( $users && $users.length ){ 
            var $bets = [0.5, 2, 5, 10, 50];
            for( var $u of $users ){
                if( ( Math.random() * 100 >= this.generic_chanse ) && $u.name ){ 
                    var $add = true; 
                    for( var $v of this.current_bets ){ 
                        if( $v.uid == $u.uid ){ 
                            //$add = false; 
                            break; 
                        }
                    }
                    if( $add ){ 
                        var $amount = $bets[ Math.round( Math.random()*($bets.length-1) ) ];
                        var $cf = parseFloat( ( Math.random() * 1000 / 100 ).toFixed(2) ); 
                        $cf = $cf < 1 ? $cf+1 : $cf; 
                        var $tmps = `<li data-uid="${ $u.uid }"> 
                                        <div class="user"><img src="res/img/users/av-${ $u.img }.png" alt=""><span>${ $u.name }</span></div> 
                                        <div class="bet">${ $amount }</div> 
                                        <div class="betx"></div> 
                                        <div class="win"></div> 
                                    </li>`; 
                        $('#current_bets_list ul').append( $tmps ); 
                        this.current_bets.push({ uid:$u.uid, name:$u.name, amount:$amount, cf:$cf, img:$u.img, win:false }); 
                        $('#game_bets .label').html( this.current_bets.length*this.factor ); 
                        $('#bets_wrapper .info_window [data-rel="bets"] .cur').html( this.current_bets.length*this.factor );
                        $('#bets_wrapper .info_window [data-rel="bets"] .total').html( this.current_bets.length*this.factor );
                    }
                }
            }
        }
    }
    get_history( $data ){ 
        $.ajax({
            url: "index.php?route=api/games/history", 
            type:"json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj && $obj.length ){ 
                    var $wrap = $('#history_wrapper .wrapper .inner'); 
                    $wrap.html(``);
                    for( var $h of $obj ){
                        var $amount = +$h.amount; 
                        var $tmps = `<span class="${ $h.amount >= 5 ? 'high' : ( $h.amount >= 2 ? 'mid' : 'low' ) }">${ $h.amount }</span>`; 
                        $wrap.append($tmps);
                    }
                }
            }
        });
    } 
    get_bets( $data ){ 
        $.ajax({
            url: "index.php?route=api/bets/load", 
            type: "json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $obj = typeof $r == "string" ? eval('('+$r+')') : $r; 
                if( $obj && $obj.length ){ 
                     var $wrap = $('#my_bets_list ul'); 
                     $wrap.html(``); 
                     for( var $u of $obj ){
                        var $tmps = `<li data-uid="${ $u.user }" class="${ +$u.result ? 'active' : '' }"> 
                                        <div class="user"><img src="res/img/users/av-${ $u.img }.png" alt=""><span>${ $user.real_name }</span></div> 
                                        <div class="bet">${ $u.bet }</div> 
                                        <div class="betx ${ +$u.cf >= 5 ? 'high' : ( +$u.cf >= 2 ? 'mid' : 'low' ) }">${ $u.cf }</div> 
                                        <div class="win">${ +$u.result ? $u.result : "-"+$u.bet }</div> 
                                    </li>`; 
                        $wrap.append( $tmps ); 
                     }
                } 
                if( $obj.balance ){
                    var $balance = parseFloat( $obj.balance ); 
                    $('[data-rel="balance"]').val( $balance ).html( $balance );
                }
            }
        }); 
    }
    balance( $data ){ 
        $.ajax({
            url: "index.php?route=api/users/balance", 
            type: "json", 
            method: "post", 
            data: $data, 
            error: function($e){ console.error($e); },
            success: function($r){
                var $balance = parseFloat( $r ); 
                if( $balance ){ 
                    $('[data-rel="balance"]').val( $balance ).html( $balance );
                }
            }
        }); 
    }
    modal( $data ){
        var $wrap = $('#modal_wrapper'); 
        var $cf = $data.cf ? ( parseFloat( $data.cf ) ).toFixed(2) : 0; 
        $('.multiplier .value', $wrap).html( $cf+"x");
        var $result = $data.result ? ( parseFloat( $data.result ) ).toFixed(2) : 0; 
        $('.win .value', $wrap).html( $result );
        $wrap.addClass('active'); 
        SOUNDS.sounds.play('win'); 
        setTimeout(function(){ $('#modal_wrapper').removeClass('active'); }, 3000);
    } 

    // SOCKET FUNC
    loading_to_flying( $data ){ 
        console.log("Data to flight: ", $data);
        this.status = "flight"; 
        SETTINGS.timers.flight = $data.delta; 
        this.timer = new Date().getTime(); 
        this.win_cf = $data.cf; 
        this.cur_cf = 1; 
        $plane.status = "move"; 
        $plane.pos = 0;  
        $plane.x = SETTINGS.start.x; 
        $plane.y = SETTINGS.start.y; 
        $('.make_bet').each(function(){
            var $self = $(this);  
            var $src = $self.attr('data-src');
            var $id = +$self.attr('data-id'); 
            console.log("Loading to flying - Button src:", $src, "id:", $id);
            if( $id ){
                $self.removeClass('danger').addClass('warning'); 
                $('span', $self).html(LOCALIZATION.make_bet_generic_cashout).css('display', 'flex');
                $('h2', $self).css('display', 'flex');
                $('h3', $self).hide();
                console.log("Button", $src, "set to cashout mode");
            } 
            else {
                // Кнопка без ставки остается в режиме "сделать ставку" - не изменяем ее состояние
                console.log("Button", $src, "has no bet - keeping in bet mode");
            }
        }); 
        $('#loading_level').css('display','none'); 
        $('#process_level').css('display', 'flex'); 
        $('#complete_level').css('display', 'none'); 
        $('#bets_wrapper .progresser').css('width', '100%').data('freq', '100'); 
        $('#actions_wrapper .actions_field').addClass('disabled'); 
        $('#actions_wrapper .actions_field .fast_bet').attr('disabled', "disabled"); 
        $('.autoplay').attr('disabled','disabled');
        this.autostart();  
        if( SETTINGS.volume.active ){ SOUNDS.sounds.play('start'); }
    }
    flying_to_finish( $data ){ 
        console.log("Data to finish: ", $data); 
        this.status = "finish"; 
        SETTINGS.timers.finish = $data.delta; 
        this.timer = new Date().getTime(); 
        this.cur_cf = this.win_cf;
        $plane.trace = false; 
        $plane.pos = 5; 
        this.clear_level({ cf: this.win_cf }); 
        $('#loading_level .progresser').css('width','100%').attr('data-freq', '100');
        $('#process_level .current').attr('data-amount',1);
        // close bets 
        this.user_bets = [ 0, 0 ]; 
        //this.game_close({}); 
        $('#loading_level').css('display','none'); 
        $('#process_level').css('display', 'none');
        $('#complete_level').css('display', 'flex');
        $('#complete_level .result').html( this.cur_cf+"x"); 
        $('#actions_wrapper .make_bet').each(function(){ 
            var $self=$(this); 
            var $wrap=$self.parent().parent(); 
            var $bet = parseFloat( $('input[type="text"]', $wrap).val() );  
            $('h2 [data-rel="current_bet"]', $self).html( $bet );
        }); 
        //$('#actions_wrapper .actions_field').each(function(){
        //    var $self=$(this); 
        //}); 
        $('#actions_wrapper .actions_field').removeClass('disabled'); 
        $('#actions_wrapper .actions_field .fast_bet').removeAttr('disabled'); 
        for( var $auto of this.autoplay ){
            if( $auto.id ){
                $('.autoplay[data-id="'+$auto.id+'"]').html(LOCALIZATION.autobet_generic_stop +' ('+( $auto.rounds - $auto.numrounds )+')'); 
            }
        }
        $('.make_bet span').html(LOCALIZATION.make_bet_generic_bet); 
        $('.make_bet h3').hide(); 
        $('.make_bet h2').css('display','flex'); 
        $('.make_bet').removeClass('danger').removeClass('warning').attr('data-id', 0); 
        $('.autoplay').removeAttr('disabled');
        setTimeout( $game.balance, 1000 ); 
        if( SETTINGS.volume.active ){ SOUNDS.sounds.play('away'); } 
        //this.get_history({}); 
        this.get_bets({ user:$user.uid, sort:'id', dir:'desc' });
        this.balance(); 
    } 
    finish_to_loading( $data ){ 
        console.log("Data to loading: ", $data);
        this.status = "loading"; 
        this.timer = new Date().getTime(); 
        SETTINGS.timers.loading = $data.delta; 
        this.win_cf = $data.cf; 
        this.cur_cf = 1; 
        $plane.status = "idle"; 
        $plane.pos = 0; 
        $plane.trace = true; 
        $plane.x = SETTINGS.start.x; 
        $plane.y = SETTINGS.start.y; 
        $('#loading_level').css('display','flex'); 
        $('#process_level').css('display', 'none');
        $('#complete_level').css('display', 'none');
        this.current_bets = []; 
        $('#current_bets_list ul').html('');
        $('#game_bets .label').html( 0 );
        $('#bets_wrapper .info_window [data-rel="bets"] .label').html( 0 );
        $('#bets_wrapper .info_window [data-rel="bets"] .cur').html( 0 );
        $('#bets_wrapper .info_window [data-rel="bets"] .total').html( 0 ); 
        // BUTTONS - Reset each button individually
        $('.make_bet').each(function(){
            var $btn = $(this);
            var $src = $btn.attr('data-src');
            $('span', $btn).html(LOCALIZATION.make_bet_generic_bet); 
            $('h3', $btn).hide(); 
            $('h2', $btn).css('display','flex'); 
            $btn.removeClass('danger').removeClass('warning');
            // Only reset data-id if there's no active bet for this button
            if( !$game.user_bets[$src-1] ) {
                $btn.attr('data-id', 0); 
            }
        }); 
        //
        this.balance(); 
        this.get_bets({ user:$user.uid, sort:'id', dir:'desc' });
        this.get_history({});
    }
}

// Plane will be created in document.ready
var $plane;  

var $game = new Game({}); 

function render( obj ){
    $ctx.clearRect( 0, 0, SETTINGS.w, SETTINGS.h );
    
    // Debug: draw background to see canvas works
    $ctx.fillStyle = "#001122";
    $ctx.fillRect(0, 0, SETTINGS.w, SETTINGS.h);
    
    if( $game ){ $game.update({}); }
    if( $plane ){ 
        $plane.update({});
        
        // Debug: draw plane position as red dot
        $ctx.fillStyle = "red";
        $ctx.fillRect($plane.x - 5, $plane.y - 5, 10, 10);
    }
    requestAnimationFrame( render );
}

function open_game(){ 
    $('#splash').addClass('show_modal');
    var $cur_settings = SETTINGS.volume.active ; 
    SETTINGS.volume.active = 0; 
    $('#splash button').off().on('click', function(){
        $('#splash').remove(); 
        if( $cur_settings ){ 
            SETTINGS.volume.active = $cur_settings; 
            SOUNDS.music.play(); 
            $('#sound_switcher').removeClass('off'); 
        }
        else {
            $('#sound_switcher').addClass('off'); 
        }
    }); 
} 
/*
$(window).on('resize', function(){
    SETTINGS.w = $('#canvas').width(); 
    SETTINGS.h = $('#canvas').height(); 
    $canvas = document.getElementById("canvas");
    $ctx = $canvas.getContext("2d");
    $canvas.width = SETTINGS.w; 
    $canvas.height = SETTINGS.h; 
});
*/

$(document).ready(function(){
    // window.$socket = new Socket(); 
    // $socket.init(); 
});
// Автоматически открыть игру через 3 секунды если сокет не подключился
setTimeout(function() {
    if ($('#splash').length > 0) {
        open_game();
    }
}, 3000);

var socket = io.connect('http://localhost:2345');    
socket.on('message', ( msg ) => { 
    console.log('New message: ', msg ); 
    var $obj = typeof msg == "string" ? eval('('+ msg +')') : msg; 
    console.log("Compiled: ", $obj); 
    if( $obj && $obj.msg && $obj.msg == "Change game state" ){
        var $data = { 
            state: $obj.game && $obj.game.state ? $obj.game.state : '', 
            cf: $obj.game && $obj.game.cf ? parseFloat( $obj.game.cf ).toFixed(2) : 1, 
            delta: $obj.game && $obj.game.delta ? parseInt( $obj.game.delta ) : 0 
        } 
        switch( $data.state ){
            case "loading": 
                $game.finish_to_loading( $data ); 
                break; 
            case "flying": 
                $game.loading_to_flying( $data );
                break; 
            case "finish": 
                $game.flying_to_finish( $data );
                break; 
        }
        // open_game(); // Remove this line that shows splash on every WebSocket message
    }
});

// Initialize the game when DOM is ready
$(document).ready(function() {
    console.log("Game initialization started");
    
    // Fix canvas size after DOM is ready
    SETTINGS.w = document.querySelector('#game_field').offsetWidth;
    SETTINGS.h = document.querySelector('#game_field').offsetHeight;
    SETTINGS.start.y = SETTINGS.h - 50; // Position plane near bottom
    
    $canvas.width = SETTINGS.w; 
    $canvas.height = SETTINGS.h; 
    
    console.log("Canvas size:", SETTINGS.w, "x", SETTINGS.h);
    console.log("Plane start position:", SETTINGS.start);
    
    // Create plane with correct settings
    $plane = new Plane({ 
        ctx: $ctx, 
        x: SETTINGS.start.x, 
        y: SETTINGS.start.y, 
        w: SETTINGS.w*0.15, 
        h: SETTINGS.w*0.15/2, 
        chart: new Chart({ 
            ctx: $ctx, 
            sx: SETTINGS.start.x, 
            sy: SETTINGS.start.y, 
            ax: SETTINGS.start.x, 
            ay: SETTINGS.start.y, 
            fx: SETTINGS.start.x, 
            fy: SETTINGS.start.y 
        })
    });
    
    console.log("Plane created at:", $plane.x, $plane.y);
    
    // Start the render loop
    render();
    
    // Initialize game bindings
    console.log("Calling $game.bind()");
    $game.bind();
    console.log("$game.bind() completed");
    
    // Show the initial splash screen only once
    open_game();
    
    console.log("Game initialization completed");
});





