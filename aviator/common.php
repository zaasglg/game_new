<?php
	//
	// GAME DATA
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    
    // Подключаем функции конвертации валют
    require_once BASE_DIR . 'currency.php';
    
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
            if( $ex ){ 
                // Конвертируем баланс из национальной валюты в USD для игры
                $balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
                Users::GI()->edit(['uid'=>$ex['uid'], 'balance'=>$balance_usd]);
                $_SESSION['user'] = Users::GI()->get(['uid'=>$ex['uid']]); 
                $_SESSION['user']['real_name'] = $main_user['email']; 
            }
            else {
                // Создаем пользователя в игровой базе данных
                // Конвертируем баланс из национальной валюты в USD для игры
                $balance_usd = convertToUSD($main_user['deposit'], $main_user['country']);
                $new_user_data = [
                    'uid'=> UID,
                    'name'=> substr( $main_user['email'], 0, 1 ) .'...'. substr( $main_user['email'], ( strlen( $main_user['email'] )-1 ), 1 ), 
                    'real_name'=> substr( $main_user['email'], 0, 8 ),  
                    'host_id'=> AUTH, 
                    'balance'=> $balance_usd  
                ];
                $result = Users::GI()->add( $new_user_data ); 
                $_SESSION['user'] = isset( $result['data'] ) ? $result['data'] : $new_user_data;
                $_SESSION['user']['real_name'] = $main_user['email']; 
            }
        }
    } 
    else {
        // Демо режим или пользователь не авторизован
        if( !isset( $_SESSION['user']['uid'] ) ){ 
            $_SESSION['USER_RATE'] = 1; 
            $_SESSION['aviator_demo'] = 500; // Демо баланс
            $_SESSION['user'] = [
                'uid' => UID,
                'name' => 'Demo Player',
                'real_name' => 'Demo Player',
                'balance' => 500,
                'host_id' => 0
            ];
        }
    } 







