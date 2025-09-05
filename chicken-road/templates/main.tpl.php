<?php
    $_SESSION['user'] = Users::GI()->get([ 'uid'=>UID ]);
    include_once BASE_DIR ."common.php";  

    $cfs = Cfs::GI()->load(['full'=>1]);
?>
<script>
    window.CFS = eval('(<?= json_encode( $cfs ); ?>)');
    window.HOST_ID = '<?= HOST_ID; ?>';
</script>
<link rel="stylesheet" type="text/css" href="./res/css/style2.css?<?= rand(0, 99999); ?>">
<div id="main_wrapper"> 
    <header id="header">
        <a href="/" id="logo">
            <img src="./res/img/logo.svg" alt="">
            <img src="./res/img/logo_mobile.svg" alt="">
        </a>
        <div class="menu">
            <input type="checkbox" id="show_burger_menu" autocomplete="off">
            <button data-rel="menu-balance"> 
                <span><?= $_SESSION['user']['balance']; ?></span><svg width="18" height="18" viewBox="0 0 18 18" style="fill: rgb(255, 255, 255);"><use xlink:href="./res/img/currency.svg#USD"></use></svg>
            </button>
            <label id="sound_switcher" for="show_burger_menu">
                <svg class="burger" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.125 18.875H19.875M4.125 12.875H19.875M4.125 6.875H19.875" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </label>
            <div id="burger_menu">
                <div class="line">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="iconDiver">
                        <path d="M10.6663 5.66651C11.555 6.85185 11.555 9.14785 10.6663 10.3332M12.6663 3.33318C15.325 5.87185 15.341 10.1445 12.6663 12.6665M1.33301 9.97251V6.02651C1.33301 5.64385 1.63167 5.33318 1.99967 5.33318H4.39034C4.47856 5.33287 4.56581 5.31469 4.64681 5.27974C4.72782 5.2448 4.80091 5.19381 4.86167 5.12985L6.86167 2.87118C7.28167 2.43385 7.99967 2.74385 7.99967 3.36185V12.6378C7.99967 13.2605 7.27301 13.5678 6.85567 13.1218L4.86234 10.8758C4.8014 10.8101 4.72757 10.7575 4.64545 10.7215C4.56333 10.6855 4.47468 10.6668 4.38501 10.6665H1.99967C1.63167 10.6665 1.33301 10.3558 1.33301 9.97251Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>Sound</span>
                    <label class="switcher">
                        <input type="checkbox" autocomplete="off" id="switch_sound">
                        <div></div>
                    </label>
                </div>
                <div class="line">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="iconDiver">
                        <path d="M5.33301 11.9999V3.81054C5.33297 3.49201 5.44696 3.18399 5.65436 2.94223C5.86176 2.70048 6.14885 2.54095 6.46367 2.49254L12.4637 1.5692C12.6537 1.53998 12.8478 1.5522 13.0326 1.60501C13.2175 1.65782 13.3887 1.74998 13.5346 1.87517C13.6805 2.00036 13.7976 2.15562 13.8779 2.3303C13.9581 2.50498 13.9997 2.69496 13.9997 2.8872V10.6665" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M5.33301 5.99984L13.9997 4.6665" stroke="white"></path>
                        <path d="M5.33301 11.9997C5.33301 12.5301 5.12229 13.0388 4.74722 13.4139C4.37215 13.789 3.86344 13.9997 3.33301 13.9997C2.80257 13.9997 2.29387 13.789 1.91879 13.4139C1.54372 13.0388 1.33301 12.5301 1.33301 11.9997C1.33301 10.895 2.22834 10.6663 3.33301 10.6663C4.43767 10.6663 5.33301 10.895 5.33301 11.9997ZM13.9997 10.6663C13.9997 11.1968 13.789 11.7055 13.4139 12.0806C13.0388 12.4556 12.5301 12.6663 11.9997 12.6663C11.4692 12.6663 10.9605 12.4556 10.5855 12.0806C10.2104 11.7055 9.99967 11.1968 9.99967 10.6663C9.99967 9.56167 10.895 9.33301 11.9997 9.33301C13.1043 9.33301 13.9997 9.56167 13.9997 10.6663Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>Music</span>
                    <label class="switcher">
                        <input type="checkbox" autocomplete="off" id="switch_music">
                        <div></div>
                    </label>
                </div>
            </div>
        </div>
    </header>

    <main id="main"> 
        <div id="game_container"> 
            <!--canvas id="game_field"></canvas--> 
            <div id="battlefield"> </div>  
        </div>
        <div id="stats"> 
            <span><?= TEXT_LIVE_WINS; ?></span>
            <div><i></i></div>
            <span class="online"><?= TEXT_LIVE_WINS_ONLINE; ?>: 8768</span>
        </div>
        <div id="random_bet"></div>
    </main>

    <footer id="footer"> 
        <div id="bet_wrapper">
            <section id="values">
                <div class="bet_value_wrapper gray_input">
                    <button class="" data-rel="min"><?= TEXT_BETS_WRAPPER_MIN; ?></button>
                    <div class="resize"><input type="text" value="0.5" id="bet_size" style="width:100%;"></div>
                    <button class="" data-rel="max"><?= TEXT_BETS_WRAPPER_MAX; ?></button>
                </div>
                <div class="basic_radio">
                    <label class="gray_input">
                        <input type="radio" name="bet_value" value="0.5" autocomplete="off">
                        <span>0.5</span> 
                        <svg width="18" height="18" viewBox="0 0 18 18" style="fill: rgb(255, 255, 255);"><use xlink:href="./res/img/currency.svg#USD"></use></svg>
                    </label>
                    <label class="gray_input">
                        <input type="radio" name="bet_value" value="1" autocomplete="off">
                        <span>1</span> 
                        <svg width="18" height="18" viewBox="0 0 18 18" style="fill: rgb(255, 255, 255);"><use xlink:href="./res/img/currency.svg#USD"></use></svg>
                    </label>
                    <label class="gray_input">
                        <input type="radio" name="bet_value" value="2" autocomplete="off">
                        <span>2</span> 
                        <svg width="18" height="18" viewBox="0 0 18 18" style="fill: rgb(255, 255, 255);"><use xlink:href="./res/img/currency.svg#USD"></use></svg>
                    </label>
                    <label class="gray_input">
                        <input type="radio" name="bet_value" value="7" autocomplete="off">
                        <span>7</span> 
                        <svg width="18" height="18" viewBox="0 0 18 18" style="fill: rgb(255, 255, 255);"><use xlink:href="./res/img/currency.svg#USD"></use></svg>
                    </label>
                </div>
            </section>
            <section id="dificulity">
                <h2>
                    <?= TEXT_BETS_WRAPPER_DIFICULITY; ?>
                    <span><?= TEXT_BETS_WRAPPER_CHANCE; ?></span>
                </h2>
                <div class="radio_buttons">
                    <label>
                        <input type="radio" name="difficulity" value="easy" checked autocomplete="off">
                        <span><?= TEXT_BETS_WRAPPER_EASY; ?></span>
                    </label>
                    <label>
                        <input type="radio" name="difficulity" value="medium" autocomplete="off">
                        <span><?= TEXT_BETS_WRAPPER_MEDIUM; ?></span>
                    </label>
                    <label>
                        <input type="radio" name="difficulity" value="hard" autocomplete="off">
                        <span><?= TEXT_BETS_WRAPPER_HARD; ?></span>
                    </label>
                    <label>
                        <input type="radio" name="difficulity" value="hardcore" autocomplete="off">
                        <span><?= TEXT_BETS_WRAPPER_HARDCORE; ?></span>
                    </label>
                </div>
                <i></i>
            </section>
            <section id="buttons_wrapper"> 
                <button id="close_bet"><?= TEXT_BETS_WRAPPER_CASHOUT; ?><span>1.99 USD</span></button>
                <button id="start"><?= TEXT_BETS_WRAPPER_PLAY; ?></button>
            </section>
        </div>
    </footer>
</div>
<div id="win_modal"> 
    <div class="inner">
        <h2><?= TEXT_WIN_MODAL_WIN; ?>!</h2>
        <h3>x100.00</h3>
        <h4>+<span>10000</span> <svg width="25" height="25" viewBox="0 0 18 18" style="fill:#2bfd80;"><use xlink:href="./res/img/currency.svg#USD"></use></svg></h4>
    </div>
</div>
<div id="splash">
    <span id="loader"></span>
    <div class="disclaimer">
        <h4><img src="./res/img/icon-help.svg" alt=""></h4>
        <p><?= TEXT_ETRY_MODAL_MAIN; ?></p>
        <button><?= TEXT_ENTRY_MODAL_BTN_OK; ?></button>
    </div>
</div>
<div id="overlay"></div>
<script src="./res/js/game2.js?<?= rand(0, 99999); ?>"></script>