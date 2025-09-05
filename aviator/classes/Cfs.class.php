<?php
	class Cfs extends Singleton {         
        protected $dbo; 
        public static $table = TABLE_CF; 
        protected $id; 
        protected $amount; 
        protected $status;  
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
        public function bulk( $d=[] ){
            $cfs = isset( $d['cfs'] ) ? explode('#', $d['cfs']) : [];
            $res = []; 
            if( $cfs ){
                foreach( $cfs as $cf ){
                    $val = preg_replace('/[^\d\,\.]/', '', $cf); 
                    $val = preg_replace('/[\,]/', '.', $val); 
                    $this->add(['amount'=>(float)$val, 'status'=>2]);
                    $res[] = $val; 
                }
            } 
            return ['success'=>1, 'data'=>$res];
        }
//
//===================================================== 
        public function add( $d=[] ){
            $data = [
                'amount'=> isset( $d['amount'] ) ? (float)$d['amount'] : 0, 
                'status'=> isset( $d['status'] ) ? (int)$d['status'] : 2 
            ]; 
            if( !$data['status'] ) { $data['status'] = 2; }

            $res = $this->dbo->ins( self::$table, $data ); 

            return $res ? ['success'=>$res, 'data'=>$data] : ['error'=>1, 'msg'=>"Unable to add cf"];
        }
//
//===================================================== 
        public function edit( $d=[] ){ 
            $cond = []; 
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond['id'] = $id; }  

            if( $cond ){
                $data = []; 

                $amount = isset( $d['amount'] ) ? (float)$d['amount'] : 0; 
                if( $amount ){ $data['amount'] = $amount; }
                $status = isset( $d['status'] ) ? (int)$d['status'] : 0; 
                if( $status ){  $data['status'] = $status;  } 

                if( $data ){
                    $upd = $this->dbo->upd( self::$table, $data, $cond ); 
                    return ['success'=>1, 'data'=>$data, 'result'=>$upd];
                }
                return ['error'=>1, 'msg'=>"Nothing to change", 'data'=>$d]; 
            }
            return ['error'=>1, 'msg'=>"Wrong data format"]; 
        }
//
//===================================================== 
        public function get( $d= [] ){ 
            $cond = []; 

            $id = isset( $d['id'] ) ? preg_replace('/[^\d\,]/', '', $d['id']) : ''; 
            if( $id ){ $cond[] = "cf.`id` IN (". $id .")"; }
            
            $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/', '', $d['status']) : ''; 
            if( $status ){ $cond[] = "cf.`status` IN (". $status .")"; } 
            else { $cond[] = "cf.`status` NOT IN (5)"; }
           
            $Q = "SELECT cf.*
                    FROM `". self::$table ."` AS cf ".
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ."
                    ORDER BY g.`id` DESC 
                    LIMIT 1 "; 
            $game = $this->dbo->get($Q); 
            
            return $game; 
        }
//
//===================================================== 
        public function load( $d=[] ){ 
            $cond = []; 

            $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/', '', $d['status']) : ''; 
            if( $status ){ $cond[] = "c.`status` IN (". $status .")"; } 
            else { $cond[] = "c.`status` NOT IN (5)"; }

            $sort = isset( $d['sort'] ) ? preg_replace( '/[^A-za-z0-9\-\_]/', '', $d['sort'] ) : ''; 
            $dir = isset( $d['dir'] ) ? App::uid( $d['dir'] ) : "";
            $sortBy = ( $sort && $dir ? ( "c.`". $sort ."` ". $dir ) : "c.`id` DESC" ); 

            $page = isset( $d['page'] ) ? (int)$d['page'] : 1; 
            $length = isset( $d['length'] ) ? (int)$d['length'] : 20; 
            $limit = $page > 1 ? ( "". ( ( $page - 1 ) * $length ) .",". $length ) : $length; 

            $Q = "SELECT c.`id`, c.`amount`, c.`status`,  
                        COALESCE(
                            (
                                SELECT `id` 
                                    FROM `games` 
                                    WHERE `cf`=c.`id` 
                                        AND `status`=2 
                                        AND `finish` IS NULL 
                            ), '' 
                        ) AS 'active' 
                    FROM `". self::$table ."` AS c ". 
                    ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ." 
                    ORDER BY ". $sortBy ."
                    LIMIT ". $limit; 
            $cfs = $this->dbo->load($Q);

            return $cfs; 
		}
