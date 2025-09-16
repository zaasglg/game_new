<?php 
	if( IS_MOBILE ){ 
		// мобилка
?>
		<div class="casino-mobile">
			<?php 
				if( !CURRENT_GAME ){
					include_once TPL_DIR ."catalogue_mobile.php";
				} 
				else {
					switch( CURRENT_GAME ){
						case "aviator": 
							include_once TPL_DIR ."games/aviator.php";
							break; 
						case "chicken_road": 
							include_once TPL_DIR ."games/chicken.php";
							break; 
						case "mines": 
							include_once TPL_DIR ."games/mines.php";
							break; 
					}
				}
			?> 
		</div>
<?php 
	} 
	else { 
		// дэсктоп
?>
		<div class="casino">
			<?php 
				// боковая менюха
				if( !IS_MOBILE && !CURRENT_GAME ){
					include_once TPL_DIR ."side_menu.php"; 
				}
				// игры нет, показываем каталог
				if( !CURRENT_GAME ){ 		
					include_once TPL_DIR ."catalogue.php"; 
				} 
				// активирована игра
				else {						
					switch( CURRENT_GAME ){
						case "aviator": 
							include_once TPL_DIR ."games/aviator.php"; 
							break; 
						case "chicken_road": 
							include_once TPL_DIR ."games/chicken.php";
							break; 
						case "mines": 
							include_once TPL_DIR ."games/mines.php";
							break;
					}
				}
			?>
		</div>
<?php 
	} 
?>