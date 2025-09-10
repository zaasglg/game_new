<?php
	class Bets extends Singleton {         
        protected $dbo; 
        public static $table = TABLE_BETS; 
        protected $id; 
        protected $user; 
        protected $sid; 
        protected $bet; 
        protected $cf; 
        protected $result; 
        protected $game; 
        protected $type; 
        protected $src; 
        protected $status; 
//
//=====================================================
        protected function __construct(){
            @$this->dbo = DBO::getInstance();  
        }
//
//===================================================== 
        public static function getInstance( $c=null, $name="", $params=[] ){
            return parent::getInstance(__CLASS__);
        } 
//
//===================================================== 
        public function add( $d=[] ){
            $data=[
                'user'=> UID, 
                'sid'=> UID, 
                'bet'=> isset( $d['bet'] ) ? (float)$d['bet'] : 0, 
                'cf'=> isset( $d['cf'] ) ? (float)$d['cf'] : 0, 
                'result'=> isset( $d['result'] ) ? (float)$d['result'] : 0, 
                'game'=> isset( $d['game'] ) ? (int)$d['game'] : 0, 
                'type'=> isset( $d['type'] ) ? App::text( $d['type'] ) : 'manual', 
                'src'=> isset( $d['src'] ) ? (int)$d['src'] : 1, 
                'status'=> isset( $d['status'] ) ? (int)$d['status'] : 2 
            ]; 
            if( !$data['status'] ){ $data['status'] = 2; } 
            if( !$data['game'] ){ 
                $game = Games::GI()->search([]); 
                if( $game ){ 
                    $data['game'] = $game['id']; 
                    //$data['cf'] = $game['amount']; 
                }
            }

            if( isset( $_SESSION['aviator_demo'] ) ){
                $balance = $_SESSION['aviator_demo'];
            } else {
                // Получаем баланс из основной базы данных и конвертируем в игровую валюту (USD)
                if( AUTH ){
                    try {
                        $main_balance = DB2::GI()->getField('deposit', 'users', ['user_id' => AUTH]);
                        $user_rate = isset($_SESSION['USER_RATE']) ? $_SESSION['USER_RATE'] : 1;
                        $balance = $main_balance / $user_rate; // Конвертируем в USD для игры
                        
                        // Обновляем баланс в игровой базе данных
                        Users::GI()->edit(['uid' => UID, 'balance' => $balance]);
                    } catch (Exception $e) {
                        $balance = Users::GI()->balance();
                    }
                } else {
                    $balance = Users::GI()->balance();
                }
            } 

            if( $data['bet'] > $balance ){
                return ['error'=>1, 'msg'=>"Low balance"]; 
            }

            $res = $this->dbo->ins( self::$table, $data ); 

            if( $res ){
                if( isset( $_SESSION['aviator_demo'] ) ){
                    $_SESSION['aviator_demo'] -= $data['bet']; 
                } 
                else { 
                    Users::GI()->charge(['uid'=>UID, 'amount'=>-$data['bet']]); 
                }
            }

            $balance = Users::GI()->balance();

            return $res ? ['success'=>$res, 'data'=>$data, 'balance'=>$balance] : ['error'=>1, 'msg'=>"Unable to create new bet"];
        } 
//
//=====================================================
        public function close( $d=[] ){
            $cond = [];
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; }

            if( $cond ){ 
                $bet = $this->get($cond);
                if( $bet ){ 
                    $cf = isset( $d['cf'] ) ? (float)$d['cf'] : 0; 
                    if( $cf ){ 
                        $data['cf'] = $cf;  
                        $game = Games::GI()->get(['id'=>$bet['game']]);
                        if( $game ){ 
                            if( $cf && $cf <= $game['amount'] ){ 
                                if( isset( $_SESSION['aviator_demo'] ) ){
                                    $_SESSION['aviator_demo'] += ( $cf * $bet['bet'] );
                                } 
                                else { 
                                    $data['result'] = $cf * $bet['bet']; 
                                    Users::GI()->charge(['uid'=>$bet['user'], 'amount'=>$data['result']]);
                                }
                            } 
                            else {
                                //Users::GI()->charge(['uid'=>$bet['user'], 'amount'=>-$bet['bet']]);
                            }
                        } 
                    } 
                    else {
                        //Users::GI()->charge(['uid'=>$bet['user'], 'amount'=>-$bet['bet']]);
                    }
                    $data['status'] = 7; 
                    $res = $this->dbo->upd( self::$table, $data, $cond ); 
                    $balance = Users::GI()->balance();
                    return ['success'=>1, 'data'=>$data, 'result'=>$res, 'balance'=>$balance];
                }
                return ['error'=>1, 'msg'=>"Bet not found"];
            }
            return ['error'=>1, 'msg'=>'Wrong data format'];
        }
