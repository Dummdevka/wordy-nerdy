<?php
namespace database;
use PDO;
use PDOException;

class Database
{
    protected $conn = false;

    public function __construct( public Array $db_conf ) {
        if( !$this->conn){
            return $this->connect();
        }
        return $this->conn;
    }
    public function connect() {
        if( $this->conn ){
            return $this->conn;
        } else {
            try {
                $dsn = 'mysql:host=' . $this->db_conf['host'] . 
                ';dbname=' . $this->db_conf['db_name'] . 
                ';user=' . $this->db_conf['user'] . 
                ';password=' . $this->db_conf['password'];

                $conn = new PDO($dsn);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                $this->conn = $conn;
                return $this->conn;
            } catch(PDOException $e){
                echo "Database connection error:" . $e->getMessage();
                exit;
            }
        }
    }

    public function table_not_empty( $table ) {
        try{
            $res = $this->connect()->query('select * from ' . $table . ' where id=1');
            $res = $res->fetchAll();
            if(empty($res)) {
                return false;
            } else {
                return true;
            }
        } catch( PDOException $e ){
            return false;
        }
    }
    //Used to form additional conditions
    public function condition($cond, $join) {
        $where_str = ''; //String for additional sql

        foreach($cond as $col=>$val){
            $where_str .= empty($where_str) ? ' ' : $join; //Connecting conditions

            $where_str .= $col . "=:" . $col; //Forming prepared statement

            $cond[':' . $col] = $cond[$col]; //Reseting keys
            unset($cond[$col]); // this isn't needed...
        }
        return $where_str;
    }
    
    //Read
    public function get($table, $params = '*', $cond = '') {
        $sql = 'select ' . $params. ' from ' . $table;

        //Additional conditions
        if(!empty($cond)) {
            if( is_array($cond)){

                $where_str = $this->condition($cond, ' and ');
            } elseif( is_string($cond) ) {

                //Custom condition
                $where_str = $cond;

            }

            $sql .= ' where ' .$where_str; //Add condition
            if(is_array($cond)){
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute($cond); //Execute prepared statement
                $res = $stmt->fetchAll(); //Fetch results
                return $res;
            }
        }
        $res = $this->connect()->query($sql)->fetchAll(); //Get all ids
        return $res;
    }

    public function create($table, $fields, $values) {    
        //Forming prepared statement
        $vals = '';
        $fields1 = explode(',', $fields);
        
        foreach($fields1 as $field){
            $vals .= empty($vals) ? '' : ', ';
            $vals .= ':' . trim($field);
        }
        //Query
        $sql = 'insert into ' . $table . '(' . $fields . ') ' 
        . 'values' . '(' .$vals .')';
        $stmt = $this->prepare_stmt($sql, $values);
        return $this->connect()->lastInsertId();
    }

    //Update
    public function update($table , $id, $vals) {
        $str = $this->condition($vals, ',');
        $vals['id'] = $id;
        
        $sql = 'update ' . $table . ' set ' . $str . ' where id=:id';
        return $this->prepare_stmt($sql, $vals);
    }

    //Delete
    public function delete($table, $id) {
        $sql = 'delete from ' . $table . ' where id=:id';
        return $this->prepare_stmt($sql,[':id'=>$id]);
    }
    
    //Delete table contents
    public function truncate ( $table ) {
        $sql = 'truncate ' . $table;
                'alter table ' . $table . ' auto_increment=1';
        return $this->connect()->exec( $sql );
    }

    public function prepare_stmt($sql, array $vals) {
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute($vals);
    }
}
