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

        return $this->_db->objectSingle($entity->getClass());
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
        foreach($this->entities as $key => $entity) {
            switch($entity->state) {
                case EntityState::CREATED:
                    foreach ($entity->getFields() as $key) {
                        if(isset($entity->$key))
                            $data[$key] = $entity->$key;
                    }

                    $this->_db->insert($entity->getTable(), $data);
                    break;
                case EntityState::MODIFIED:
                    foreach($entity->getFields() as $key){
                        if(isset($entity->$key))
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

    private function action ($entity, $state) {
        if(!isset($this->entities))
            $this->entities = [];
        $entity->state = $state;
        array_push($this->entities, $entity);
        return $this;
    }

    public function add($entity) {
        return $this->action($entity, EntityState::CREATED);
    }

    public function update($entity) {
        return $this->action($entity, EntityState::MODIFIED);
    }

    public function remove($entity) {
        return $this->action($entity, EntityState::DELETED);
    }

    private function entityFromString($entity) {
        $name = "ORM\\Models\\{$entity}";
        return new $name();
    }

    public function init($queries){
        if(is_array($queries)){
            foreach($queries as $query){
                $this->_db->exec($query);
            }
        } else if (is_string($queries)){
            $this->_db->exec($queries);
        }
    }
} 