//
//===================================================== 
        public function edit( $d=[] ){ 
            $cond = []; 
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; } 

            if( $cond ){
                $bet = $this->get($cond); 
                if( $bet && $bet['status']==2 ){
                    $data = []; 
                    $amount = isset( $d['bet'] ) ? (float)$d['bet'] : 0; 
                    if( $amount ){ $data['bet'] = $amount; } 
                    $cf = isset( $d['cf'] ) ? (float)$d['cf'] : 0; 
                    if( $cf ){ $data['cf'] = $cf; } 
                    $result = isset( $d['result'] ) ? (float)$d['result'] : 0; 
                    if( $result ){ $data['result'] = $result; } 
                    $game = isset( $d['game'] ) ? (int)$d['game'] : 0; 
                    if( $game ){ $data['game'] = $game; } 
                    $type = isset( $d['type'] ) ? App::text( $d['type'] ) : ''; 
                    if( $type ){ $data['type'] = $type; } 
                    $src = isset( $d['src'] ) ? (int)$d['src'] : 0; 
                    if( $src ){ $data['src'] = $src; } 
                    $status = isset( $d['status'] ) ? (int)$d['status'] : 0; 
                    if( $status ){ $data['status'] = $status; }

                    if( $data ){
                        $res = $this->dbo->upd( self::$table, $data, $cond ); 
                        if( $res && $data['status'] == 5 ){ 
                            if( isset( $_SESSION['aviator_demo'] ) ){
                                $_SESSION['aviator_demo'] += $bet['bet']; 
                            }
                            else { 
                                Users::GI()->charge(['uid'=>UID, 'amount'=>$bet['bet']]); 
                            }
                        } 
                        $balance = Users::GI()->balance();
                        return ['success'=>1, 'data'=>$data, 'result'=>$res, 'balance'=>$balance]; 
                    } 
                    return ['error'=>1, 'msg'=>"Nothing to change"]; 
                }
                return ['error'=>1, 'msg'=>"Bet is closed"];
            }
            return ['error'=>1, 'msg'=>"Wrong data format"]; 
        }
//
//===================================================== 
        public function get( $d=[] ){ 
            $cond = []; 

            $id = isset( $d['id'] ) ? preg_replace('/[^\d\,]/', '', $d['id']) : ''; 
            if( $id ){ $cond[] = "b.`id` IN (". $id .")"; }
            
            $Q = "SELECT b.`id`, b.`user`, b.`sid`, b.`bet`, b.`cf`, b.`result`, 
                        b.`game`, b.`type`, b.`src`, b.`status`, b.`date`, 
                        COALESCE( u.`img`, 1 ) AS 'img', 
                        COALESCE(
                            (
                                SELECT COUNT(*) 
                                    FROM `games` 
                                    WHERE `id`=b.`game` 
                                        AND `status`=2 
                                        AND `finish` IS NULL 
                            ), 0 
                        ) AS 'active'    
                    FROM `". self::$table ."` AS b 
                    LEFT JOIN `users` AS u ON u.`uid`=b.`user`".
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ."
                    ORDER BY b.`id` DESC 
                    LIMIT 1";
            $bet = $this->dbo->get( $Q );
            return $bet;  
        }
//
//===================================================== 
        public function load( $d=[] ){ 
            $cond = []; 

            $user = isset( $d['user'] ) ? App::uid( $d['user'] ) : ''; 
            if( $user ){ $cond[] = "b.`user`='". $user ."'"; } 

            $result = isset( $d['result'] ) ? (float)$d['result'] : 0; 
            if( $result ){ $cond[] = "b.`result`='". $result ."'"; } 
            
            $game = isset( $d['game'] ) ? (int)$d['game'] : 0; 
            if( $game ){ $cond[] = "b.`game`='". $game ."'"; } 
            
            $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/', '', $d['status']) : 0; 
            if( $status ){ $cond[] = "b.`status` IN (". $status .")"; } 
            else { $cond[] = "b.`status` NOT IN (5)"; } 

            $sort = isset( $d['sort'] ) ? preg_replace( '/[^A-za-z0-9\-\_]/', '', $d['sort'] ) : ''; 
            $dir = isset( $d['dir'] ) ? App::uid( $d['dir'] ) : "";
            $sortBy = ( $sort && $dir ? ( "b.`". $sort ."` ". $dir ) : "b.`id` DESC" ); 

            $page = isset( $d['page'] ) ? (int)$d['page'] : 1; 
            $length = isset( $d['length'] ) ? (int)$d['length'] : 20; 
            $limit = $page > 1 ? ( "". ( ( $page - 1 ) * $length ) .",". $length ) : $length; 

            $Q = "SELECT b.`id`, b.`user`, b.`sid`, b.`bet`, b.`cf`, b.`result`, 
                        b.`game`, b.`type`, b.`src`, b.`status`, b.`date`, 
                        COALESCE( u.`img`, 1 ) AS 'img', COALESCE( u.`name`, '' ) AS 'name', 
                        COALESCE(
                            (
                                SELECT COUNT(*) 
                                    FROM `games` 
                                    WHERE `id`=b.`game` 
                                        AND `status`=2 
                                        AND `finish` IS NULL 
                            ), 0 
                        ) AS 'active'    
                    FROM `". self::$table ."` AS b 
                    LEFT JOIN `users` AS u ON u.`uid`=b.`user`".
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ."
                    ORDER BY ". $sortBy ."
                    LIMIT ". $limit;
            $bets = $this->dbo->load($Q); 

            return is_array( $bets ) ? $bets : [$bets]; 
		}
//
//===================================================== 
        public function generic( $d=[] ){
            $uid = isset( $d['uid'] ) ? (int)$uid : 0; 
            $bet = isset( $d['bet'] ) ? (float)$d['bet'] : 0; 
            if( $uid && $float ){
                
            }
        }        
//
//===================================================== 
        
//
//=====================================================  
		
//
//=====================================================
		
	} 





