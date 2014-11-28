<?php
namespace ORM\DB;

use ORM\Entities\EntityState;
/**
 * Class DBContext
 * DATA MAPPING LAYER
 * @package src\ORM\DB
 */
class DBContext {

    private $_db;
    private $entities = [];

    public function __construct(){
        $this->_db = Database::getInstance();
    }

    public function find($entity, $conditions = [], $field = '*', $order = '', $limit = null, $offset = '') {
        $entity = $this->entityFromString($entity);
        $where = '';

        if(count($conditions) > 0 ){
            foreach($conditions as $key => $value){
                if(is_string($value))
                    $where .= ' '. $key .' = "'. $value . '"'.' &&';
                else
                    $where .= ' '. $key .' = '. $value .' &&';
            }
            $where = rtrim($where, " &&");
        }
        $this->_db->select($entity->getTable(), $where, $field, $order, $limit, $offset);

        return $this->_db->objectResult($entity->getClass());
//        return $this->_db->resultArray();
    }

    public function all($entity, $conditions = [], $field = '*', $order = '', $limit = null, $offset = ''){
        $entity = $this->entityFromString($entity);

        $where = '';
        if(count($conditions) > 0 ){
            foreach($conditions as $key => $value){
                if(is_string($value))
                    $where .= ' '. $key .' = "'. $value . '"'.' &&';
                else
                    $where .= ' '. $key .' = '. $value .' &&';
            }
        }
        $where = rtrim($where, " &&");

        $this->_db->select($entity->getTable(), $where, $field, $order, $limit, $offset);
        return $this->_db->objectResult($entity->getClass());
    }

    public function save(){
        $data = [];
        foreach($this->entities as $entity) {
            switch($entity->state) {
                case EntityState::CREATED:
                    foreach ($entity->getFields() as $key) {
                        $data[$key] = $entity->$key;
                    }

                    $this->_db->insert($entity->getTable(), $data);
                    break;
                case EntityState::MODIFIED:
                    foreach($entity->getFields() as $key){
                        $data[$key] = $entity->$key;
                    }
                    $where = '';
                    foreach ($entity->getPrimaryKey() as $key){
                        $where .= ' '. $key .' = '. $entity->$key.' &&';
                    }
                    $where = rtrim($where, ' &&');

                    $this->_db->update($entity->getTable(), $data, $where);
                    break;
                case EntityState::DELETED:
                    $where = '';
                    foreach($entity->getPrimaryKey() as $key){
                        $where .=' '. $key .' = '. $entity->$key . ' &&';
                    }
                    $where = rtrim($where, ' &&');

                    $this->_db->delete($entity->getTable(), $where);
                    break;
                default:
                    break;
            }
        }
        unset($this->entities);
    }

    public function add($entity) {
        $entity->state = EntityState::CREATED;
        array_push($this->entities, $entity);
    }

    public function update($entity) {
        $entity->state = EntityState::MODIFIED;
        array_push($this->entities, $entity);
    }

    public function remove($entity) {
        $entity->state = EntityState::DELETED;
        array_push($this->entities, $entity);
    }

    private function entityFromString($entity) {
        $name = "ORM\\Models\\{$entity}";
        return new $name();
    }
} 