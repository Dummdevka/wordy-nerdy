<?php
global $logger;
class Database
{
    protected $conn = false;

    public function __construct( public Array $db_conf ) {
        if( !$this->conn){
            $this->connect();
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
            } catch(PDOException $e){
                echo "Database connection error:" . $e->getMessage();
                exit;
            }
        }
    }
    
    //Check that books are loaded into database
    public function booksLoaded() {
        if( !$this->table_exists('wd_books')){
            //Upload the books
            $books = new Bookparser();
            foreach( $books->split(BASEDIR . '/contents')[0] as $str ){
                $val = $this->conn->quote($str);
                $this->conn->exec("insert into wd_books(sentence) values ($val);");
            }
        }
    }

    public function table_exists( $table ) {
        try{
            $this->conn->exec('select * from ' . $table . ' where id=1');
            return true;
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
                unset($cond[$col]);
            }
            return $where_str;
    }
    
    //Read
    public function get($table, $params = '*', $cond = '') {
        global $logger;
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

    //Create
    //Fields - STRING of col names separated with a ,
    //Values - ARRAY col=>val
    public function create($table, $fields, $values) {    
        //Forming prepared statement
        $vals = '';
        $fields1 = explode(',', $fields);
        
        foreach($fields1 as $field){
            $vals .= empty($vals) ? '' : ', ';
            $vals .= ':' . trim($field);
        }
        //Query
        $sql = 'insert into ' . $table . '(' . $fields . ') ' . 'values' . '(' .$vals .') returning id';
        $stmt = $this->prepare_stmt($sql, $vals);
        $res =  $stmt->fetchColumn();
        return $res;
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

    public function prepare_stmt($sql, array $vals) {
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute($vals);
    }
}
