<?php
	class DB2 extends Singleton {
        protected $pdo;
        protected $ini;
//
//=====================================================
        protected function __construct(){
        	@$this->pdo = new PDO("mysql:host=". MAIN_DBHOST .";port=". MAIN_DBPORT .";dbname=". MAIN_DBNAME, MAIN_DBUSER, MAIN_DBPASSWD) or die();
			@$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );        
        }
//
//===================================================== 
        public static function init( $c=null, $name="", $params=array() ){
            return parent::getInstance(__CLASS__);
        }
        public static function getInstance( $c=null, $name="", $params=array() ){
            return parent::getInstance(__CLASS__);
        }
//
//=====================================================     
        public function query( $Q, $params = array() ){
        	$sth = $this->pdo->prepare($Q); 
        	//var_dump( $sth );
    		$sth->execute($params);
        	return $sth;
        }
//
//===================================================== 
        public function upd( $table="", $bind=array(), $where='' ){
			return $this->update( $table, $bind, $where );
        }

		public function update( $table="", $bind=array(), $where='' ){
			$set = array();
			$i = 0;
			foreach( $bind as $col => $val) {
			    array_push( $set, "`". $col ."`='". $val ."'" );
			}
			unset( $val ); 
			if( $where && is_array( $where ) && count( $where ) ){
				$cond = array(); 
				foreach( $where as $key=>$val ){ array_push( $cond, "`". $key ."`='". $val ."'" ); } 
				$where = implode(" AND ", $cond); 
				unset( $val );
			} 
			if( $set ){
				$sql = "UPDATE ". $table ." SET ". implode(', ', $set) . " WHERE ". $where; 
				//var_dump( $sql );
				$stmt = $this->query( $sql ); 
				$count = $stmt->rowCount();
				return $count ? $count : $sql; 
			} 
			else {
				return 0; 
			}
		}
//
//===================================================== 
		public function ins( $table="", $bind=array() ){
			return $this->insert( $table, $bind );
		}
		public function insert( $table="", $bind=array() ){
			$cols = array();
			$vals = array();
			$i = 0;
			foreach ($bind as $col => $val) {
			    $cols[] = "`".$col."`";
			    $vals[] = '?';
			}
			$sql = "INSERT INTO ". $table ." ( ". implode(', ', $cols) ." ) VALUES ( ". implode(', ', $vals) ." ) ";
			$stmt = $this->query($sql, array_values( $bind ) ); 
			$res = $stmt->rowCount();
			return $res ? $this->lastInsertId() : false;
		}
//
//===================================================== 
		public function count( $table="", $where=array(), $query='' ){ 
			return $this->getCount( $table, $where, $query );
		}
        public function getCount( $table, $where=array(), $query='' ){
            $Q = "SELECT COUNT(*) AS 'total' FROM ". $table;
			$args = array();
			if( $this->is_assoc($where) && count($where) ){
				$Q .= " WHERE ";
				$vals = array();
				foreach( $where as $key=>$value ){
				    $vals[] = " $key = ? ";
				    $args[] = $value;
				}
				$Q .= implode(" AND ", $vals);
            } 
            else if( is_array($where) && count($where) ){ 
            	$Q .= ' WHERE '.implode(" and ", $where ); 
            } 
            else if( !empty($where) ){ 
            	$Q .= ' WHERE '.$where; 
            }
			if( !empty($query) ){ 
				$sth = $this->query($query, $args); 
			} 
			else { 
				$sth = $this->query($Q, $args); 
			}
            if( $sth->rowCount() == 0 ){ return 0; } 
            else {
                $result = $sth->fetch( PDO::FETCH_BOTH );
                return $result[0];
            }
        }
//
//=====================================================
        public function getField( $key="", $table="", $where=array(), $order=null, $offset=0 ){
            $Q = "SELECT ". $key ." FROM ". $table;
			$args = array();
			if( $this->is_assoc($where) && count($where) ){
				$Q .= " WHERE ";
				$vals = array();
				foreach($where as $key=>$value) {
				    $vals[] = " $key = ? ";
				    $args[] = $value;
				}
				$Q .= implode(" AND ", $vals);
			} 
			else if( is_array($where) && count($where) ){ 
				$Q .= " WHERE ". implode(" AND ", $where ); 
			} 
			else if( !is_array($where) && !empty($where) ){ 
				$Q .= " WHERE ". $where; 
			} 
			if( !empty($order) ){ 
				$Q .= " ORDER BY ". $order; 
			}
            $sth = $this->query( $Q . " LIMIT 1 ". ( $offset>0 ? " offset $offset" : "" ), $args );
            if( $sth->rowCount()==0 ){ 
            	return null; 
            } 
            else {
                $result = $sth->fetch(PDO::FETCH_BOTH);
                return $result[0];
            }
        }
//
//=====================================================
        public function getvalue( $sth="", $key="" ){
            if( $sth->rowCount()==0 ){ return null; } 
            else {
                $result = $sth->fetch(PDO::FETCH_BOTH);
                return $result[$key];
            }
        }
//
//===================================================== 
        public function get( $q="", $p=array(), $fetch_style=PDO::FETCH_ASSOC ){
        	return $this->getRow( $q, $p, $fetch_style );
        }
        public function getRow( $q="", $p=array(), $fetch_style=PDO::FETCH_ASSOC ){
            $sth = $this->query($q, $p);
            if( $sth ){ return $sth->fetch( $fetch_style ); } 
            else { return array(); }
        }
//
//===================================================== 
        public function load( $q="", $fetch_style=PDO::FETCH_ASSOC, $params=array() ){
        	return $this->fetchAll( $q, $fetch_style, $params );
        }
		public function fetchAll( $q="", $fetch_style=PDO::FETCH_ASSOC, $params=array() ){
			if( is_array($fetch_style) ){
				$a = $params;
				$params = $fetch_style;
				$fetch_style = $a;	
			}
    		$sth = $this->query($q, $params);
    		if( $sth ){ return $sth->fetchAll($fetch_style); } 
        	else { return array(); }
		}
//
//=====================================================  
		public function quote( $value ){
    		return $this->pdo->quote($value);
        }
//
//=====================================================
		public function lastInsertId() {
			return $this->pdo->lastInsertId();
		}
	} 





