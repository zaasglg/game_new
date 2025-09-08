<?php
	class Users extends Singleton {  
		protected $dbo; 
		public static $table = TABLE_USERS; 
		protected $id; 
		protected $uid; 
		protected $host_id; 
		protected $name; 
		protected $real_name;
		protected $img; 
		protected $balance; 
		protected $status; 
		protected $date; 
		protected $basic_balance; 
//	
//===================================
		public function __construct( $d=[] ){
			$this->dbo = DBO::getInstance();  
			$this->basic_balance = 500; 
		}
//		
//-----------------------------------------------------
		public static function getInstance( $c=null, $name="", $params=[] ){
            return parent::getInstance( $c ? $c : __CLASS__ );
        }
//
//===================================
        public function exists( $d=[] ){
            $cond = [];
            foreach( $d as $k=>$v ){ $cond[] = "`". $k ."`='". $v ."'"; }
            if( $cond ){ 
                $Q = "SELECT `id` 
                        FROM `". self::$table ."` 
                        WHERE ". implode(" AND ", $cond);
                $ex = $this->dbo->get($Q);
                return $ex && isset( $ex['id'] ) ? $ex['id'] : false;  
            } 
            return false;
        }
//
//===================================
		public static function logout(){
			foreach( $_SESSION as $k=>$v ){ unset( $_SESSION[ $k ] ); }
			$_SESSION = [];
			header('Location: /');
			exit();
		}
//
//===================================
		public function add( $d=[] ){
			$uid = ""; 
			while( !$uid ){
				$uid = substr( md5("NEW_USER_". time()), 0, 8 ); 
				if( $this->exists(['uid'=>$uid]) ){ $uid = ""; }
			} 

			$data = array(
				'uid'=> $uid, 
				'host_id'=> isset( $d['host_id'] ) ? (int)$d['host_id'] : 0, 
				'name'=> isset( $d['name'] ) ? App::text( $d['name'] ) : substr( $uid, 0, 1 ) .'...'. substr( $uid, 6, 1 ),
				'real_name'=> isset( $d['real_name'] ) ? App::text( $d['real_name'] ) : '', 
				'img'=> rand(1,70),
				'balance'=> isset( $d['balance'] ) ? $d['balance'] : $this->basic_balance, 
				'status'=> isset( $d['status'] ) ? (int)$d['status'] : 2
			);
			$ins = $this->dbo->ins( self::$table, $data ); 

			//App::activity( array( 'uid'=>UID, 'action'=>5, 'object'=>$uid ) );

			return $ins ? 
				['success'=>$ins, 'data'=>$this->get(['uid'=>$uid])] : 
				['error'=>1, 'msg'=>"DB error"]; 
		}
//
//===================================
		public function get( $d=[] ){ 
			$cond = []; 
			$uid = isset( $d['uid'] ) ? App::uid( $d['uid'] ) : ''; 
			if( $uid ){ $cond[] = "u.`uid`='". $uid ."'"; } 
			$host_id = isset( $d['host_id'] ) ? (int)$d['host_id'] : ''; 
			if( $host_id ){ $cond[] = "u.`host_id`='". $host_id ."'"; } 

            $Q = "SELECT u.*, 
						COALESCE(
							(
								SELECT COUNT(*) 
									FROM `bets` 
									WHERE `user`=u.`uid` 
										AND `status` NOT IN (3,5) 
							), 0 
						) AS 'bets' 
					FROM `". self::$table ."` AS u ". 
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ." 
                    ORDER BY u.`id` DESC  
					LIMIT 1"; 
			$user = $this->dbo->get( $Q ); 

			return $user; 
		}
//
//===================================
		public function edit( $d=[] ){ 
			$cond = []; 
			$uid = isset( $d['uid'] ) ? App::uid( $d['uid'] ) : ''; 
			if( $uid ){ $cond['uid'] = $uid; } 
			$id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
			if( $id ){ $cond['id'] = $id; }
			$user = $this->get( $cond ); 

			if( $user ){ 
				$data = []; 
				$name = isset( $d['name'] ) ?  App::text( $d['name'] ) : "";
				if( $name ){ $data['name'] = $name; } 
				$img = isset( $d['img'] ) ? App::text ($d['img'] ) : ''; 
				if( $img ){ $data['img'] = $img; } 
				$status = isset( $d['status'] ) ? (int)$d['status'] : 0; 
				if( $status ){ $data['status'] = $status; }
				$balance = isset( $d['balance'] ) ? (float)$d['balance'] : 0; 
				if( isset( $d['balance'] )  ){ $data['balance'] = $balance; }
				if( $data ){
					$upd = $this->dbo->upd( self::$table, $data, $cond );  
					return ['success'=>1, 'result'=>$upd, 'data'=>$data]; 
				}
				else {
					return ['error'=>1, 'msg'=>"Nothing to change"];
				}
			}
			else {
				return ['error'=>1, 'msg'=>"User not found"]; 
			}
		}
//
//=================================== 
		public function load( $d=[] ){
			$cond = []; 

            $id = isset( $d['id'] ) ? preg_replace( '/[^\d\,]/', '', $d['id'] ) : ''; 
            if( $id ){ $cond[] = "u.`id` IN (". $id .")"; } 

            $uid = isset( $d['uid'] ) ? App::uid($d['uid']) : ''; 
            if( $uid ){ $cond[] = "u.`uid`='". $uid ."'"; }

            $name = isset( $d['name'] ) ? App::text( $d['name'] ) : ''; 
            if( $name ){ $cond[] = "u.`name` LIKE '%". $name ."%'"; }

            $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/','',$d['status']) : 0; 
            if( $status ){ $cond[] = "u.`status` IN (". $status .")"; } 
            else { $cond[] = "u.`status` NOT IN(5)"; }

            $sort = isset( $d['sort'] ) ? preg_replace( '/[^A-za-z0-9\-\_]/', '', $d['sort'] ) : ''; 
            $dir = isset( $d['dir'] ) ? App::uid( $d['dir'] ) : "";
            $sortBy = ( $sort && $dir ? ( "u.`". $sort ."` ". $dir ) : "u.`id` DESC" ); 

            $page = isset( $d['page'] ) ? (int)$d['page'] : 1; 
            $length = isset( $d['length'] ) ? (int)$d['length'] : BASIC_LENGTH; 
            $limit = $page > 1 ? ( "". ( ( $page - 1 ) * $length ) .",". $length ) : $length; 

			$Q = "SELECT u.*, 
						COALESCE(
							(
								SELECT COUNT(*) 
									FROM `bets` 
									WHERE `user`=u.`uid` 
										AND `status` NOT IN (3,5) 
							), 0 
						) AS 'bets' 
					FROM `". self::$table ."` AS u ". 
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ." 
                    ORDER BY ". $sortBy ." 
					LIMIT ". $limit; 
			$users = $this->dbo->load( $Q ); 

			return $users;
		}
//
//=================================== 
		public function active( $d=[]){
			$Q = "SELECT u.*, 
					COALESCE(
						(
							SELECT COUNT(*) 
								FROM `bets` 
								WHERE `user`=u.`uid` 
									AND `status` NOT IN (3,5) 
						), 0 
					) AS 'bets' 
				FROM `". self::$table ."` AS u 
				WHERE u.`uid`<>'". $_SESSION['user']['uid'] ."' 
					AND u.`status` NOT IN (5) 
				ORDER BY u.`id` DESC 
				LIMIT 50"; 
			$users = $this->dbo->load( $Q ); 

			return $users;
		}
//
//=================================== 
		public function charge( $d=[] ){
			$cond = []; 
			$uid = isset( $d['uid'] ) ? App::uid( $d['uid'] ) : ''; 
			if( $uid ){ $cond['uid'] = $uid; } 
			if( $cond ){
				// Обновляем баланс в локальной базе
				$balance = $this->dbo->getField("balance", self::$table, $cond);
				$amount = isset( $d['amount'] ) ? (float)$d['amount'] : 0; 
				$new_balance = $balance + $amount;
				$this->dbo->upd( self::$table, ['balance'=>$new_balance], $cond ); 

				// Получаем host_id пользователя для обновления основной базы
				$user = $this->get(['uid' => $uid]);
				if( $user && $user['host_id'] > 0 ){
					// Получаем страну пользователя для конвертации валюты
					$user_data = DB2::getInstance()->get("SELECT country FROM users WHERE user_id = ?", [$user['host_id']]);
					if( $user_data ){
						// Подключаем функции конвертации валют
						require_once BASE_DIR . 'currency.php';
						
						// Конвертируем баланс из долларов в национальную валюту
						$new_balance_national = $new_balance * getCurrencyRate($user_data['country']);
						
						// Обновляем баланс в основной базе данных volurgame (в национальной валюте)
						DB2::getInstance()->upd('users', ['deposit' => $new_balance_national], ['user_id' => $user['host_id']]);
					}
				}

				return $new_balance;
			}
			return false;
		}
//
//=================================== 
		public function balance( $d=[] ){
			$balance = $this->dbo->getField( "balance", self::$table, ['uid'=>UID] ); 
			$_SESSION['user']['balance'] = $balance; 
			return $balance; 
		}
//
//=================================== 
	public function updateBalance( $d=[] ){
		$user_id = isset( $d['user_id'] ) ? (int)$d['user_id'] : 0;
		$new_balance_usd = isset( $d['balance'] ) ? (float)$d['balance'] : 0;
		
		// Если user_id = 0 или не указан, это демо режим
		if( !$user_id ){
			return [
				'success' => 1, 
				'balance' => $new_balance_usd,
				'balance_national' => $new_balance_usd,
				'user_id' => 0,
				'country' => '',
				'demo_mode' => true
			];
		}
		
		try {
			// Получаем страну пользователя для конвертации валюты
			$user_data = DB2::getInstance()->get("SELECT country FROM users WHERE user_id = ?", [$user_id]);
			if( !$user_data ){
				return ['error'=>1, 'msg'=>'User not found in main database'];
			}
			
			// Подключаем функции конвертации валют
			require_once BASE_DIR . 'currency.php';
			
			// Конвертируем баланс из долларов в национальную валюту
			$new_balance_national = $new_balance_usd * getCurrencyRate($user_data['country']);
			
			// Обновляем баланс в основной базе данных volurgame
			$update_result = DB2::getInstance()->upd('users', ['deposit' => $new_balance_national], ['user_id' => $user_id]);
		} catch (Exception $e) {
			return ['error'=>1, 'msg'=>'Database error: ' . $e->getMessage()];
		}
			
			// Также обновляем в локальной базе, если пользователь существует
			$local_user = $this->get(['host_id' => $user_id]);
			if( $local_user ){
				$this->edit([
					'uid' => $local_user['uid'],
					'balance' => $new_balance_usd
				]);
			}
			
			if( $update_result ){
				$_SESSION['user']['balance'] = $new_balance_usd;
				return [
					'success' => 1, 
					'balance' => $new_balance_usd,
					'balance_national' => $new_balance_national,
					'user_id' => $user_id,
					'country' => $user_data['country']
				];
			} else {
				return ['error'=>1, 'msg'=>'Failed to update balance'];
			}
		}
//
//=================================== 
	public function get_user_balance( $d=[] ){
		$user_id = isset( $d['user_id'] ) ? (int)$d['user_id'] : 0;
		
		// Если user_id = 0 или не указан, это демо режим
		if( !$user_id ){
			return [
				'success' => 1, 
				'balance' => 500,
				'user_id' => 0,
				'country' => '',
				'balance_national' => 500,
				'demo_mode' => true
			];
		}
		
		try {
			// Получаем баланс и страну из основной базы данных volurgame
			$user_data = DB2::getInstance()->get("SELECT deposit, country FROM users WHERE user_id = ?", [$user_id]);
			
			if( !$user_data ){
				return ['error'=>1, 'msg'=>'User not found in main database'];
			}
			
			// Подключаем функции конвертации валют
			require_once BASE_DIR . 'currency.php';
			
			// Конвертируем баланс из национальной валюты в доллары для отображения в игре
			$balance_national = (float)$user_data['deposit'];
			$balance_usd = convertToUSD($balance_national, $user_data['country']);
		} catch (Exception $e) {
			return ['error'=>1, 'msg'=>'Database error: ' . $e->getMessage()];
		}
			
			return [
				'success' => 1, 
				'balance' => $balance_usd,
				'user_id' => $user_id,
				'country' => $user_data['country'],
				'balance_national' => $balance_national
			];
		}
//
//=================================== 
	public function save_game_result( $d=[] ){
		$user_id = isset( $d['user_id'] ) ? (int)$d['user_id'] : 0;
		$new_balance_usd = isset( $d['balance'] ) ? (float)$d['balance'] : 0; // Баланс в долларах из игры
		$bet_amount = isset( $d['bet_amount'] ) ? (float)$d['bet_amount'] : 0;
		$win_amount = isset( $d['win_amount'] ) ? (float)$d['win_amount'] : 0;
		$game_result = isset( $d['game_result'] ) ? $d['game_result'] : 'lose'; // 'win' or 'lose'
		
		// Если user_id = 0 или не указан, это демо режим
		if( !$user_id ){
			return [
				'success' => 1, 
				'balance' => $new_balance_usd,
				'balance_national' => $new_balance_usd,
				'user_id' => 0,
				'country' => '',
				'game_result' => $game_result,
				'bet_amount' => $bet_amount,
				'win_amount' => $win_amount,
				'demo_mode' => true
			];
		}
		
		try {
			// Получаем страну пользователя для конвертации валюты
			$user_data = DB2::getInstance()->get("SELECT country FROM users WHERE user_id = ?", [$user_id]);
			if( !$user_data ){
				return ['error'=>1, 'msg'=>'User not found in main database'];
			}
			
			// Подключаем функции конвертации валют
			require_once BASE_DIR . 'currency.php';
			
			// Конвертируем баланс из долларов в национальную валюту
			$new_balance_national = $new_balance_usd * getCurrencyRate($user_data['country']);
			
			// Обновляем баланс в основной базе данных volurgame (в национальной валюте)
			$update_result = DB2::getInstance()->upd('users', ['deposit' => $new_balance_national], ['user_id' => $user_id]);
		} catch (Exception $e) {
			return ['error'=>1, 'msg'=>'Database error: ' . $e->getMessage()];
		}
			
			// Также обновляем в локальной базе, если пользователь существует (в долларах)
			$local_user = $this->get(['host_id' => $user_id]);
			if( $local_user ){
				$this->edit([
					'uid' => $local_user['uid'],
					'balance' => $new_balance_usd
				]);
			}
			
			if( $update_result ){
				$_SESSION['user']['balance'] = $new_balance_usd;
				return [
					'success' => 1, 
					'balance' => $new_balance_usd, // Возвращаем баланс в долларах для игры
					'balance_national' => $new_balance_national,
					'user_id' => $user_id,
					'country' => $user_data['country'],
					'game_result' => $game_result,
					'bet_amount' => $bet_amount,
					'win_amount' => $win_amount
				];
			} else {
				return ['error'=>1, 'msg'=>'Failed to update balance'];
			}
		}
//
//=================================== 

//
//=================================== 
	}
//
//
//