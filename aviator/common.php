<?php
	//
	// GAME DATA
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    
    // Подключаем функции конвертации валют
    require_once BASE_DIR . 'currency.php';
    
    error_log("Common.php - HOST_ID: " . HOST_ID . ", AUTH: " . AUTH . ", UID: " . UID);
    
    // Создаем или получаем пользователя для игры
    if( HOST_ID != 'demo' && AUTH ){ 
        // Получаем пользователя из основной базы данных
        $Q = "SELECT * FROM `users` WHERE `user_id`='". AUTH ."'";
        $main_user = DB2::GI()->get($Q); 
        
        if( isset( $main_user['user_id'] ) && $main_user['user_id'] ){ 
            // Используем новую систему конвертации валют
            $_SESSION['USER_RATE'] = getCurrencyRate($main_user['country']); 
            
            // Проверяем, есть ли пользователь в игровой базе данных
            $ex = Users::GI()->get(['host_id'=>(int)AUTH]); 
            error_log("Looking for user with host_id: " . AUTH . ", found: " . json_encode($ex));
            
            if( $ex ){ 
                // Конвертируем баланс из национальной валюты в USD для игры
                $balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
                Users::GI()->edit(['uid'=>$ex['uid'], 'balance'=>$balance_usd]);
                $_SESSION['user'] = Users::GI()->get(['uid'=>$ex['uid']]); 
                $_SESSION['user']['real_name'] = $main_user['email']; 
                error_log("Updated existing user: " . json_encode($_SESSION['user']));
            }
            else {
                // Создаем пользователя в игровой базе данных
                // Конвертируем баланс из национальной валюты в USD для игры
                $balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
                $new_user_data = [
                    'name'=> substr( $main_user['email'], 0, 1 ) .'...'. substr( $main_user['email'], ( strlen( $main_user['email'] )-1 ), 1 ), 
                    'real_name'=> substr( $main_user['email'], 0, 8 ),  
                    'host_id'=> (int)AUTH, 
                    'balance'=> $balance_usd  
                ];
                error_log("Creating new user with data: " . json_encode($new_user_data));
                $result = Users::GI()->add( $new_user_data ); 
                error_log("User creation result: " . json_encode($result));
                $_SESSION['user'] = isset( $result['data'] ) ? $result['data'] : $new_user_data;
                $_SESSION['user']['real_name'] = $main_user['email']; 
            }
        }
    } 
    else {
        // Демо режим или пользователь не авторизован
        error_log("Demo mode activated");
        $_SESSION['USER_RATE'] = 1; 
        $_SESSION['aviator_demo'] = 500; // Демо баланс
        $_SESSION['user'] = [
            'uid' => UID,
            'name' => 'Demo Player',
            'real_name' => 'Demo Player',
            'balance' => 500,
            'host_id' => 0
        ];
        error_log("Demo user created: " . json_encode($_SESSION['user']));
    }
    
    // Убеждаемся, что баланс всегда установлен
    if( !isset($_SESSION['user']['balance']) || $_SESSION['user']['balance'] === null ){
        if( isset($_SESSION['aviator_demo']) ){
            $_SESSION['user']['balance'] = $_SESSION['aviator_demo'];
        } else {
            $_SESSION['user']['balance'] = Users::GI()->balance();
        }
        error_log("Balance fixed: " . $_SESSION['user']['balance']);
    }
    
    error_log("Final user session: " . json_encode($_SESSION['user'])); 







