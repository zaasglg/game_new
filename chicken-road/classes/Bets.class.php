<?php
	class Bets extends Singleton {         
        protected $dbo; 
        public static $table = TABLE_BETS; 
        protected $id; 
        protected $user; 
        protected $sid; 
        protected $lvl; 
        protected $bet; 
        protected $fire; 
        protected $finish; 
        protected $result; 
        protected $status; 
        protected $date; 
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
            // $current = $this->current(); 
            $this->dbo->upd( self::$table, ['result'=>0, 'status'=>7], ['user'=>$_SESSION['user']['uid']] );
            $data=[
                'user'=> $_SESSION['user']['uid'], 
                'sid'=> $_SESSION['user']['uid'], 
                'lvl'=> isset( $d['lvl'] ) ? (int)$d['lvl'] : 0,
                'bet'=> isset( $d['bet'] ) ? (float)$d['bet'] : 0, 
                'fire'=> isset( $d['fire'] ) ? (int)$d['fire'] : 0, 
                'finish'=> isset( $d['finish'] ) ? (int)$d['finish'] : 0, 
                'result'=> isset( $d['result'] ) ? (float)$d['result'] : 0, 
                'status'=> isset( $d['status'] ) ? (int)$d['status'] : 2 
            ]; 
            if( !$data['status'] ){ $data['status'] = 2; } 

            $balance = isset( $_SESSION['chicken_demo'] ) ? 
                        $_SESSION['chicken_demo'] : 
                        Users::GI()->balance(); 

            if( $data['bet'] > $balance ){
                return ['error'=>1, 'msg'=>"Low balance"]; 
            }

            $res = $this->dbo->ins( self::$table, $data ); 

            if( $res ){
                if( isset( $_SESSION['chicken_demo'] ) ){
                    $_SESSION['chicken_demo'] -= $data['bet']; 
                } 
                else { 
                    Users::GI()->charge([
                        'uid'=>$_SESSION['user']['uid'], 
                        'amount'=>-$data['bet']
                    ]);
                }
            }

            $balance = Users::GI()->balance();

            return $res ? 
                ['success'=>$res, 'data'=>$data, 'balance'=>$balance] : 
                ['error'=>1, 'msg'=>"Unable to create new bet"];
        } 
//
//=====================================================
        public function move( $d=[] ){
            $stp = isset( $d['stp'] ) ? (int)$d['stp'] : 0; 
            if( $stp ){
                $bet = $this->current(); 
                if( $bet ){ 
                    if( $bet['fire'] == $stp - 1 ){
                        $this->lose([ 
                            'id'=>$bet['id'], 
                            'stp'=> $stp-1
                        ]);
                    } 
                    else {
                        $this->dbo->upd( 
                            self::$table, 
                            ['finish'=>$stp], 
                            ['id'=>$bet['id']] 
                        );
                    }
                }
            }
        }
//
//=====================================================
        public function current( $d=[] ){ 
            $uid = isset( $d['uid'] ) ? App::uid( $d['uid'] ) : UID; 
            $Q = "SELECT * 
                    FROM `". self::$table ."` 
                    WHERE `user`='". $uid ."' 
                        AND `status` IN (2) 
                    ORDER BY `id` DESC 
                    LIMIT 1"; 
            $cur_bet = $this->dbo->get($Q); 
            return $cur_bet; 
        } 
//
//=====================================================
        public function lose( $d=[] ){ 
            $cond = []; 
            
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; }
            
            if( $cond ){ 
                $stp = isset( $d['stp'] ) ? (int)$d['stp'] : 0; 

                $data = [ 
                    'finish'=> $stp, 
                    'result'=>0, 
                    'status'=>7
                ]; 
                $this->dbo->upd( self::$table, $data, $cond );  
                return true; 
            } 

            return false; 
        }
//
//=====================================================
        public function close( $d=[] ){ 
            $stp = isset( $d['stp'] ) ? (int)$d['stp'] : 0; 
            $cur_bet = $this->current(); 
            if( $cur_bet ){ 
                $cfs = Cfs::GI()->load(['group'=>$cur_bet['lvl']]); 
                if( $cfs ){ 
                    if( $cur_bet['fire'] == $stp - 1 ){ 
                        $data = [
                            'finish'=> $stp, 
                            'result'=> 0, 
                            'status'=> 7
                        ];
                    } 
                    else { 
                        $cur_cf = $cfs[ $stp - 1 ]['value']; 
                        $award = $cur_bet['bet'] * $cur_cf; 
                        $data = [ 
                            'finish'=> $stp, 
                            'result'=> $award, 
                            'status'=> 7 
                        ]; 
                        $this->dbo->upd( 
                            self::$table, 
                            $data, 
                            ['id'=>$cur_bet['id']]
                        ); 
                        if( isset( $_SESSION['chicken_demo'] ) ){
                            $_SESSION['chicken_demo'] += $award;
                        } 
                        else { 
                            Users::GI()->charge([
                                'uid'=>$cur_bet['user'], 
                                'amount'=>$award
                            ]);
                        }
                    }
                    return true; 
                } 
                return false; 
            }
            return false; 
        }
//
//===================================================== 
        public function fire( $d=[] ){
            $host_id = isset( $d['host_id'] ) ? (int)$d['host_id'] : 
                ( isset( $_REQUEST['user_id'] ) ? (int)$_REQUEST['user_id'] : '' ); 
            //var_dump( $host_id );
            $uid = $this->dbo->getField(
                "uid", 
                TABLE_USERS, 
                "`host_id`='". $host_id ."'"
            ); 
            //var_dump( $uid );
            $bet = $this->current(['uid'=>$uid]); 
            return $bet ? $bet['fire'] + 1 : 'No available games'; 
        }
// DEPRECATED
//===================================================== 
        public function edit( $d=[] ){ 
            $cond = []; 
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; } 

            if( $cond ){
                $bet = $this->get($cond); 
                if( $bet && $bet['status']==2 ){
                    $data = []; 
                    $lvl = isset( $d['lvl'] ) ? (int)$d['lvl'] : 0; 
                    if( $lvl ){ $data['lvl'] = $lvl; } 
                    $bet = isset( $d['bet'] ) ? (float)$d['bet'] : 0; 
                    if( $bet ){ $data['bet'] = $bet; } 
                    $fire = isset( $d['fire'] ) ? (int)$d['fire'] : 0; 
                    if( $fire ){ $data['fire'] = $fire; } 
                    $finish = isset( $d['finish'] ) ? (int)$d['finish'] : 0; 
                    if( $finish ){ $data['finish'] = $finish; } 
                    $result = isset( $d['result'] ) ? (float)$d['result'] : 0; 
                    if( isset( $d['result'] ) ){ $data['result'] = $result; } 
                    $status = isset( $d['status'] ) ? (int)$d['status'] : 0; 
                    if( $status ){ $data['status'] = $status; }

                    if( $data ){
                        $res = $this->dbo->upd( self::$table, $data, $cond ); 
                        if( $res && $data['status'] == 5 ){
                            Users::GI()->charge(['uid'=>$_SESSION['user']['uid'], 'amount'=>$bet['bet']]);
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
// DEPRECATED 
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
// DEPRECATED
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
        
//
//===================================================== 
        
//
//=====================================================  
		
//
//=====================================================
		
	} 





