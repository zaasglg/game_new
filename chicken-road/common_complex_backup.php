<?php
	//
	// GAME DATA
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    $rates = [
        'Argentina'=> 1, 
        'Colombia'=> 1, 
        'Ecuador'=> 1 
    ];
    
    // Инициализируем переменные по умолчанию
    $_SESSION['USER_RATE'] = 1;
    
    if( HOST_ID != 'demo' ){ 
    	// Получаем данные пользователя из основной системы
    	$user_data_url = 'http://127.0.0.1:8000/get_user_data.php';
    	if(function_exists('curl_init')) {
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $user_data_url);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    		curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
    		$response = curl_exec($ch);
    		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    		curl_close($ch);
    		
    		// Отладочная информация
    		file_put_contents('debug.log', "HOST_ID: " . HOST_ID . "\n", FILE_APPEND);
    		file_put_contents('debug.log', "Response: " . $response . "\n", FILE_APPEND);
    		file_put_contents('debug.log', "HTTP Code: " . $http_code . "\n", FILE_APPEND);
    		
    		if($http_code === 200 && $response) {
    			$user_data = json_decode($response, true);
    			file_put_contents('debug.log', "User data: " . print_r($user_data, true) . "\n", FILE_APPEND);
    			
    			if($user_data && $user_data['success'] && $user_data['data']['is_auth']) {
    				// Пользователь авторизован
    				$balance = $user_data['data']['balance'];
    				$country = $user_data['data']['country'];
    				$_SESSION['USER_RATE'] = isset( $rates[ $country ] ) ? $rates[ $country ] : 1;
    				
    				file_put_contents('debug.log', "Real balance: " . $balance . "\n", FILE_APPEND);
    				
    				$ex = Users::GI()->get(['host_id'=>(int)HOST_ID]); 
		    		if( $ex ){ 
		    			// Обновляем баланс
		    			Users::GI()->edit(['uid'=>$ex['uid'], 'balance'=>($balance / $_SESSION['USER_RATE'])]);
		    			$_SESSION['user'] = Users::GI()->get(['uid'=>$ex['uid']]); 
		    		}
		    		else {
		    			// Создаем нового пользователя
		    			$new_user = [
				    		'name'=> 'User_' . substr(HOST_ID, -4), 
				    		'real_name'=> 'User_' . substr(HOST_ID, -4),  
				    		'host_id'=> HOST_ID, 
				    		'balance'=> $balance / $_SESSION['USER_RATE']  
				    	];
			    		$_SESSION['user'] = Users::GI()->add( $new_user );
			    		$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
		    		}
		    		$_SESSION['AUTH'] = HOST_ID;
		    		$_SESSION['user']['real_name'] = 'User_' . substr(HOST_ID, -4);
		    		
    			} else {
    				file_put_contents('debug.log', "Not authenticated, using demo\n", FILE_APPEND);
    				// Демо режим
    				if( !isset( $_SESSION['user']['uid'] ) ){ 
			    		$_SESSION['user'] = Users::GI()->add([]); 
			    		$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
			    		$_SESSION['user']['real_name'] = $_SESSION['user']['name']; 
			    		$_SESSION['AUTH'] = false; 
			    	}
    			}
    		} else {
    			file_put_contents('debug.log', "Failed to get user data, HTTP: " . $http_code . "\n", FILE_APPEND);
    			// Ошибка получения данных, создаем пользователя с ID
    			if( !isset( $_SESSION['user']['uid'] ) ){ 
		    		$ex = Users::GI()->get(['host_id'=>(int)HOST_ID]); 
		    		if( $ex ){ 
		    			$_SESSION['user'] = $ex;
		    		} else {
		    			$new_user = [
				    		'name'=> 'User_' . substr(HOST_ID, -4), 
				    		'real_name'=> 'User_' . substr(HOST_ID, -4),  
				    		'host_id'=> HOST_ID, 
				    		'balance'=> 500  
				    	];
			    		$_SESSION['user'] = Users::GI()->add( $new_user );
			    		$_SESSION['user'] = isset( $_SESSION['user']['data'] ) ? $_SESSION['user']['data'] : [];
		    		}
		    		$_SESSION['AUTH'] = HOST_ID; 
		    	}
    		}
    	}
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
