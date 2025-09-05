<div id="app" class="mines"> 
    <div id="modeSelectionModal" class="mode-selection-modal">
      	<div class="mode-selection-content">
			<h2>Selecciona un modo de juego</h2>
			<button class="mode-two-btn" data-mode="demo">Modo demostración</button>
			<button class="mode-two-btn" data-mode="real">El juego de siempre</button>
      	</div>
    </div>
            
    <div id="post-message-size" class="game-wrapper demo">
        <div class="game-header">
            <div class="game-header__logo"></div>
            <div class="game-header__balance-title">Saldo:</div>
            <div class="game-header__balance">
                <div class="game-header__balance-text" id="total_account" style="display:flex; font-weight:700;">
                    <div class="summ-balance" id="balance"><?= htmlspecialchars( SYS_BALANCE ); ?></div>
                    <div class="summ-balance" id="currency"><?= htmlspecialchars( SYS_CURRENCY ); ?></div>
                </div>
                <div class="game-header__balance-delta">-1000</div>
            </div>
            
            <?php if( 2 == 3 ){ ?>
                <div class="game-header__buttons">
                    <div class="game-header__button">
                         <div class="game-header__button--inner undefined"><i class="iconFont iconFont-sound"></i></div>
                    </div>
                    <div class="game-header__button">
                        <div class="game-header__button--inner undefined"><i class="iconFont iconFont-settings"></i></div>
                    </div>
                </div> 
            <?php } ?>
            
            <div class="settings modal-light">
                <div class="settings__inner">
                    <div class="settings__close"><i class="iconFont iconFont-close"></i></div>
                    <div class="settings__title">Ajustes</div>
                    <div class="settings__content">
                    	<div class="settings-input">
                        	<div class="settings-input__inner">
                        		<label for="name">Apodo</label>
                        		<input autocomplete="off" type="text">
                        	</div>
                      	</div>
                      	<div class="settings__link">
                        	<div class="settings__icon"><i class="iconFont iconFont-limits"></i></div>
                        	<div class="settings__name settings__item-name">Límites</div>
                    	</div>
                      	<div class="settings__link">
                        	<div class="settings__icon"><i class="iconFont iconFont-rules"></i></div>
                        	<div class="settings__name settings__item-name">Reglamento</div>
                      	</div>
                      	<span class="settings__item">
                        	<div class="settings__item-icon settings__icon"><i class="iconFont iconFont-sound"></i></div>
                        	<div class="settings__range">
                        		<div class="range-slider _sound">
                        			<input type="range" min="0" max="1" step="0.1" class="range-slider__input">
                            		<div class="range-slider__bg">
                              			<div class="range-slider__bg-inner"></div>
                              			<div style="transform: translateX(100%);" class="range-slider__active"></div>
                            		</div>
                            		<div style="transform: translateX(0%);" class="range-slider__btn-wrap">
                              			<div style="transform: translateX(0%);" class="range-slider__btn"></div>
                            		</div>
                          		</div>
                        	</div>
                      	</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="game-container">
            <!-- COEFFICIENT FIELD -->
            <div class="game-history">
				<div class="game-history__inner">
					<div class="game-history__inner-container" id="coeffs_list"> </div>
				</div>
            </div>
			<!-- SELECT GAME MODE BUTTONS -->
			<div class="game-mode-tabs" id="game-mode-tabs">
				<div data-track="manual" class="mode-btn active">
					<div class="mode-btn__inner">
						<div class="mode-btn__text">Manuel</div>
						<div class="mode-btn__indicator"></div>
					</div>
				</div>
				<div data-track="auto" class="mode-btn">
					<div class="mode-btn__inner">
						<div class="mode-btn__text">Auto</div>
						<div class="mode-btn__indicator"></div>
					</div>
				</div>
			</div>
            <!-- GAME FIELD -->
            <div class="chart-wrapper">
                <div class="table-holder" style="position:relative;">
                    <div class="game-tiles" id="game_field"> </div>
                    <style>
						#win_modal{ display: none; width: 100%; height: 100%; position: absolute; inset: 0; background: transparent; display: flex; flex-flow: row nowrap; justify-content: center; align-items: center; gap: 0; padding: 0px; margin: 0px; z-index: 1000; }
						#modal_body { width: 250px; height: 200px; background: transparent url('./mines/img/win-screen-bg.png') center center no-repeat; background-size: 100% auto; padding: 0px; margin: -30px 0px 0px 0px; backdrop-filter: blur(2px); }
						#modal_body h3{ height: 70px; color: #fff; font-size: 24px; font-weight: 700; display: flex; flex-flow: row nowrap; justify-content: center; align-items: center; gap: 0; }
						#modal_result{ color: #b3edfe; font-weight: 700; font-size: 24px; text-align: center; padding-bottom: 20px; margin: 25px 20px 15px 20px; border-bottom: solid 1px #b3edfe; }
						#modal_coeff { display: flex; flex-flow: row nowrap; justify-content: stretch; align-items: center; gap: 0; padding: 0px; margin: 20px 20px 0px 20px; }
						#modal_coeff span { flex-grow: 1; flex-shrink: 1; font-size: 10px; color: #fff; }
						#modal_coeff strong { flex-grow: 0; flex-shrink: 0; font-size: 12px; font-weight: 700; color: #fff; }
                    </style>
                    <div id="win_modal">
                    	<div id="modal_body">
                        	<h3>¡Tú ganas!</h3>
                        	<div id="modal_result">$100</div>
                    		<div id="modal_coeff">
                          		<span>Multiplicador</span>
                          		<strong>1.1</strong>
                    		</div>
                    	</div>
                    </div>
                </div>
            </div>
            <!-- MANUAL BET SETUP -->
            <div class="game-betslip">
                <div class="input-text__wrapper" id="setup_bet_wrapper">
                    <div data-track="min" class="button _sm">
                    	<div class="button__inner">
                        	<div class="button__text">min</div>
                      	</div>
                    </div>
                    <div data-track="sub" class="button _sm">
                    	<div class="button__inner">
                        	<div class="button__text">-</div>
                      	</div>
                    </div>
                    <div data-track="add" class="button _sm">
                      	<div class="button__inner">
                        	<div class="button__text">+</div>
                      	</div>
                    </div>
                    <div data-track="max" class="button _sm">
                      	<div class="button__inner">
                        	<div class="button__text">max</div>
                      	</div>
                    </div>
                    <div class="games-input__wrapper">
                    	<label for="d5792516-2adb-43c4-8454-ac3359b63b37">Monto apuesta
                    		<span class="tooltip-box">i<span class="tooltip-hint"> </span></span>
                    	</label>
                      	<input type="text" inputmode="decimal" id="current_bet" autocomplete="off" spellcheck="false" tabindex="-1" class="games-input__number" value="">
                    </div>
                </div>
                <!-- BAD CELLS SETUP -->
                <div class="settings-input__wrapper _mines">
                    <div class="settings-input__wrapper-inner" id="num_lose_fields">
                      	<div data-track="3" class="button _sm">
                        	<div class="button__inner">
                          		<div class="button__text">3</div>
                        	</div>
                      	</div>
                      	<div data-track="5" class="button _sm">
                        	<div class="button__inner">
                          		<div class="button__text">5</div>
                        	</div>
                      	</div>
                      	<div data-track="10" class="button _sm">
                        	<div class="button__inner">
                          		<div class="button__text">10</div>
                        	</div>
                      	</div>
                      	<div data-track="20" class="button _sm">
                        	<div class="button__inner">
                          		<div class="button__text">20</div>
                        	</div>
                      	</div>
                      	<div data-track="sub" class="button _sm _golden">
                        	<div class="button__inner">
                          		<div class="button__text">-</div>
                        	</div>
                      	</div>
                      	<div data-track="add" class="button _sm _golden">
                        	<div class="button__inner">
                          		<div class="button__text">+</div>
                        	</div>
                      	</div>
                      	<div class="games-input__wrapper">
                        	<label for="mines">Minas</label>
                        	<input id="mines" name="mines" type="text" autocomplete="off" spellcheck="false" tabindex="-1" class="games-input__number" value="">
                      	</div>
                    </div>
                </div>
                <!-- AUTO MODE -->
                <div class="game-autobet" id="autobet_mode" style="display:none">
                    <div class="settings-input__wrapper">
                    	<div class="games-input__wrapper">
                        	<label for="9dte0n">Número de rondas</label>
                        	<input id="9dte0n" type="text" autocomplete="off" spellcheck="false" tabindex="-1" class="games-input__number">
                      	</div>
                      	<span class="indicator _infinity">∞</span>
                    </div>
                    <div class="settings-input__wrapper _has_btn">
                      	<label for="on_loss">Cuando ganasa</label>
                      	<div data-track="" class="input-button _md active">
                        	<div class="input-button__text">Reiniciar</div>
                      	</div>
                      	<div data-track="" class="input-button _md">
                        	<div class="input-button__text">Aumentar en:</div>
                      	</div>
                      	<div class="games-input__wrapper  input--disabled">
                        	<input id="on_win" name="on_win" type="text" autocomplete="off" spellcheck="false" disabled="" tabindex="-1" class="games-input__number">
                      	</div>
                      	<span class="indicator _infinity">%</span>
                    </div>
                    <div class="settings-input__wrapper _has_btn">
                      	<label for="on_loss">Cuando pierdes</label>
                      	<div data-track="" class="input-button _md active">
                        	<div class="input-button__text">Reiniciar</div>
                      	</div>
                      	<div data-track="" class="input-button _md">
                        	<div class="input-button__text">Aumentar en:</div>
                      	</div>
                      	<div class="games-input__wrapper  input--disabled">
                        	<input id="onLoseIncreaseAmount" name="on_lose" type="text" autocomplete="off" spellcheck="false" disabled="" tabindex="-1" class="games-input__number">
                      	</div>
                      	<span class="indicator _infinity">%</span>
                    </div>
                    <div class="settings-input__wrapper _has_btn">
                      	<label for="on_any_win">Cualquier ganancia</label>
                      	<div class="switcher">
                        	<div class="switcher__inner">
                          		<input type="checkbox" id="on_any_win" class="switcher__input">
                          		<label for="on_any_win" data-track="autobet_setup.stopwin" class="switcher__label"></label>
                        	</div>
                      	</div>
                    </div>
                </div>
                <!-- START GAME BUTTON -->
                <div class="game-betslip__buttons">
                    <div class="btn-new _placebet">
                      	<div class="btn-bg _bg1"></div>
                      	<div class="btn-bg _bg11"></div>
                      	<div class="btn-new__border">
                        	<div class="btn-bg _bg2"></div>
                        	<div class="btn-bg _bg22"></div>
                        	<div class="btn-new__inner">
                          		<div class="btn-bg _bg1"></div>
                          		<div class="btn-bg _bg11"></div>
                          		<div class="btn-new__text">
	                            	<div class="text-wrapper">
	                              		<div class="text">Comienza el juego</div>
	                              		<div class="_small"><span class="_roboto"></span></div>
	                            	</div>
	                            	<div class="indicator _placebet"></div>
	                          	</div>
                        	</div>
                      	</div>
                    </div>
                </div>
                <!-- DETAILS ONLY MANUAL MODE -->
                <div class="game-details" id="game_details">
                    <div class="game-details__inner">
                      	<div class="game-details__inner-container">
                        	<div class="game-details__title">Detalles del juego</div>
                        	<div class="game-details__row">
                          		<div class="game-details__row-icon _crystal"></div>
                          		<div class="game-details__row-text">Joyas dejadas</div>
                          		<div class="game-details__row-value" data-value="crystals">0</div>
                        	</div>
                        	<div class="game-details__row">
                          		<div class="game-details__row-icon _mine"></div>
                          		<div class="game-details__row-text">Riesgo de minas:</div>
                          		<div class="game-details__row-value" data-value="percent">0%</div>
                        	</div>
                        	<div class="game-details__row">
                          		<div class="game-details__row-icon _tiles"></div>
                          		<div class="game-details__row-text">Azulejos abiertos</div>
                          		<div class="game-details__row-value" data-value="progress">0/25</div>
                        	</div>
                      	</div>
                    </div>
                </div>

           	</div>

            <!-- STATS -->
            <div class="game-statistic">
                <div class="border"></div>
            	<ul class="tabs">
                	<li data-track="all_bets" class="tabs__item _active">
                  		<div class="tabs__item-inner">
                    		<div class="text">Todas las apuestas</div>
                  		</div>
                	</li>
                	<li data-track="top_bets" class="tabs__item">
                  		<div class="tabs__item-inner">
                    		<div class="text">Las mejores apuestas</div>
                  		</div>
                	</li>
                	<li data-track="my_bets" class="tabs__item">
                  		<div class="tabs__item-inner">
                    		<div class="text">Mis apuestas</div>
                  		</div>
            		</li>
            	</ul>
              	<div class="content">
                	<div class="tab-content" data-type="all_bets">
                  		<div class="titles">
                    		<div class="titles__item">Juego</div>
                    		<div class="titles__item">Actor</div>
                    		<div class="titles__item _time">Tiempo</div>
                    		<div class="titles__item">Monto de la apuesta</div>
                    		<div class="titles__item">Multiplicador</div>
                    		<div class="titles__item">Pago</div>
                  		</div>
                  		<div class="separator"></div>
                  		<div class="rows">
	                        <div class="row">
	                        	<div class="cell _capitalize _nowrap">
	                            	<div class="icon folders-icon _mines"></div>Mines
	                        	</div>
	                          	<div class="cell _user _fw500">
	                            	<div class="inner">Alondra segura</div>
	                        	</div>
	                        	<div class="cell _time _fw500"><span>9:27 PM</span></div>
	                        	<div class="cell amount _amount _fw600">$0.10</div>
	                        	<div class="cell _fw500">1.36x</div>
	                        	<div class="cell _fw600">$0.14</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Digital Coyote</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.12</div>
								<div class="cell _fw500">2.71x</div>
								<div class="cell _fw600">$0.32</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _crash"></div>Crash X
								</div>
								<div class="cell _user _fw500">
									<div class="inner">ind_turbo_9492539</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$1.19</div>
								<div class="cell _fw500">1.51x</div>
								<div class="cell _fw600">$1.80</div>
	                        </div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _hilo"></div>Hi-lo
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Democratic Constr...</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.01</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
							</div>
			                <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _fruittowers"></div>Fruit Towers
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Relevant Porcupin...</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.26</div>
								<div class="cell _fw500">1.90x</div>
								<div class="cell _fw600">$0.50</div>
			                </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _ballandball"></div>Ball &amp; Ball
								</div>
								<div class="cell _user _fw500">
									<div class="inner">42721799</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.48</div>
								<div class="cell _fw500">1.40x</div>
								<div class="cell _fw600">$0.67</div>
	                        </div>
					        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">mvp_183080542</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.58</div>
								<div class="cell _fw500">1.64x</div>
								<div class="cell _fw600">$0.96</div>
					        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _towers"></div>Towers
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Available Spider</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.61</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Gleaming Jackal</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.33</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Technological Imp...</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.00</div>
								<div class="cell _fw500">1.42x</div>
								<div class="cell _fw600">$0.00</div>
							</div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _keno"></div>Keno
								</div>
								<div class="cell _user _fw500">
									<div class="inner">953854075_EGP</div>
								</div>
								<div class="cell _time _fw500"><span>9:27 PM</span></div>
								<div class="cell amount _amount _fw600">$0.06</div>
								<div class="cell _fw500">1.20x</div>
								<div class="cell _fw600">$0.07</div>
	                        </div>
                		</div>
                	</div>
            		<div class="tab-content" data-type="top_bets" style="display:none;">
						<div class="titles">
							<div class="titles__item">Juego</div>
							<div class="titles__item">Actor</div>
							<div class="titles__item _time">Tiempo</div>
							<div class="titles__item">Monto de la apuesta</div>
							<div class="titles__item">Çarpan</div>
							<div class="titles__item">Ödeme</div>
						</div>
                  		<div class="separator"></div>
                  		<div class="rows">
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _limbo"></div>Limbo Rider
								</div>
								<div class="cell _user _fw500">
									<div class="inner">959724695_NGN</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.05</div>
								<div class="cell _fw500">20.00x</div>
								<div class="cell _fw600">$0.94</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">ind_turbo_9107175</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.06</div>
								<div class="cell _fw500">18.21x</div>
								<div class="cell _fw600">$1.09</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _turbomines"></div>Turbomines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">paulod</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.53</div>
								<div class="cell _fw500">18.21x</div>
								<div class="cell _fw600">$9.65</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _stairs"></div>Stairs
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Illegal Hedgehog</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.45</div>
								<div class="cell _fw500">13.16x</div>
								<div class="cell _fw600">$5.92</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">1037482</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.00</div>
								<div class="cell _fw500">18.21x</div>
								<div class="cell _fw600">$2.00</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _keno"></div>Keno
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Educational Touca...</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.11</div>
								<div class="cell _fw500">12.00x</div>
								<div class="cell _fw600">$1.33</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _hilo"></div>Hi-lo
								</div>
								<div class="cell _user _fw500">
									<div class="inner">erlhahaha</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.05</div>
								<div class="cell _fw500">19.71x</div>
								<div class="cell _fw600">$0.89</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _ballandball"></div>Ball &amp; Ball
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Nutty Mockingbird</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.12</div>
								<div class="cell _fw500">10.00x</div>
								<div class="cell _fw600">$1.19</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Jealous Boa</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">10.18x</div>
								<div class="cell _fw600">$1,018.00</div>
							</div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _fruittowers"></div>Fruit Towers
								</div>
								<div class="cell _user _fw500">
									<div class="inner">Quickest Echidna</div>
								</div>
								<div class="cell _time _fw500"><span>7:49 PM</span></div>
								<div class="cell amount _amount _fw600">$0.02</div>
								<div class="cell _fw500">10.96x</div>
								<div class="cell _fw600">$0.19</div>
							</div>
                  		</div>
                	</div>
                	<div class="tab-content" data-type="my_bets" style="display:none;">
						<div class="titles">
							<div class="titles__item">Juego</div>
							<div class="titles__item _time">Tiempo</div>
							<div class="titles__item">Monto de la apuesta</div>
							<div class="titles__item">Multiplicador</div>
							<div class="titles__item">Ödeme</div>
						</div>
                  		<div class="separator"></div>
                  		<div class="rows">
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>6:24 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
							</div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>6:23 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>6:23 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">16.81x</div>
								<div class="cell _fw600">$1,681.00</div>
	                        </div>
		                    <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>5:12 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
		                    </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>5:12 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>5:11 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
							<div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>5:11 PM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
							</div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>4:56 AM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>4:56 AM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
	                        <div class="row">
								<div class="cell _capitalize _nowrap">
									<div class="icon folders-icon _mines"></div>Mines
								</div>
								<div class="cell _time _fw500"><span>4:56 AM</span></div>
								<div class="cell amount _amount _fw600">$100.00</div>
								<div class="cell _fw500">0.00x</div>
								<div class="cell _fw600">$0.00</div>
	                        </div>
                  		</div>
                	</div>
              	</div>
            </div>
            <link rel="stylesheet" href="mines/css/icons.css">
        </div>
        <div class="messages"></div>
    </div>