//
//===================================================== 
        public function next( $d=[] ){
            $cond = []; 
            $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
            if( $id ){ $cond[] = "cf.`id`='". $id ."'"; } 

            if( $id ){
                $Q = "SELECT cf.* 
                        FROM `". self::$table ."` AS cf 
                        WHERE cf.`id`>'". $id ."' 
                            AND cf.`status` NOT IN (5) 
                        ORDER BY cf.`id` ASC 
                        LIMIT 1";
                $cf = $this->dbo->get($Q); 
                if( !$cf ){
                    $Q = "SELECT cf.* 
                            FROM `". self::$table ."` AS cf 
                            WHERE cf.`status` NOT IN (5) 
                            ORDER BY cf.`id` ASC 
                            LIMIT 1";
                    $cf = $this->dbo->get($Q); 
                }
            } 
            else {
                $Q = "SELECT cf.* 
                        FROM `". self::$table ."` AS cf 
                        WHERE cf.`status` NOT IN (5) 
                        ORDER BY cf.`id` ASC 
                        LIMIT 1"; 
                $cf = $this->dbo->get($Q);
            }

            return $cf; 
        }
//
//===================================================== 
        public function _current( $d=[] ){ 
            $cfs = []; 
            $Q = "SELECT cf.`id`, cf.`amount`, 
                        g.`id` AS 'game', 'current' AS 'status' 
                    FROM `". self::$table ."` AS cf 
                    JOIN `". TABLE_GAMES ."` AS g 
                        ON g.`cf`=cf.`id` 
                        AND g.`finish` IS NULL 
                        AND g.`status` IN (1,2) 
                    ORDER BY cf.`id` DESC 
                    LIMIT 1"; 
            $current = $this->dbo->get($Q); 

            if( $current ){
                $cfs[] = $current; 
                $min = $this->dbo->getField('MIN(`id`)', self::$table, "`status`=2");
                $max = $this->dbo->getField('MAX(`id`)', self::$table, "`status`=2");
                $next_id = $current['id'] + 1; 
                $next_id = $next_id <= $max ? $next_id : $min; 
                $Q = "SELECT cf.`id`, cf.`amount`, 
                            '' AS 'game', 'next' AS 'status' 
                        FROM `". self::$table ."` AS cf 
                        WHERE cf.`id`='". $next_id ."' 
                            AND cf.`status` NOT IN (5) 
                        ORDER BY cf.`id` DESC 
                        LIMIT 1";
                $next = $this->dbo->get($Q); 
                if( $next ){
                    $cfs[] = $next; 
                }
            }

            return $cfs; 
        }
//
//=====================================================  
		public function current( $d=[] ){ 
            $cfs = []; 

            $Q = "SELECT g.`id` AS 'game', 
                        g.`cf` AS 'cf_id', c.`amount` AS 'cf', 
                        COALESCE(
                            (
                                CASE 
                                    WHEN g.`status`=1 THEN 'loading' 
                                    WHEN g.`status`=2 THEN 'active' 
                                    WHEN g.`status`=7 THEN 'finished' 
                                END 
                            ), '' 
                        ) AS 'game_status' 
                    FROM `". TABLE_GAMES ."` AS g 
                    LEFT JOIN `". self::$table ."` AS c 
                        ON c.`id`=g.`cf` 
                    WHERE g.`status` NOT IN (5) 
                    ORDER BY g.`id` DESC 
                    LIMIT 1 "; 
            $current = $this->dbo->get($Q); 

            if( $current ){
                $cfs[] = $current; 
                $min = $this->dbo->getField('MIN(`id`)', self::$table, "`status`=2");
                $max = $this->dbo->getField('MAX(`id`)', self::$table, "`status`=2");
                $next_id = $current['cf_id'] + 1; 
                $next_id = $next_id <= $max ? $next_id : $min; 
                $Q = "SELECT cf.`id` AS 'cf_id', cf.`amount` AS 'cf', 
                            '' AS 'game', 'next' AS 'game_status' 
                        FROM `". self::$table ."` AS cf 
                        WHERE cf.`id`='". $next_id ."' 
                            AND cf.`status` NOT IN (5) 
                        ORDER BY cf.`id` DESC 
                        LIMIT 1";
                $next = $this->dbo->get($Q); 
                if( $next ){
                    $cfs[] = $next; 
                }
            }

            return $cfs; 

        }
//
//=====================================================
		
	} 





