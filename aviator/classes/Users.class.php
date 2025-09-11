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
		public function active( $d=[] ){
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
				WHERE u.`uid`<>'". UID ."' 
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
				// Обновляем баланс в локальной базе (в USD)
				$balance = $this->dbo->getField("balance", self::$table, $cond);
				$amount = isset( $d['amount'] ) ? (float)$d['amount'] : 0; 
				$new_balance = $balance + $amount;
				$this->dbo->upd( TABLE_USERS, ['balance'=>$new_balance], $cond ); 

				// Получаем host_id пользователя для обновления основной базы
				$user = $this->get(['uid' => $uid]);
				if( $user && $user['host_id'] > 0 ){
					// Получаем страну пользователя для конвертации валюты
					$user_data = DB2::GI()->get("SELECT country FROM users WHERE user_id = ?", [$user['host_id']]);
					if( $user_data ){
						// Подключаем функции конвертации валют
						require_once BASE_DIR . 'currency.php';
						
						// Конвертируем баланс из долларов в национальную валюту
						$new_balance_national = $new_balance * getCurrencyRate($user_data['country']);
						
						// Обновляем баланс в основной базе данных (в национальной валюте)
						DB2::GI()->upd('users', ['deposit' => $new_balance_national], ['user_id' => $user['host_id']]);
					}
				}

				return $new_balance;
			}
			return false;
		}
//
//=================================== 
		public function balance( $d=[] ){ 
			error_log("Users::balance called - UID: " . UID . ", AUTH: " . AUTH);
			if( isset( $_SESSION['aviator_demo'] ) ){
				$balance = $_SESSION['aviator_demo']; 
				error_log("Demo mode - balance: " . $balance);
			}
			else {
				// Синхронизируем баланс с основной базой данных
				if( AUTH ){
					try {
						// Получаем баланс и страну из основной базы данных
						$user_data = DB2::GI()->get("SELECT deposit, country FROM users WHERE user_id = ?", [AUTH]);
						error_log("Main DB user data: " . json_encode($user_data));
						if( $user_data ){
							// Подключаем функции конвертации валют
							require_once BASE_DIR . 'currency.php';
							
							// Конвертируем баланс из национальной валюты в доллары для игры
							$balance_national = (float)$user_data['deposit'];
							$balance = convertToUSD($balance_national, $user_data['country']);
							error_log("Converted balance: " . $balance_national . " -> " . $balance);
							
							// Обновляем баланс в игровой базе данных
							$this->edit(['uid' => UID, 'balance' => $balance]);
							
							// Обновляем сессию
							$_SESSION['user']['balance'] = $balance;
						} else {
							$balance = $this->dbo->getField( "balance", TABLE_USERS, ['uid'=>UID] ); 
							error_log("No main DB user, local balance: " . $balance);
						}
					} catch (Exception $e) {
						$balance = $this->dbo->getField( "balance", TABLE_USERS, ['uid'=>UID] ); 
						error_log("Exception in balance sync: " . $e->getMessage() . ", local balance: " . $balance);
					}
				} else {
					$balance = $this->dbo->getField( "balance", TABLE_USERS, ['uid'=>UID] ); 
					error_log("No AUTH, local balance: " . $balance);
				}
				
				// Убеждаемся, что баланс обновлен в сессии
				if( !isset($_SESSION['user']['balance']) || $_SESSION['user']['balance'] != $balance ){
					$_SESSION['user']['balance'] = $balance;
				}
			}
			error_log("Final balance returned: " . $balance);
			return $balance; 
		}

//
//
//
//=================================== 

//
//=================================== 
	}
//
//
//