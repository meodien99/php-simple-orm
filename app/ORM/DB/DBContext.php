<?php
namespace app\ORM\DB;

use app\ORM\Entities\EntityState;
/**
 * Class DBContext
 * DATA MAPPING LAYER
 * @package app\ORM\DB
 */
class DBContext {

    private $_db;
    private $entites = [];

    public function __construct(){
        $this->_db = Database::getInstance();
    }

    public function find($entity, $conditions = [], $field = '*', $order = '', $limit = null, $offset = '') {
        $where = '';

        foreach($conditions as $key => $value){
            if(is_string($value))
                $where .= ' '. $key .' = "'. $value . '"'.' &&';
            else
                $where .= ' '. $key .' = '. $value .' &&';
        }

        $where = rtrim($where, " &&");

        $this->_db->select($entity->table, $where, $field, $order, $limit, $offset);
        return $this->_db->objectSingle($entity->class);
    }

    public function all($entity, $conditions = [], $field = '*', $order = '', $limit = null, $offset = ''){
        $where = '';

        foreach($conditions as $key => $value){
            if(is_string($value))
                $where .= ' '. $key .' = "'. $value . '"'.' &&';
            else
                $where .= ' '. $key .' = '. $value .' &&';
        }

        $where = rtrim($where, " &&");

        $this->_db->select($entity->table, $where, $field, $order, $limit, $offset);
        return $this->_db->objectResult($entity->class);
    }

    public function save(){
        $data = [];
        foreach($this->entites as $entity) {
            switch($entity->state) {
                case EntityState::CREATED:
                    foreach ($entity->fields as $key) {
                        $data[$key] = $entity->$key;
                    }

                    $this->_db->insert($entity->table, $data);
                    break;
                case EntityState::MODIFIED:
                    foreach($entity->fields as $key){
                        $data[$key] = $entity->$key;
                    }
                    $where = '';
                    foreach ($entity->primary_keys as $key){
                        $where .= ' '. $key .' = '. $entity->$key.' &&';
                    }
                    $where = rtrim($where, ' &&');

                    $this->_db->update($entity->table, $data, $where);
                    break;
                case EntityState::DELETED:
                    $where = '';
                    foreach($entity->primary_keys as $key){
                        $where .=' '. $key .' = '. $entity->$key . ' &&';
                    }
                    $where = rtrim($where, ' &&');

                    $this->_db->delete($entity->table, $where);
                    break;
                default:
                    break;
            }
        }
        unset($this->entites);
    }

    public function add($entity) {
        $entity->state = EntityState::CREATED;
        array_push($this->entites, $entity);
    }

    public function update($entity) {
        $entity->state = EntityState::MODIFIED;
        array_push($this->entites, $entity);
    }

    public function remove($entity) {
        $entity->state = EntityState::DELETED;
        array_push($this->entites, $entity);
    }
} 