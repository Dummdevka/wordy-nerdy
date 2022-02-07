<?php
namespace models;

abstract class Model
{
    protected $table_name;
    protected $db;
    protected $config;

    public function __construct() {
        $this->config = & $GLOBALS['config'];
        $this->db = & $GLOBALS['database'];
        //$this->db = $this->database;

        //Getting name of the table
        $table_name= preg_match( '/.*[\\\\$](.*)$/', get_class($this), $a);
        $this->table_name = lcfirst(end( $a )) . 's'; 
    }
    public function get_all (){
        $all_data = $this->db->get($this->table_name);
        return $all_data;
    }
    public function truncate () {
        $res = $this->db->truncate( $this->table_name );
        return $res;
    }
     public function id ( $cond = []) //Returns an array od ids
     {
         $ids = $this->db->get($this->table_name,'id', $cond);
         return $ids;
     }
     
     public function timestamp ( $cond = []) //Returns an array of timestamps
     {
         $ids = $this->db->get($this->table_name,'created_at', $cond);
         return $ids;
     }
 
     public function create ( Array $vals ){
         
         $fields = '';
         foreach($vals as $field => $val){
             $fields .= empty( $fields ) ? '':', ';
             $fields .= $field;
             $vals[':' . $field] = $vals[$field];
             unset( $vals[$field] );
         }
         return $this->db->create($this->table_name, $fields, $vals);
     }
     
     //Update row (by id)
     public function update ( $id, Array $upload_data ){
         var_dump(
             $this->db->update(
                 $this->table_name, $id,$upload_data));
     }
 
     //Delete row (by id)
     public function delete ( $id )
     {
         $this->db->delete( $this->table, $id );
     }
     public function has (Array $vals ) {
        $key = array_key_first( $vals );
        $res = $this->db->get( $this->table_name, $key, $vals );
        
        foreach ($res as $line){
            if ( strcmp( $vals[$key], $line->$key) === 0 ) {
                return true;
            } 
        }
        return false;
     }
    
}