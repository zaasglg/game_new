<?php
	class Games extends Singleton {         
        protected $dbo; 
        public static $table = TABLE_GAMES; 
        protected $id; 
        protected $cf; 
        protected $status; 
        protected $finish;  
//
//=====================================================
        protected function __construct(){
            @$this->dbo = DBO::getInstance();  
        }
//
//===================================================== 
        public static function getInstance( $c=null, $name="", $params=array() ){
            return parent::getInstance(__CLASS__);
        } 
//
//===================================================== 
        public function search( $d=[] ){
            $game = $this->get([]); 
            if( !$game ){ 
                $old_game = $this->get(['last'=>1]);  
                if( $old_game ){ $cf = Cfs::GI()->next(['id'=>$old_game['cf']]); }
                else { $cf = Cfs::GI()->next([]); } 
                if( $cf ){ $ins = $this->add([ 'cf'=> $cf['id'], 'status'=> 1 ]); } 
                else { $ins = $this->add([ 'cf'=> 1, 'status'=> 1 ]); }
                $game = $this->get([]);
            } 
            return $game; 
        }
//
//===================================================== 
        public function add( $d=[] ){
            $data = [
                'cf'=> isset( $d['cf'] ) ? (int)$d['cf'] : 1, 
                'status'=> isset( $d['status'] ) ? (int)$d['status'] : 1 
            ]; 

            $ins = $this->dbo->ins( self::$table, $data ); 
            $game = $this->get([]);
            return $game; 
        }
//
//===================================================== 
        public function close( $d=[] ){
            $game = $this->get();
            if( $game ){
                $data = [
                    'status'=>7, 
                    'finish'=> date("Y-m-d H:i:s")
                ]; 

                $upd = $this->dbo->upd( self::$table, $data, ['id'=>$game['id']]); 

                $bets = $this->dbo->getField( "GROUP_CONCAT(`id`)", TABLE_BETS, ['game'=>$game['id'], 'status'=>2] ); 
                $bets = $bets ? explode(",", $bets) : []; 
                foreach( $bets as $bet ){
                    $data = [ 'result'=> 0, 'cf'=> 0, 'status'=> 7 ];
                    $bets = Bets::GI()->close(['id'=>$bet, 'cf'=>0]);
                }

                $balance = Users::GI()->balance();

                return ['success'=>1, 'balance'=>$balance];
            }
            return ['error'=>1, 'msg'=>"Game not found"];
        }
//
//===================================================== 
        public function edit( $d=[] ){ 
            $cond = []; 
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; }  
            else { 
                $game = $this->get([]); 
                if( $game ){ $cond['id'] = $game['id']; }
            }

            if( $cond ){
                $data = []; 
                $game = $this->get(['id'=>$id]); 

                if( $game ){
                    $status = isset( $d['status'] ) ? (int)$d['status'] : 0; 
                    if( $status ){ $data['status'] = $status; } 

                    $finish = isset( $d['finish'] ) ? (int)$d['finish'] : 0; 
                    if( $finish ){ $data['finish'] = date("Y-m-d H:i:s"); } 

                    if( $data ){
                        $upd = $this->dbo->upd( self::$table, $data, $cond ); 
                        return ['success'=>1, 'data'=>$data, 'result'=>$upd];
                    }
                    return ['error'=>1, 'msg'=>"Nothing to change", 'data'=>$d]; 
                } 

                return ['error'=>1, 'msg'=>"Game not found"];
            }

            return ['error'=>1, 'msg'=>"Wrong data format"]; 
        }
//
//===================================================== 
        public function get( $d= [] ){ 
            $cond = []; 

            $id = isset( $d['id'] ) ? preg_replace('/[^\d\,]/', '', $d['id']) : ''; 
            if( $id ){ $cond[] = "g.`id` IN (". $id .")"; }
            
            $last = isset( $d['last'] ) ? (int)$d['last'] : 0; 
            if( $last ){ $cond[] = "g.`finish` IS NOT NULL"; } 
            else { $cond[] = "g.`finish` IS NULL"; }
            
            $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/', '', $d['status']) : ''; 
            if( $status ){ $cond[] = "g.`status` IN (". $status .")"; } 
            else { $cond[] = "g.`status` NOT IN (5)"; }
           
            $Q = "SELECT g.`id`, g.`cf`, g.`status`, g.`finish`, cf.`amount`, 
                        ( UNIX_TIMESTAMP() - UNIX_TIMESTAMP( g.`date` ) ) AS 'delta', 
                        DATE_FORMAT( g.`date`, '%Y-%m-%d %H:%i:%s' ) AS 'start'  
                    FROM `games` AS g 
                    LEFT JOIN `cf` AS cf ON cf.`id`=g.`cf` ". 
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ."
                    ORDER BY g.`id` DESC 
                    LIMIT 1 "; 
            $game = $this->dbo->get($Q); 
            
            return $game; 
        }
//
//===================================================== 
        public function load( $d=array() ){ 
            $cond = []; 

            $sort = isset( $d['sort'] ) ? preg_replace( '/[^A-za-z0-9\-\_]/', '', $d['sort'] ) : ''; 
            $dir = isset( $d['dir'] ) ? App::uid( $d['dir'] ) : "";
            $sortBy = ( $sort && $dir ? ( "g.`". $sort ."` ". $dir ) : "g.`id` DESC" ); 

            $page = isset( $d['page'] ) ? (int)$d['page'] : 1; 
            $length = isset( $d['length'] ) ? (int)$d['length'] : 20; 
            $limit = $page > 1 ? ( "". ( ( $page - 1 ) * $length ) .",". $length ) : $length; 

            $Q = "SELECT g.`id`, g.`cf`, g.`status`, g.`finish`, cf.`amount`, 
                        COALESCE(
                            (
                                SELECT COUNT(*) 
                                    FROM `bets` 
                                    WHERE `game`=g.`id` 
                                        AND `status` NOT IN (3,5) 
                            ), 0 
                        ) AS 'bets', 
                        UNIX_TIMESTAMP( g.`date` ) AS 'tst', 
                        DATE_FORMAT( g.`date`, '%Y-%m-%d %H:%i:%s' ) AS 'start'  
                    FROM `". self::$table ."` AS g 
                    LEFT JOIN `cf` AS cf ON cf.`id`=g.`cf` ". 
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ."
                    ORDER BY ". $sortBy ."
                    LIMIT ". $limit; 

            $games = $this->dbo->load($Q);

            return $games; 
		}
//
//=====================================================
		public function history( $d=[] ){
            $Q = "SELECT g.*, cf.`amount` 
                    FROM `games` AS g 
                    LEFT JOIN `cf` AS cf ON cf.`id`=g.`cf` 
                    WHERE g.`status`=7 
                        AND g.`finish` IS NOT NULL 
                    ORDER BY g.`id` DESC 
                    LIMIT 100 ";
            $history = $this->dbo->load($Q); 
            return $history; 
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





