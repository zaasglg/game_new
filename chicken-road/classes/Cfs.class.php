<?php
class Cfs extends Singleton {         
    protected $dbo; 
    public static $table = TABLE_CFS; 
    protected $id; 
    protected $value; 
    protected $group; 
    protected $status;  

    protected function __construct(){
        @$this->dbo = DBO::getInstance();  
    }

    public static function getInstance( $c=null, $name="", $params=[] ){
        return parent::getInstance(__CLASS__);
    } 

    public function bulk( $d=[] ){
        $cfs = isset( $d['cfs'] ) ? explode('#', $d['cfs']) : [];
        $res = []; 
        if( $cfs ){
            foreach( $cfs as $cf ){
                $val = preg_replace('/[^\d\,\.]/', '', $cf); 
                $val = preg_replace('/[\,]/', '.', $val); 
                $this->add(['value'=>(float)$val, 'status'=>2]);
                $res[] = $val; 
            }
        } 
        return ['success'=>1, 'data'=>$res];
    }

    public function add( $d=[] ){
        $data = [
            'value'=> isset( $d['value'] ) ? (float)$d['value'] : 0, 
            'group'=> isset( $d['group'] ) ? (int)$d['group'] : 0, 
            'status'=> isset( $d['status'] ) ? (int)$d['status'] : 2 
        ]; 
        if( !$data['status'] ) { $data['status'] = 2; }

        $res = $this->dbo->ins( self::$table, $data ); 

        return $res ? 
            ['success'=>$res, 'data'=>$data] : 
            ['error'=>1, 'msg'=>"Unable to add cf"];
    }

    public function edit( $d=[] ){ 
        $cond = []; 
        $id = isset( $d['id'] ) ? (int)$d['id'] : 0; 
        if( $id ){ $cond['id'] = $id; }  

        if( $cond ){
            $data = []; 

            $value = isset( $d['value'] ) ? (float)$d['value'] : 0; 
            if( $value ){ $data['value'] = $value; }
            $group = isset( $d['group'] ) ? (int)$d['group'] : 0; 
            if( $group ){ $data['group'] = $group; }
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
                ORDER BY cf.`id` DESC 
                LIMIT 1 "; 
        $cf = $this->dbo->get($Q); 
        
        return $cf; 
    }

    public function load( $d=[] ){ 
        $cond = []; 

        $group = isset( $d['group'] ) ? (int)$d['group'] : 0; 
        if( $group ){ $cond[] = "c.`group`='". $group ."'"; }

        $status = isset( $d['status'] ) ? preg_replace('/[^\d\,]/', '', $d['status']) : ''; 
        if( $status ){ $cond[] = "c.`status` IN (". $status .")"; } 
        else { $cond[] = "c.`status` NOT IN (5)"; }

        $page = isset( $d['page'] ) ? (int)$d['page'] : 1; 
        $length = isset( $d['length'] ) ? (int)$d['length'] : 100; 
        $limit = $page > 1 ? ( "". ( ( $page - 1 ) * $length ) .",". $length ) : $length; 

        $Q = "SELECT c.`id`, c.`value`, c.`group`, 
                (
                    CASE 
                        WHEN c.`group`=1 THEN 'easy' 
                        WHEN c.`group`=2 THEN 'medium' 
                        WHEN c.`group`=3 THEN 'hard' 
                        WHEN c.`group`=4 THEN 'hardcore' 
                    END 
                ) AS 'lvl', 
                c.`status` 
                FROM `". self::$table ."` AS c ". 
                ( $cond ? "WHERE ". implode(" AND ", $cond) : "" ) ." 
                ORDER BY c.`group` ASC, 
                    c.`value` ASC 
                LIMIT ". $limit; 
        $cfs = $this->dbo->load($Q); 

        $full = isset( $d['full'] ) ? (int)$d['full'] : 0; 
        if( $full ){
            $ret = []; 
            foreach( $cfs as $cf ){
                if( !isset( $ret[ $cf['lvl'] ] ) ){
                    $ret[ $cf['lvl'] ] = []; 
                } 
                $ret[ $cf['lvl'] ][] = (float)$cf['value']; 
            } 
            return $ret; 
        }

        return $cfs; 
    } 
}
