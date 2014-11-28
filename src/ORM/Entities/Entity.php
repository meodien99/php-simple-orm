<?php
namespace ORM\Entities;

use ORM\DB\Database;
class Entity {

    private $_db;

    public $fields = [];
    public $table;
    public $primary_keys = [];

    public function __construct(){
        foreach($this->fields as $field){
            $this->{$field} = null;
        }
        $this->class = get_class($this);
        $this->_db = Database::getInstance();
    }

    public function add(){
        $data = [];
        foreach ($this->fields as $key){
            $data[$key] = $this->$key;
        }
        $this->_db->insert($this->table, $data);
    }

    public function update(){
        $data = [];
        foreach ($this->fields as $key) {
            if(!is_null($key))
                $data[$key] = $this->$key;
        }

        $where = '';
        foreach ($this->primary_keys as $key) {
            $where .= ' '. $key .' = '. $this->$key .' &&';
        }
        $where = rtrim($where, ' &&');
        $this->_db->update($this->table, $data, $where);
    }

    public function remove(){
        $where = '';
        foreach ($this->primary_keys as $key) {
            $where .= ' '. $key .' = '. $this->$key .' &&';
        }
        $where = rtrim($where, ' &&');
        $this->_db->delete($this->table, $where);
    }
} 