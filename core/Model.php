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

        //Getting name of the table
        // Let's talk about how we can simplify this process... This is more voodoo than
        // I would generally like. For instance, what if in each sub-class, they defined
        // the table_name property explicitly? Then you don't have to do this =]
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

    public function create ( Array $vals)
    {
        $fields = '';
        foreach($vals as $field=>$val){
            $fields .= empty($fields) ? '':', ';
            $fields .= $field;
            $vals[':' . $field] = $vals[$field];
            unset($vals[$field]);
        }
        return $this->db->create($this->table_name, $fields, $vals);
    }

    //Update row (by id)
    public function update($id, Array $upload_data){
        // This seems like we could return the insert ID instead of `var_dump`ing straight into the document.
        var_dump(
            $this->db->update($this->table_name, $id,$upload_data)
        );
    }

    //Delete row (by id)
    public function delete($id)
    {
        // Does ->delete() return anything to indicate success or failure?
        $this->db->delete($this->table, $id);
    }
}
