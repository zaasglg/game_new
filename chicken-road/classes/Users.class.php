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
				$balance = $this->dbo->getField("balance", self::$table, $cond);
				$amount = isset( $d['amount'] ) ? (float)$d['amount'] : 0; 
				$this->dbo->upd( self::$table, ['balance'=>($balance+$amount)], $cond ); 
				$balance = $this->dbo->getField("balance", self::$table, $cond); 

				if( AUTH ){ 
					$host_balance = ( $balance * $_SESSION['USER_RATE'] ); 
					DB2::GI()->upd('users', ['deposit'=>$host_balance], ['user_id'=>AUTH]); 
				}

				return $balance;
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

//
//=================================== 
	}
//
//
//