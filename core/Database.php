<?php

class Database
{
    protected $conn = false;

    public function __construct( public Array $db_conf ) {
        if( !$this->conn){
            $this->connect();
        }
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
                $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
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
}