</div>

 <!-- Скрипт для обычной игры -->
<script id="realScript" type="text/javascript" style="display:none;">
    function App() {
      return {
        total_account: parseFloat(<?php echo htmlspecialchars( SYS_BALANCE ); ?>),
        coeffs: {
          2: [1.03, 1.13, 1.23, 1.36, 1.5, 1.67, 1.86, 2.1, 2.38, 2.71, 3.13, 3.65, 4.32, 5.18, 6.33, 7.92, 10.18, 13.57, 19, 28.5, 47.5, 95, 285],
          3: [1.08, 1.23, 1.42, 1.64, 1.92, 2.25, 2.68, 3.21, 3.9, 4.8, 6, 7.64, 9.93, 13.24, 18.21, 26.01, 39.02, 62.43, 109.25, 218.5, 546.25, 2180],
          4: [1.13, 1.36, 1.64, 2.01, 2.48, 3.1, 3.93, 5.05, 6.6, 8.8, 12.01, 16.81, 24.28, 36.42, 57.23, 95.38, 171.68, 343.36, 801.17, 2400, 12001],
          5: [1.19, 1.5, 1.92, 2.48, 3.26, 4.34, 5.89, 8.16, 11.56, 16.81, 25.21, 39.22, 63.73, 109.25, 200.29, 400.58, 901.31, 2400, 8410, 50470],
          6: [1.25, 1.67, 2.25, 3.1, 4.34, 6.2, 9.06, 13.59, 21.01, 33.62, 56.03, 98.04, 182.08, 364.17, 801.17, 2000, 6000, 24030, 168240],
          7: [1.32, 1.86, 2.68, 3.93, 5.89, 9.06, 14.35, 23.48, 39.92, 70.97, 133.06, 266.12, 576.6, 1380, 3800, 12680, 57080, 456660],
          8: [1.4, 2.1, 3.21, 5.05, 8.16, 13.59, 23.48, 42.27, 79.84, 159.67, 342.16, 798.37, 2070, 6220, 22830, 114160],
          9: [1.48, 2.38, 3.9, 6.6, 11.56, 21.01, 39.92, 79.84, 169.65, 387.78, 969.44, 2710, 8820, 35280, 194080],
          10: [1.58, 2.71, 4.8, 8.8, 16.81, 33.62, 70.97, 159.67, 387.78, 1030, 3100, 10850, 47050, 282300],
          11: [1.7, 3.13, 6, 12.01, 25.21, 56.03, 133.06, 342.16, 969.44, 3100, 11630, 54280, 352870],
          12: [1.83, 3.65, 7.64, 16.81, 39.22, 98.04, 266.12, 798.37, 2710, 10850, 54280, 380020],
          13: [1.98, 4.32, 9.93, 24.28, 63.73, 182.08, 576.6, 2070, 8820, 47050, 352870],
          14: [2.16, 5.18, 13.24, 36.42, 109.25, 364.17, 1380, 6220, 35280, 282300],
          15: [2.38, 6.33, 18.21, 57.23, 200.29, 801.17, 3800, 22830, 194080],
          16: [2.64, 7.92, 26.01, 95.38, 400.58, 2000, 12680, 114160],
          17: [2.97, 10.18, 39.02, 171.68, 901.31, 6000, 57080],
          18: [3.39, 13.57, 62.43, 343.36, 2400, 24030, 456660],
          19: [3.96, 19, 109.25, 801.17, 8410, 168240],
          20: [4.75, 28.5, 218.5, 2400, 50470],
          21: [5.94, 47.5, 546.25, 12010],
          22: [7.92, 95, 2.18],
          23: [11.88, 285],
          24: [23.75]
        },
        current_coeff: 0,
        game_process: false,
        game_random: 1,
        bet_mode: "manual",
        bet: 10,
        valute: '<?php echo htmlspecialchars( SYS_CURRENCY ); ?>',
        min_bet: 10,
        max_bet: 100000,
        num_lose_fields: 3,
        total_account: <?php echo htmlspecialchars( SYS_BALANCE ); ?>,
        game_bet: 0,
        game_count: 0,
        min_cells: 2,
        max_cells: 24,
        count_opened: 0,
        win: false,
        trace: true,
        mine_positions: [],
        init: function () {
          var $this = this;
          if ($this.trace) { }
          $('.game-betslip__buttons.btn-new ._small').hide();
          $('#current_bet').val($this.bet + $this.valute);
          $('#setup_bet_wrapper .tooltip-hint').html('Maksimum Kar ' + $this.bet + $this.valute);
          $('#mines').val($this.num_lose_fields);
          $this.prepare_game();
          $this.draw_coeffs(0);
          $this.bind();
          setInterval(function () {
            $('[data-type="all_bets"] .rows .row').last().prependTo($('[data-type="all_bets"] .rows'));
          }, 2000);
        },
        bind: function () {
          var $this = this;
          $(document).ready(function () {
            $('.game-betslip__buttons .btn-new').off().on('click', function () {
              var $self = $(this);

              // Проверка баланса перед началом игры
              if ($this.total_account < $this.min_bet || $this.total_account <= 0) {
                Notiflix.Notify.failure('Saldo insuficiente. Por favor, recargue su cuenta.');
                return;
              }

              if ($self.hasClass('_placebet') && $this.total_account < $this.bet) {
                Notiflix.Notify.failure('Fondos insuficientes en el saldo para esta apuesta');
                return;
              }

              if (!$self.attr('disabled')) {
                $self.attr('disabled', "disabled");
                var $indicator = $('.indicator', $self);
                var $label = $('.text', $self);
                var $count = $('._small', $self);
                var $counter = $('._roboto', $count);
                if ($self.hasClass('_placebet')) {
                  $self.removeClass('_placebet');
                  $indicator.removeClass('_placebet');
                  $self.addClass('_cancel');
                  $indicator.addClass('_cancel');
                  $label.html('Cancelar');
                  $count.show();
                  $counter.html('');

                  console.log("Позиции мин:", $this.mine_positions.sort((a, b) => a - b));

                  $this.game_process = true;
                  $this.game_count = 0;
                  $this.game_bet = 0;
                  $this.win = false;
                  $this.count_opened = 0;
                  $this.current_coeff = 0;
                  $this.draw_details();
                  $('#game_field').addClass('_active');
                }
                else {
                  if ($self.hasClass('_cashout')) {
                    $self.removeClass('_cashout');
                    $indicator.removeClass('_cashout');
                    $self.addClass('_placebet');
                    $indicator.addClass('_placebet');
                    $label.html('Comienza el juego');
                    $count.hide();
                    $counter.html('');
                    $this.win = true;
                    $this.complete_game();
                  }
                }
              }
            });

            $('#game-mode-tabs .mode-btn').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $tab = $self.data('track');
                switch ($tab) {
                  case "manual":
                    $this.bet_mode = "manual";
                    $('#game_details').show();
                    $('#autobet_mode').hide();
                    break;
                  case "auto":
                    $this.bet_mode = "auto";
                    $('#game_details').hide();
                    $('#autobet_mode').show();
                    break;
                }
                $('#game-mode-tabs .mode-btn').removeClass('active');
                $self.addClass('active');
              }
            });

            $('#setup_bet_wrapper .button').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $type = $self.data('track');
                var $wrap = $self.parent();
                var $text = $('input[type="text"]', $wrap);
                var $val = parseFloat($text.val().replace(/[^\d\.\,]/gi, ''));
                switch ($type) {
                  case "min": $this.bet = $this.min_bet; break;
                  case "max": $this.bet = $this.max_bet; break;
                  case "sub":
                    $val -= 1;
                    $this.bet = $val < $this.min_bet ? $this.min_bet : $val;
                    break;
                  case "add":
                    $val += 1;
                    $this.bet = $val > $this.max_bet ? $this.max_bet : $val;
                    break;
                }
                $this.bet = $this.bet > $this.total_account ? $this.total_account : $this.bet;
                $text.val($this.bet + $this.valute);
                $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                $this.draw_details();
              }
              else {
                $text.val($this.bet + $this.valute);
              }

              if ($this.bet > $this.total_account) {
                Notiflix.Notify.info('El tipo se reduce a su saldo actual');
              }
            });

            $('#current_bet').off().on('keyup', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $val = parseFloat($self.val().replace(/[^\d\.\,]/gi, ''));
                if ($this.trace) { console.log("Current bet: " + $val); }
                $this.bet = $val < $this.min_bet ? $this.min_bet : ($val > $this.max_bet ? $this.max_bet : $val);
                $this.bet = $this.bet > $this.total_account ? $this.total_account : $this.bet;
                $self.val($this.bet + $this.valute);
                $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                $this.draw_details();
              }
              else {
                $self.val($this.bet + $this.valute);
              }

              if ($this.bet > $this.total_account) {
                Notiflix.Notify.info('El tipo se reduce a su saldo actual');
              }
            });

            $('#num_lose_fields .button').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $type = $self.data('track');
                var $wrap = $self.parent();
                var $text = $('input[type="text"]', $wrap);

                var oldValue = $this.num_lose_fields;

                switch ('' + $type) {
                  case "3": $this.num_lose_fields = 3; break;
                  case "5": $this.num_lose_fields = 5; break;
                  case "10": $this.num_lose_fields = 10; break;
                  case "20": $this.num_lose_fields = 20; break;
                  case "sub":
                    $this.num_lose_fields -= 1;
                    $this.num_lose_fields = $this.num_lose_fields < $this.min_cells ? $this.min_cells : $this.num_lose_fields;
                    break;
                  case "add":
                    $this.num_lose_fields += 1;
                    $this.num_lose_fields = $this.num_lose_fields > $this.max_cells ? $this.max_cells : $this.num_lose_fields;
                    break;
                }

                if (oldValue !== $this.num_lose_fields) {
                  $text.val($this.num_lose_fields);
                  $this.draw_details();
                  $this.draw_coeffs();
                  $this.prepare_game();
                  $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                }
              }
              else {
                $text.val($this.num_lose_fields);
              }
            });

            $('#mines').off().on('keyup', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $val = parseInt($self.val().replace(/[^\d]/gi, ''));
                var oldValue = $this.num_lose_fields;
                $this.num_lose_fields = $val < $this.min_cells ? $this.min_cells : ($val > $this.max_cells ? $this.max_cells : $val);

                if (oldValue !== $this.num_lose_fields) {
                  $self.val($this.num_lose_fields);
                  $this.draw_details();
                  $this.draw_coeffs();
                  $this.prepare_game();
                  $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                }
              }
              else {
                $self.val($this.num_lose_fields);
              }
            });

            $('.game-statistic .tabs__item').off().on('click', function () {
              var $self = $(this);
              var $type = $self.data('track');
              $('.game-statistic .tabs__item').removeClass('_active');
              $self.addClass('_active');
              $('.game-statistic .content .tab-content').hide();
              $('.game-statistic .content .tab-content[data-type="' + $type + '"]').show();
            });
          });
        },
        // Новая функция для обновления возможного выигрыша
        updatePossibleWin: function() {
          var $this = this;
          var possibleWin = ($this.bet * $this.coeffs[$this.num_lose_fields][0]).toFixed(2) + $this.valute;
          $('.game-tile__inner-possible-win').text(possibleWin);
        },
        draw_coeffs: function ($stp) {
          var $this = this;
          var $wrap = $('#coeffs_list');
          var $stp = typeof $stp == "undefined" ? 0 : $stp;
          $wrap.html('');
          if ($this.trace) { }
          for (var $i = $stp; $i < $this.coeffs[$this.num_lose_fields].length; $i++) {
            var $cf = $this.coeffs[$this.num_lose_fields][$i];
            var $tmps = `<div data-history-item="false" class="game-history__item" style="min-width: 78px;">
                                        <div class="game-history__item-text">x${$cf > 1000 ? ($cf / 1000).toFixed(2) + 'k' : $cf}</div>
                                    </div>`;
            $wrap.append($tmps);
          }
          $this.bind();
        },
        prepare_game: function () {
          var $this = this;
          if ($this.trace) { }
          $('#win_modal').hide();
          $('#game_field').css('opacity', 1);
          $('#coeffs_list').scrollLeft(0);

          $this.mine_positions = [];
          var allPositions = Array.from({ length: 25 }, (_, i) => i + 1);
          for (let i = 0; i < $this.num_lose_fields; i++) {
            const randomIndex = Math.floor(Math.random() * allPositions.length);
            $this.mine_positions.push(allPositions[randomIndex]);
            allPositions.splice(randomIndex, 1);
          }
          fetch('/save_mines.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mine_positions=${$this.mine_positions.join(',')}`
          })
            .then(response => response.json())
            .then(data => {
              if (!data.success) console.error('Ошибка сохранения мин:', data.message);
            })
            .catch(error => console.error('Ошибка:', error));

          $this.randomized = Array(25).fill(0);
          $this.mine_positions.forEach(pos => {
            $this.randomized[pos - 1] = 1;
          });

          if ($this.trace) { }
          $this.draw_game($this.randomized);
          $this.draw_details();
        },
        draw_game: function ($arr) {
          var $this = this;
          if ($this.trace) { }
          var $wrap = $('#game_field');
          $wrap.html('');
          for (var $i = 0; $i < $arr.length; $i++) {
            var $type = $arr[$i];
            var $tmps = `<div class="game-tile" data-id="${$i + 1}">
                                        <div class="game-tile__inner-possible-win">${($this.bet * $this.coeffs[$this.num_lose_fields][0]).toFixed(2) + $this.valute}</div> 
                                        <div class="game-tile__inner">${!$type ? '<div class="diamond"></div>' : '<div class="bomb"></div>'}</div>
                                    </div>`;
            $wrap.append($tmps);
          }
          $this.bind_game();
        },
        bind_game: function () {
          var $this = this;
          $('.game-tiles .game-tile').off().on('click', function (e) {
            var $self = $(this);
            var $inner = $('.game-tile__inner', $self);
            if (!$self.hasClass('_loading') && !$self.hasClass('_lose') && !$self.hasClass('_win') && !$self.hasClass('_active')) {
              if ($this.game_process) {
                var cellId = $self.data('id');
                if ($this.mine_positions.includes(cellId)) {
                  $('.game-betslip__buttons .btn-new').attr('disabled', "disabled");
                  $this.game_bet = $this.bet * $this.coeffs[$this.num_lose_fields][$this.current_coeff];
                  $this.win = false;
                  $self.addClass('_lose');
                  setTimeout(function () {
                    $self.addClass('_opened');
                    setTimeout(function () {
                      $self.addClass('_active');
                    }, 100);
                  }, 100);
                  $this.complete_game();
                }
                else {
                  $this.win = true;
                  $self.addClass('_loading');
                  setTimeout(function () {
                    $self.addClass('_win');
                    setTimeout(function () {
                      $self.addClass('_active');
                    }, 100);
                    $this.move();
                  }, 100);
                  setTimeout(function () {
                    $self.removeClass('_loading');
                  }, 300);
                }
              }
            }
          });
        },
        move: function () {
          var $this = this;
          console.log("Movement detected");
          var $self = $('.game-betslip__buttons .btn-new');
          var $indicator = $('.indicator', $self);
          var $label = $('.text', $self);
          var $count = $('._small', $self);
          var $counter = $('._roboto', $count);
          var $total_crystals = 25 - $this.num_lose_fields;
          $this.count_opened += 1;
          $this.game_bet = $this.bet * $this.coeffs[$this.num_lose_fields][$this.current_coeff];

          $self.removeClass('_cancel');
          $indicator.removeClass('_cancel');
          $self.addClass('_cashout');
          $indicator.addClass('_cashout');
          $label.html('Obtener ganancias');
          $count.show();
          $self.removeAttr('disabled');

          let counts = setInterval(updated);
          let upto = parseFloat($('.game-betslip__buttons .btn-new ._roboto').html().replace(/[^\d\.\,]/gi, ''));
          upto = upto ? upto : 0;
          function updated() {
            upto += $this.game_bet / 300;
            $counter.html(($this.game_bet).toFixed(2) + $this.valute);
            if (upto >= $this.game_bet) {
              $counter.html(($this.game_bet).toFixed(2) + $this.valute);
              clearInterval(counts);
            }
          }

          if ($this.count_opened >= $total_crystals) {
            $('.game-betslip__buttons .btn-new').click();
          }
          else {
            $this.game_count += 1;
            $this.current_coeff += 1;
            $('#coeffs_list .game-history__item').removeClass('_active').eq($this.current_coeff - 1).addClass('_active');
            if (($this.current_coeff > 4)) {
              $('#coeffs_list').scrollLeft((($this.current_coeff - 4) * $('.game-history__item').eq(0).width() + ($this.current_coeff * 3)));
            }
            var $win = ($this.bet * ($this.coeffs[$this.num_lose_fields][$this.current_coeff]));
            $win = $win > 1000 ? (($win / 1000).toFixed(2) + 'k') : ($win).toFixed(2);
            $('#game_field .game-tile__inner-possible-win').html($win + $this.valute);
          }
          $this.draw_details();
        },
        complete_game: function () {
          var $this = this;
          $('#game_field').removeClass('_active');

          $('#game_field .game-tile').each(function () {
            var cellId = $(this).data('id');
            if ($this.mine_positions.includes(cellId)) {
              $(this).addClass('_lose _opened _active');
              $('.diamond', this).removeClass('diamond').addClass('bomb');
            }
            else if ($(this).hasClass('_win')) {
              $(this).addClass('_opened _active');
            }
            else {
              $(this).addClass('notselected');
              $('.diamond', this).show();
            }
          });

          $('.notselected').css('opacity', 0.5);

          var $self = $('.game-betslip__buttons .btn-new');
          var $indicator = $('.indicator', $self);
          var $label = $('.text', $self);
          var $count = $('._small', $self);
          var $counter = $('._roboto', $count);
          $('#coeffs_list .game-history__item').removeClass('_active');

          console.log("Game result:", $this.win ? "WIN" : "LOSE", "Stake:", $this.bet, "Win amount:", $this.game_bet);

          const updateServerBalance = (newBalance) => {
            const numericBalance = typeof newBalance === 'string' ?
              parseFloat(newBalance.replace(/[^\d\.]/g, '')) :
              newBalance;

            fetch('/update_balance.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `balance=${numericBalance}`
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  const formattedBalance = parseFloat(data.balance).toFixed(2);
                  $('#balance').text(formattedBalance);
                  $('#total_account').html(formattedBalance + $this.valute);
                  $this.total_account = parseFloat(data.balance);
                } else {
                  console.error('Ошибка обновления баланса:', data.message);
                }
              })
              .catch(error => console.error('Ошибка:', error));
          };

          if ($this.win) {
            const totalWinAmount = parseFloat($this.game_bet);
            const netWinAmount = totalWinAmount - parseFloat($this.bet);

            $this.total_account = parseFloat($this.total_account) + netWinAmount;

            let counts = setInterval(updated);
            let upto = parseFloat($('#total_account').html().replace(/[^\d\.]/g, ''));
            upto = upto ? upto : 0;

            function updated() {
              upto += netWinAmount / 300;
              $('#total_account').html(upto.toFixed(2) + $this.valute);
              if (upto >= parseFloat($this.total_account)) {
                $('#total_account').html(parseFloat($this.total_account).toFixed(2) + $this.valute);
                clearInterval(counts);
                updateServerBalance($this.total_account);

                $('#modal_result').html(totalWinAmount.toFixed(2) + $this.valute);
                const currentCoeff = $this.coeffs[$this.num_lose_fields][$this.count_opened - 1];

                let formattedCoeff;
                if (currentCoeff >= 1000) {
                  formattedCoeff = (currentCoeff / 1000).toFixed(2) + 'k';
                } else if (currentCoeff % 1 === 0) {
                  formattedCoeff = currentCoeff.toString();
                } else {
                  formattedCoeff = currentCoeff.toFixed(2);
                  formattedCoeff = formattedCoeff.replace(/\.?0+$/, '');
                }

                $('#modal_coeff strong').html('x' + formattedCoeff);

                setTimeout(function () {
                  $('#win_modal').css('display', 'flex');
                  setTimeout(function () {
                    $this.prepare_game();
                    $self.removeClass('_cashout').removeClass('_cancel');
                    $indicator.removeClass('_cashout').removeClass('_cancel');
                    $self.addClass('_placebet');
                    $indicator.addClass('_placebet');
                    $label.html('Comienza el juego');
                    $count.hide();
                    $counter.html('');
                    $self.removeAttr('disabled');
                  }, 4000);
                }, 200);
              }
            }
          } else {
            const lossAmount = parseFloat($this.bet);
            $this.total_account = parseFloat($this.total_account) - lossAmount;

            const formattedBalance = parseFloat($this.total_account).toFixed(2);
            $('#total_account').html(formattedBalance + $this.valute);
            $('#balance').text(formattedBalance);
            updateServerBalance($this.total_account);

            setTimeout(function () {
              $this.prepare_game();
              $self.removeClass('_cashout').removeClass('_cancel');
              $indicator.removeClass('_cashout').removeClass('_cancel');
              $self.addClass('_placebet');
              $indicator.addClass('_placebet');
              $label.html('Comienza el juego');
              $count.hide();
              $counter.html('');
              $self.removeAttr('disabled');
            }, 2000);
          }

          $this.game_process = false;
          $this.game_count = 0;
          $this.win = false;
          $this.current_coeff = 0;
          $this.game_bet = 0;
        },
        draw_details: function () {
          var $this = this;
          var $crystals = 25 - $this.num_lose_fields;
          var $percent = $crystals * ($this.max_cells / 100);
          var $progress = $this.count_opened + "/" + 25;
          $('#game_details [data-value="crystals"]').html($crystals);
          $('#game_details [data-value="percent"]').html(($percent).toFixed(2) + '%');
          $('#game_details [data-value="progress"]').html($progress);
        },
        randomized: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        prepared: [
          [
            0, 0, 0, 0, 0,
            0, 1, 0, 0, 0,
            0, 0, 0, 1, 0,
            0, 0, 1, 0, 0,
            0, 0, 0, 0, 0
          ],
          [
            1, 0, 0, 0, 0,
            0, 0, 0, 1, 0,
            0, 0, 0, 0, 0,
            0, 1, 0, 0, 0,
            0, 0, 0, 0, 0
          ],
          [
            0, 0, 0, 0, 0,
            0, 0, 1, 0, 0,
            0, 0, 0, 0, 1,
            0, 0, 0, 0, 0,
            0, 0, 0, 1, 0
          ]
        ],
        prepared_num: 0
      }
    }
    //window.$app = new App();
    //$app.init();
