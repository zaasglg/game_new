<?php
	//
	// GAME DATA
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    
    // Подключаем функции конвертации валют
    require_once BASE_DIR . 'currency.php';
    
    //if( !isset( $_SESSION['user']['uid'] ) ){ 
    	if( HOST_ID != 'demo' ){ 
    		$Q = "SELECT * 
    			FROM `users` 
    			WHERE `user_id`='". HOST_ID ."'";
    		$main_user = DB2::GI()->get($Q); 
    		if( isset( $main_user['user_id'] ) && $main_user['user_id'] ){ 
    			// Используем новую систему конвертации валют
    			$_SESSION['USER_RATE'] = getCurrencyRate($main_user['country']); 
    			$ex = Users::GI()->get(['host_id'=>(int)HOST_ID]); 
    			if( $ex ){ 
    				// Конвертируем баланс из национальной валюты в USD для игры
    				$balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
    				Users::GI()->edit(['uid'=>$ex['uid'], 'balance'=>$balance_usd]);
    				$_SESSION['user'] = Users::GI()->get(['uid'=>$ex['uid']]); 
    				$_SESSION['user']['real_name'] = $main_user['email']; 
    			}
    			else {
    				// Конвертируем баланс из национальной валюты в USD для игры
    				$balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
    				$new_user = [
			    		'name'=> substr( $main_user['email'], 0, 1 ) .'...'. substr( $main_user['email'], ( strlen( $main_user['email'] )-1 ), 1 ), 
			    		'real_name'=> substr( $main_user['email'], 0, 8 ),  
			    		'host_id'=> HOST_ID, 
			    		'balance'=> $balance_usd  
			    	];
			    	$_SESSION['user'] = Users::GI()->add( $new_user ); 
			    	$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
			    	$_SESSION['user']['real_name'] = $main_user['email']; 
    			}
    			$_SESSION['AUTH'] = $main_user['user_id'];
    		}
    		else {
    			if( !isset( $_SESSION['user']['uid'] ) && ( isset( $_SESSION['user']['host_id'] ) && $_SESSION['user']['host_id'] ) ){ 
	    			$_SESSION['USER_RATE'] = 1; 
	    			$_SESSION['user'] = Users::GI()->add([]); 
	    			$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
	    			$_SESSION['user']['real_name'] = $_SESSION['user']['name']; 
	    			$_SESSION['AUTH'] = false; 
	    		}
    		}
    	} 
    	else {
    		if( !isset( $_SESSION['user']['uid'] ) || ( isset( $_SESSION['user']['host_id'] ) && $_SESSION['user']['host_id'] ) ){ 
	    		$_SESSION['USER_RATE'] = 1; 
	    		$_SESSION['user'] = Users::GI()->add([]); 
	    		$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
	    		$_SESSION['user']['real_name'] = $_SESSION['user']['name']; 
	    		$_SESSION['AUTH'] = false; 
	    	}
    	}
    //} 
    //define('USER_RATE', $_SESSION['USER_RATE']); 







