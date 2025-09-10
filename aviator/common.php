<?php
	//
	// GAME DATA
	//
	define('HOST_ID', ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : 'demo' )); 
    
    // Подключаем функции конвертации валют
    require_once BASE_DIR . 'currency.php';
    
    error_log("Common.php - HOST_ID: " . HOST_ID . ", AUTH: " . AUTH . ", UID: " . UID);
    
    // Отладочная информация
    $debug_file = BASE_DIR . 'debug.log';
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - HOST_ID: " . HOST_ID . ", AUTH: " . AUTH . ", UID: " . UID . "\n", FILE_APPEND);
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - _GET: " . json_encode($_GET) . "\n", FILE_APPEND);
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - _REQUEST: " . json_encode($_REQUEST) . "\n", FILE_APPEND);
    
    // Создаем или получаем пользователя для игры
    if( HOST_ID != 'demo' && AUTH ){ 
        file_put_contents('/tmp/aviator_debug.log', date('Y-m-d H:i:s') . " - Trying to connect to main DB for user: " . AUTH . "\n", FILE_APPEND);
        
        // Проверяем подключение к основной базе данных
        try {
            $db2_instance = DB2::GI();
            file_put_contents('/tmp/aviator_debug.log', date('Y-m-d H:i:s') . " - DB2 connection successful\n", FILE_APPEND);
            
            // Получаем пользователя из основной базы данных
            $Q = "SELECT * FROM `users` WHERE `user_id`='". AUTH ."'";
            $main_user = $db2_instance->get($Q);
            file_put_contents('/tmp/aviator_debug.log', date('Y-m-d H:i:s') . " - Main user query result: " . json_encode($main_user) . "\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('/tmp/aviator_debug.log', date('Y-m-d H:i:s') . " - DB2 connection failed: " . $e->getMessage() . "\n", FILE_APPEND);
            $main_user = false;
        } 
        
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