</script>

<!-- Скрипт для демо-режима -->
<script id="demoScript" type="text/javascript" style="display:none;">
    function AppDemo() {
      return {
        total_account: 500, // Фиксированный баланс 500 для демо
        coeffs: {
          2: [1.03, 1.13, 1.23, 1.36, 1.5, 1.67, 1.86, 2.1, 2.38, 2.71, 3.13, 3.65, 4.32, 5.18, 6.33, 7.92, 10.18, 13.57, 19, 28.5, 47.5, 95, 285],
          3: [1.08, 1.23, 1.42, 1.64, 1.92, 2.25, 2.68, 3.21, 3.9, 4.8, 6, 7.64, 9.93, 13.24, 18.21, 26.01, 39.02, 62.43, 109.25, 218.5, 546.25, 2180],
          4: [1.13, 1.36, 1.64, 2.01, 2.48, 3.1, 3.93, 5.05, 6.6, 8.8, 12.01, 16.81, 24.28, 36.42, 57.23, 95.38, 171.68, 343.36, 801.17, 2400, 12001],
          5: [1.19, 1.5, 1.92, 2.48, 3.26, 4.34, 5.89, 8.16, 11.56, 16.81, 25.21, 39.22, 63.73, 109.25, 200.29, 400.58, 901.31, 2400, 8410, 50470],
          6: [1.25, 1.67, 2.25, 3.1, 4.34, 6.2, 9.06, 13.59, 21.01, 33.62, 56.03, 98.04, 182.08, 364.17, 801.17, 2000, 6000, 24030, 168240],
          7: [1.32, 1.86, 2.68, 3.93, 5.89, 9.06, 14.35, 23.48, 39.92, 70.97, 133.06, 266.12, 576.6, 1380, 3800, 12680, 57080, 456660],
          8: [1.4, 2.1, 3.21, 5.05, 8.16, 13.59, 23.48, 42.27, 79.84, 159.67, 342.16, 798.37, 2070, 6220, 22830, 114160],
          9: [1.48, 2.38, 3.9, 6.6, 11.56, 21.01, 39.92, 79.84, 169.65, 387.78, 969.44, 2710, 8820, 35280, 194080],
          10: [1.58, 2.71, 4.8, 8.8, 16.81, 33.62, 70.97, 159.67, 387.78, 1030, 3100, 10850, 47050, 282300],
          11: [1.7, 3.13, 6, 12.01, 25.21, 56.03, 133.06, 342.16, 969.44, 3100, 11630, 54280, 352870],
          12: [1.83, 3.65, 7.64, 16.81, 39.22, 98.04, 266.12, 798.37, 2710, 10850, 54280, 380020],
          13: [1.98, 4.32, 9.93, 24.28, 63.73, 182.08, 576.6, 2070, 8820, 47050, 352870],
          14: [2.16, 5.18, 13.24, 36.42, 109.25, 364.17, 1380, 6220, 35280, 282300],
          15: [2.38, 6.33, 18.21, 57.23, 200.29, 801.17, 3800, 22830, 194080],
          16: [2.64, 7.92, 26.01, 95.38, 400.58, 2000, 12680, 114160],
          17: [2.97, 10.18, 39.02, 171.68, 901.31, 6000, 57080],
          18: [3.39, 13.57, 62.43, 343.36, 2400, 24030, 456660],
          19: [3.96, 19, 109.25, 801.17, 8410, 168240],
          20: [4.75, 28.5, 218.5, 2400, 50470],
          21: [5.94, 47.5, 546.25, 12010],
          22: [7.92, 95, 2.18],
          23: [11.88, 285],
          24: [23.75]
        },
        current_coeff: 0,
        game_process: false,
        game_random: 1,
        bet_mode: "manual",
        bet: 100,
        valute: '$',
        min_bet: 1,
        max_bet: 100,
        num_lose_fields: 3,
        game_bet: 0,
        game_count: 0,
        min_cells: 2,
        max_cells: 24,
        count_opened: 0,
        win: false,
        trace: true,
        mine_positions: [],
        init: function () {
          var $this = this;
          if ($this.trace) { }
          $('.game-betslip__buttons.btn-new ._small').hide();
          $('#current_bet').val($this.bet + $this.valute);
          $('#setup_bet_wrapper .tooltip-hint').html('Maksimum Kar ' + $this.bet + $this.valute);
          $('#mines').val($this.num_lose_fields);
          $('#total_account').html($this.total_account.toFixed(2) + $this.valute);
          $this.prepare_game();
          $this.draw_coeffs(0);
          $this.bind();
          setInterval(function () {
            $('[data-type="all_bets"] .rows .row').last().prependTo($('[data-type="all_bets"] .rows'));
          }, 2000);
        },
        bind: function () {
          var $this = this;
          $(document).ready(function () {
            $('.game-betslip__buttons .btn-new').off().on('click', function () {
              var $self = $(this);

              // Проверка баланса перед началом игры
              if ($self.hasClass('_placebet')) {
                if ($this.total_account < $this.min_bet) {
                  Notiflix.Notify.failure('Saldo insuficiente. Redirigiendo a la página de depósito...');
                  setTimeout(() => { window.location.href = '/deposit.php'; }, 2000);
                  return;
                }
                if ($this.total_account < $this.bet) {
                  Notiflix.Notify.failure('Fondos insuficientes en el balance para esta apuesta');
                  return;
                }
              }

              if (!$self.attr('disabled')) {
                $self.attr('disabled', "disabled");
                var $indicator = $('.indicator', $self);
                var $label = $('.text', $self);
                var $count = $('._small', $self);
                var $counter = $('._roboto', $count);
                if ($self.hasClass('_placebet')) {
                  $self.removeClass('_placebet');
                  $indicator.removeClass('_placebet');
                  $self.addClass('_cancel');
                  $indicator.addClass('_cancel');
                  $label.html('Cancelar');
                  $count.show();
                  $counter.html('');

                  console.log("Позиции мин:", $this.mine_positions.sort((a, b) => a - b));

                  $this.game_process = true;
                  $this.game_count = 0;
                  $this.game_bet = 0;
                  $this.win = false;
                  $this.count_opened = 0;
                  $this.current_coeff = 0;
                  $this.draw_details();
                  $('#game_field').addClass('_active');
                }
                else {
                  if ($self.hasClass('_cashout')) {
                    $self.removeClass('_cashout');
                    $indicator.removeClass('_cashout');
                    $self.addClass('_placebet');
                    $indicator.addClass('_placebet');
                    $label.html('Comienza el juego');
                    $count.hide();
                    $counter.html('');
                    $this.win = true;
                    $this.complete_game();
                  }
                }
              }
            });

            $('#game-mode-tabs .mode-btn').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $tab = $self.data('track');
                switch ($tab) {
                  case "manual":
                    $this.bet_mode = "manual";
                    $('#game_details').show();
                    $('#autobet_mode').hide();
                    break;
                  case "auto":
                    $this.bet_mode = "auto";
                    $('#game_details').hide();
                    $('#autobet_mode').show();
                    break;
                }
                $('#game-mode-tabs .mode-btn').removeClass('active');
                $self.addClass('active');
              }
            });

            $('#setup_bet_wrapper .button').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $type = $self.data('track');
                var $wrap = $self.parent();
                var $text = $('input[type="text"]', $wrap);
                var $val = parseFloat($text.val().replace(/[^\d\.\,]/gi, ''));
                switch ($type) {
                  case "min": $this.bet = $this.min_bet; break;
                  case "max": $this.bet = $this.max_bet; break;
                  case "sub":
                    $val -= 1;
                    $this.bet = $val < $this.min_bet ? $this.min_bet : $val;
                    break;
                  case "add":
                    $val += 1;
                    $this.bet = $val > $this.max_bet ? $this.max_bet : $val;
                    break;
                }
                $this.bet = $this.bet > $this.total_account ? $this.total_account : $this.bet;
                $text.val($this.bet + $this.valute);
                $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                $this.draw_details();

                if ($this.bet > $this.total_account) {
                  Notiflix.Notify.info('La apuesta se redujo a su saldo actual');
                }
              }
              else {
                $text.val($this.bet + $this.valute);
              }
            });

            $('#current_bet').off().on('keyup', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $val = parseFloat($self.val().replace(/[^\d\.\,]/gi, ''));
                if ($this.trace) { console.log("Current bet: " + $val); }
                $this.bet = $val < $this.min_bet ? $this.min_bet : ($val > $this.max_bet ? $this.max_bet : $val);
                $this.bet = $this.bet > $this.total_account ? $this.total_account : $this.bet;
                $self.val($this.bet + $this.valute);
                $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                $this.draw_details();

                if ($this.bet > $this.total_account) {
                  Notiflix.Notify.info('La apuesta se redujo a su saldo actual');
                }
              }
              else {
                $self.val($this.bet + $this.valute);
              }
            });

            $('#num_lose_fields .button').off().on('click', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $type = $self.data('track');
                var $wrap = $self.parent();
                var $text = $('input[type="text"]', $wrap);

                var oldValue = $this.num_lose_fields;

                switch ('' + $type) {
                  case "3": $this.num_lose_fields = 3; break;
                  case "5": $this.num_lose_fields = 5; break;
                  case "10": $this.num_lose_fields = 10; break;
                  case "20": $this.num_lose_fields = 20; break;
                  case "sub":
                    $this.num_lose_fields -= 1;
                    $this.num_lose_fields = $this.num_lose_fields < $this.min_cells ? $this.min_cells : $this.num_lose_fields;
                    break;
                  case "add":
                    $this.num_lose_fields += 1;
                    $this.num_lose_fields = $this.num_lose_fields > $this.max_cells ? $this.max_cells : $this.num_lose_fields;
                    break;
                }

                if (oldValue !== $this.num_lose_fields) {
                  $text.val($this.num_lose_fields);
                  $this.draw_details();
                  $this.draw_coeffs();
                  $this.prepare_game();
                  $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                }
              }
              else {
                $text.val($this.num_lose_fields);
              }
            });

            $('#mines').off().on('keyup', function () {
              var $self = $(this);
              if (!$this.game_process) {
                var $val = parseInt($self.val().replace(/[^\d]/gi, ''));
                var oldValue = $this.num_lose_fields;
                $this.num_lose_fields = $val < $this.min_cells ? $this.min_cells : ($val > $this.max_cells ? $this.max_cells : $val);

                if (oldValue !== $this.num_lose_fields) {
                  $self.val($this.num_lose_fields);
                  $this.draw_details();
                  $this.draw_coeffs();
                  $this.prepare_game();
                  $this.updatePossibleWin(); // Добавлено обновление возможного выигрыша
                }
              }
              else {
                $self.val($this.num_lose_fields);
              }
            });

            $('.game-statistic .tabs__item').off().on('click', function () {
              var $self = $(this);
              var $type = $self.data('track');
              $('.game-statistic .tabs__item').removeClass('_active');
              $self.addClass('_active');
              $('.game-statistic .content .tab-content').hide();
              $('.game-statistic .content .tab-content[data-type="' + $type + '"]').show();
            });
          });
        },
        // Новая функция для обновления возможного выигрыша
        updatePossibleWin: function() {
          var $this = this;
          var possibleWin = ($this.bet * $this.coeffs[$this.num_lose_fields][0]).toFixed(2) + $this.valute;
          $('.game-tile__inner-possible-win').text(possibleWin);
        },
        draw_coeffs: function ($stp) {
          var $this = this;
          var $wrap = $('#coeffs_list');
          var $stp = typeof $stp == "undefined" ? 0 : $stp;
          $wrap.html('');
          if ($this.trace) { }
          for (var $i = $stp; $i < $this.coeffs[$this.num_lose_fields].length; $i++) {
            var $cf = $this.coeffs[$this.num_lose_fields][$i];
            var $tmps = `<div data-history-item="false" class="game-history__item" style="min-width: 78px;">
                                          <div class="game-history__item-text">x${$cf > 1000 ? ($cf / 1000).toFixed(2) + 'k' : $cf}</div>
                                      </div>`;
            $wrap.append($tmps);
          }
          $this.bind();
        },
        prepare_game: function () {
          var $this = this;
          if ($this.trace) { }
          $('#win_modal').hide();
          $('#game_field').css('opacity', 1);
          $('#coeffs_list').scrollLeft(0);

          $this.mine_positions = [];
          var allPositions = Array.from({ length: 25 }, (_, i) => i + 1);
          for (let i = 0; i < $this.num_lose_fields; i++) {
            const randomIndex = Math.floor(Math.random() * allPositions.length);
            $this.mine_positions.push(allPositions[randomIndex]);
            allPositions.splice(randomIndex, 1);
          }
        
          // Добавьте этот блок для сохранения мин в демо-режиме
          fetch('save_mines.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mine_positions=${$this.mine_positions.join(',')}`
          })
            .then(response => response.json())
            .then(data => {
              if (!data.success) console.error('Ошибка сохранения мин:', data.message);
            })
            .catch(error => console.error('Ошибка:', error));
          
          $this.randomized = Array(25).fill(0);
          $this.mine_positions.forEach(pos => {
            $this.randomized[pos - 1] = 1;
          });
        
          if ($this.trace) { }
          $this.draw_game($this.randomized);
          $this.draw_details();
        },
        draw_game: function ($arr) {
          var $this = this;
          if ($this.trace) { }
          var $wrap = $('#game_field');
          $wrap.html('');
          for (var $i = 0; $i < $arr.length; $i++) {
            var $type = $arr[$i];
            var $tmps = `<div class="game-tile" data-id="${$i + 1}">
                                          <div class="game-tile__inner-possible-win">${($this.bet * $this.coeffs[$this.num_lose_fields][0]).toFixed(2) + $this.valute}</div> 
                                          <div class="game-tile__inner">${!$type ? '<div class="diamond"></div>' : '<div class="bomb"></div>'}</div>
                                      </div>`;
            $wrap.append($tmps);
          }
          $this.bind_game();
        },
        bind_game: function () {
          var $this = this;
          $('.game-tiles .game-tile').off().on('click', function (e) {
            var $self = $(this);
            var $inner = $('.game-tile__inner', $self);
            if (!$self.hasClass('_loading') && !$self.hasClass('_lose') && !$self.hasClass('_win') && !$self.hasClass('_active')) {
              if ($this.game_process) {
                var cellId = $self.data('id');
                if ($this.mine_positions.includes(cellId)) {
                  $('.game-betslip__buttons .btn-new').attr('disabled', "disabled");
                  $this.game_bet = $this.bet * $this.coeffs[$this.num_lose_fields][$this.current_coeff];
                  $this.win = false;
                  $self.addClass('_lose');
                  setTimeout(function () {
                    $self.addClass('_opened');
                    setTimeout(function () {
                      $self.addClass('_active');
                    }, 100);
                  }, 100);
                  $this.complete_game();
                }
                else {
                  $this.win = true;
                  $self.addClass('_loading');
                  setTimeout(function () {
                    $self.addClass('_win');
                    setTimeout(function () {
                      $self.addClass('_active');
                    }, 100);
                    $this.move();
                  }, 100);
                  setTimeout(function () {
                    $self.removeClass('_loading');
                  }, 300);
                }
              }
            }
          });
        },
        move: function () {
          var $this = this;
          console.log("Movement detected");
          var $self = $('.game-betslip__buttons .btn-new');
          var $indicator = $('.indicator', $self);
          var $label = $('.text', $self);
          var $count = $('._small', $self);
          var $counter = $('._roboto', $count);
          var $total_crystals = 25 - $this.num_lose_fields;
          $this.count_opened += 1;
          $this.game_bet = $this.bet * $this.coeffs[$this.num_lose_fields][$this.current_coeff];

          $self.removeClass('_cancel');
          $indicator.removeClass('_cancel');
          $self.addClass('_cashout');
          $indicator.addClass('_cashout');
          $label.html('Obtener ganancias');
          $count.show();
          $self.removeAttr('disabled');

          let counts = setInterval(updated);
          let upto = parseFloat($('.game-betslip__buttons .btn-new ._roboto').html().replace(/[^\d\.\,]/gi, ''));
          upto = upto ? upto : 0;
          function updated() {
            upto += $this.game_bet / 300;
            $counter.html(($this.game_bet).toFixed(2) + $this.valute);
            if (upto >= $this.game_bet) {
              $counter.html(($this.game_bet).toFixed(2) + $this.valute);
              clearInterval(counts);
            }
          }

          if ($this.count_opened >= $total_crystals) {
            $('.game-betslip__buttons .btn-new').click();
          }
          else {
            $this.game_count += 1;
            $this.current_coeff += 1;
            $('#coeffs_list .game-history__item').removeClass('_active').eq($this.current_coeff - 1).addClass('_active');
            if (($this.current_coeff > 4)) {
              $('#coeffs_list').scrollLeft((($this.current_coeff - 4) * $('.game-history__item').eq(0).width() + ($this.current_coeff * 3)));
            }
            var $win = ($this.bet * ($this.coeffs[$this.num_lose_fields][$this.current_coeff]));
            $win = $win > 1000 ? (($win / 1000).toFixed(2) + 'k') : ($win).toFixed(2);
            $('#game_field .game-tile__inner-possible-win').html($win + $this.valute);
          }
          $this.draw_details();
        },
        complete_game: function () {
          var $this = this;
          $('#game_field').removeClass('_active');

          $('#game_field .game-tile').each(function () {
            var cellId = $(this).data('id');
            if ($this.mine_positions.includes(cellId)) {
              $(this).addClass('_lose _opened _active');
              $('.diamond', this).removeClass('diamond').addClass('bomb');
            }
            else if ($(this).hasClass('_win')) {
              $(this).addClass('_opened _active');
            }
            else {
              $(this).addClass('notselected');
              $('.diamond', this).show();
            }
          });

          $('.notselected').css('opacity', 0.5);

          var $self = $('.game-betslip__buttons .btn-new');
          var $indicator = $('.indicator', $self);
          var $label = $('.text', $self);
          var $count = $('._small', $self);
          var $counter = $('._roboto', $count);
          $('#coeffs_list .game-history__item').removeClass('_active');

          console.log("Game result:", $this.win ? "WIN" : "LOSE", "Stake:", $this.bet, "Win amount:", $this.game_bet);

          if ($this.win) {
            // Полный выигрыш (включая ставку) для отображения
            const totalWinAmount = parseFloat($this.game_bet);
            // Чистая прибыль (без учета ставки) для добавления к балансу
            const netWinAmount = totalWinAmount - parseFloat($this.bet);

            // Добавляем только чистую прибыль (ставка уже была списана ранее)
            $this.total_account = parseFloat($this.total_account) + netWinAmount;

            let counts = setInterval(updated);
            let upto = parseFloat($('#total_account').html().replace(/[^\d\.]/g, ''));
            upto = upto ? upto : 0;

            function updated() {
              upto += netWinAmount / 300;
              $('#total_account').html(upto.toFixed(2) + $this.valute);
              if (upto >= parseFloat($this.total_account)) {
                $('#total_account').html(parseFloat($this.total_account).toFixed(2) + $this.valute);
                clearInterval(counts);

                // Исправленный расчет коэффициента
                const currentCoeff = $this.coeffs[$this.num_lose_fields][$this.count_opened - 1];

                // Форматирование коэффициента
                let formattedCoeff;
                if (currentCoeff >= 1000) {
                  formattedCoeff = (currentCoeff / 1000).toFixed(2) + 'k';
                } else if (currentCoeff % 1 === 0) {
                  formattedCoeff = currentCoeff.toString();
                } else {
                  formattedCoeff = currentCoeff.toFixed(2);
                  // Удаляем лишние нули после запятой
                  formattedCoeff = formattedCoeff.replace(/\.?0+$/, '');
                }

                // Показываем полный выигрыш (включая ставку)
                $('#modal_result').html(totalWinAmount.toFixed(2) + $this.valute);
                $('#modal_coeff strong').html('x' + formattedCoeff);

                setTimeout(function () {
                  $('#win_modal').css('display', 'flex');
                  setTimeout(function () {
                    $this.prepare_game();
                    $self.removeClass('_cashout').removeClass('_cancel');
                    $indicator.removeClass('_cashout').removeClass('_cancel');
                    $self.addClass('_placebet');
                    $indicator.addClass('_placebet');
                    $label.html('Comienza el juego');
                    $count.hide();
                    $counter.html('');
                    $self.removeAttr('disabled');
                  }, 4000);
                }, 200);
              }
            }
          } else {
            const lossAmount = parseFloat($this.bet);
            $this.total_account = parseFloat($this.total_account) - lossAmount;

            $('#total_account').html($this.total_account.toFixed(2) + $this.valute);

            setTimeout(function () {
              $this.prepare_game();
              $self.removeClass('_cashout').removeClass('_cancel');
              $indicator.removeClass('_cashout').removeClass('_cancel');
              $self.addClass('_placebet');
              $indicator.addClass('_placebet');
              $label.html('Comienza el juego');
              $count.hide();
              $counter.html('');
              $self.removeAttr('disabled');

              if ($this.total_account < $this.min_bet) {
                Notiflix.Notify.failure('Saldo insuficiente. Redirigiendo a la página de depósito...');
                setTimeout(() => { window.location.href = '/deposit.php'; }, 2000);
              }
            }, 2000);
          }

          $this.game_process = false;
          $this.game_count = 0;
          $this.win = false;
          $this.current_coeff = 0;
          $this.game_bet = 0;
        },
        draw_details: function () {
          var $this = this;
          var $crystals = 25 - $this.num_lose_fields;
          var $percent = $crystals * ($this.max_cells / 100);
          var $progress = $this.count_opened + "/" + 25;
          $('#game_details [data-value="crystals"]').html($crystals);
          $('#game_details [data-value="percent"]').html(($percent).toFixed(2) + '%');
          $('#game_details [data-value="progress"]').html($progress);
        },
        randomized: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        prepared: [
          [
            0, 0, 0, 0, 0,
            0, 1, 0, 0, 0,
            0, 0, 0, 1, 0,
            0, 0, 1, 0, 0,
            0, 0, 0, 0, 0
          ],
          [
            1, 0, 0, 0, 0,
            0, 0, 0, 1, 0,
            0, 0, 0, 0, 0,
            0, 1, 0, 0, 0,
            0, 0, 0, 0, 0
          ],
          [
            0, 0, 0, 0, 0,
            0, 0, 1, 0, 0,
            0, 0, 0, 0, 1,
            0, 0, 0, 0, 0,
            0, 0, 0, 1, 0
          ]
        ],
        prepared_num: 0
      }
    }
    //window.$app = new App();
    //$app.init();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Показываем модальное окно при загрузке
        document.getElementById('app').style.display = 'block';         
        
        // Обработчики для кнопок выбора режима
        document.querySelectorAll('.mode-two-btn').forEach( button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                activateGameMode( mode );
                // Скрываем модальное окно после выбора
                document.getElementById('modeSelectionModal').style.display = 'none';
            });
        });

        // Функция активации выбранного режима
        function activateGameMode(mode) {
            // Получаем оба скрипта
            const demoScript = document.getElementById('demoScript');
            const realScript = document.getElementById('realScript');    
            // Удаляем текущий экземпляр игры, если он существует
            //if (window.$app) {
            //    window.$app = null;
            //}
            if( mode === 'demo' ){
                // Обновляем баланс визуально перед инициализацией
                $('#total_account').html('5000.00' + ( demoScript.textContent.match(/valute:\s*'([^']+)'/)[1] || '') );
                // Переинициализируем игру в демо-режиме
                eval( demoScript.textContent );
                console.log("Активирован демо-режим");
                window.$app = new AppDemo(); 
                $app.init(); 
            } 
            else {
                // Получаем валюту из скрипта
                const valuteMatch = realScript.textContent.match(/valute:\s*'([^']+)'/);
                const valute = valuteMatch ? valuteMatch[1] : '';
                // Обновляем баланс визуально перед инициализацией
                $('#total_account').html( <?= htmlspecialchars( SYS_BALANCE ); ?> + valute );
                // Переинициализируем игру в обычном режиме
                eval(realScript.textContent);
                console.log("Активирован обычный режим"); 
                window.$app = new App(); 
                $app.init(); 
            }
        }

        // По умолчанию активируем обычный режим
        activateGameMode('real');
    });
</script> 