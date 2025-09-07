<?php 
	$post = $_REQUEST; 
	$input = json_decode( file_get_contents('php://input'), 1, 1024 ); 
	$return = array('error'=>1, 'msg'=>"UNAUTHORIZED"); 

	$fields = isset( $post['fields'] ) ? $post['fields'] : false; 
	$cond = isset( $post['cond'] ) ? $post['cond'] : false; 

	//var_dump( $input );

	$CLASS = "";
//
// VARIABLES
//
	function cmp_function($a, $b, $c){ return ($a[$c] > $b[$c]); } // ASC  uasort($array, 'cmp_function');
	function cmp_function_desc($a, $b, $c){ return ($a[$c] < $b[$c]); } // DESC  uasort($array, 'cmp_function_desc');

	switch( CONTROLLER ){ 
		case "bets": $CLASS = Bets::getInstance(); break; 
		case "cfs": $CLASS = Cfs::getInstance(); break; 
		case "games": $CLASS = Games::getInstance(); break; 
		case "users": $CLASS = Users::getInstance(); break; 
		case "telegram": 
			require_once BASE_DIR . 'telegram_notify.php';
			break;
		case "settings": 
			if( isset( $post['play_sounds'] ) ){ $_SESSION['play_sounds'] = $post['play_sounds']; } 
			if( isset( $post['play_music'] ) ){ $_SESSION['play_music'] = $post['play_music']; }
			$return = ['res'=>['sound'=>$_SESSION['play_sounds'], 'music'=>$_SESSION['play_sounds']], 'data'=>$post];
			break; 
	}

	switch( ACTION ){
		case "add": $return = $CLASS->add( $post ); break; 
		case "edit": $return = $CLASS->edit( $post ); break; 
		case "load": $return = $CLASS->load( $post ); break; 
		case "get": $return = $CLASS->get( $post ); break;  
		// users 
		case "auth": $return = $CLASS->auth( $post ); break;
		case "reg": $return = $CLASS->reg( $post ); break; 
		case "balance": $return = $CLASS->balance( $post ); break; 
		// cf
		case "next": $return = $CLASS->next( $post ); break;
		case "bulk": $return = $CLASS->bulk( $post ); break;  
		case "current": $return = $CLASS->current( $post ); break;  
		// games  
		case "search": $return = $CLASS->search( $post ); break; 
		case "history": $return = $CLASS->history( $post ); break; 
		case "close": $return = $CLASS->close( $post ); break; 
		// bets 
		case "move": $return = $CLASS->move( $post ); break; 
		case "fire": $return = $CLASS->fire( $post ); break;
		// game results
		case "save_game_result": $return = $CLASS->save_game_result( $post ); break;
		case "get_user_balance": $return = $CLASS->get_user_balance( $post ); break; 
		// telegram notifications
		case "notify_registration": 
			$result = sendTelegramNotification('registration', $input);
			$return = ['success' => $result ? 1 : 0, 'sent' => $result];
			break;
		case "notify_first_game": 
			$result = sendTelegramNotification('first_game', $input);
			$return = ['success' => $result ? 1 : 0, 'sent' => $result];
			break;
		case "notify_big_win": 
			$result = sendTelegramNotification('big_win', $input);
			$return = ['success' => $result ? 1 : 0, 'sent' => $result];
			break;
		case "test_telegram": 
			$result = sendTelegramNotification('test', []);
			$return = ['success' => $result ? 1 : 0, 'sent' => $result];
			break;
	}

	echo json_encode( $return );
	exit(); 



