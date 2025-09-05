<?php
	//
	// GAME DATA - SIMPLIFIED VERSION
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    $_SESSION['USER_RATE'] = 1;
    
    // Проверяем, есть ли пользователь в локальной базе
    if( HOST_ID != 'demo' ){ 
    	// Пытаемся найти пользователя по host_id
    	$ex = Users::GI()->get(['host_id'=>(int)HOST_ID]); 
    	if( $ex ){ 
    		$_SESSION['user'] = $ex;
    	} else {
    		// Создаем нового пользователя с начальным балансом
    		$new_user = [
	    		'name'=> 'User_' . substr(HOST_ID, -4), 
	    		'real_name'=> 'User_' . substr(HOST_ID, -4),  
	    		'host_id'=> HOST_ID, 
	    		'balance'=> 1000  // Устанавливаем тестовый баланс
	    	];
    		$_SESSION['user'] = Users::GI()->add( $new_user );
    		$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
    	}
    	$_SESSION['AUTH'] = HOST_ID; 
    } 
    else {
    	// Демо режим
    	if( !isset( $_SESSION['user']['uid'] ) ){ 
	    	$_SESSION['user'] = Users::GI()->add([]); 
	    	$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
	    	$_SESSION['user']['real_name'] = $_SESSION['user']['name']; 
	    	$_SESSION['AUTH'] = false; 
	    }
    }
    
    // Устанавливаем имя пользователя если не задано
    if( !isset($_SESSION['user']['real_name']) ){
    	$_SESSION['user']['real_name'] = $_SESSION['user']['name'];
    }
?>
