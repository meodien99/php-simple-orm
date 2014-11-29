<?php
namespace ORM\DB;

use PDO;
class Database {

    public static $DB;

    private $_db_engine = 'sqlite';
    private $_db_connect;
    private $_error;
    private $_statement;


    private function __construct(){
        $config = $this->dataConfig($this->_db_engine);

        if(!is_null($config)){
            try {
                if($this->_db_engine == 'mysql'){
                    $dns = "mysql:host=" . $config['host'] . ";dbname=" . $config['db'];
                    $this->_db_connect = new PDO($dns, $config['user'], $config['pass']);
                } else if ($this->_db_engine == 'sqlite'){
                    $this->_db_connect = new PDO($config['memory']);
                }
                $this->_db_connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                    self::$DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (\PDOException $e) {
                echo "Connection Error: " . $e->getMessage();
            }
        }
    }

    private function __clone(){}

    private function dataConfig($engine){
        $data = require_once(__DIR__.'/../config.php');

        return isset($data['database'][$engine]) ? $data['database'][$engine] : null;
    }


    public static function getInstance(){
        if(!(self::$DB instanceof self) || !self::$DB ){
            self::$DB = new self();
        }
        return self::$DB;
    }

    public function prepares($query){
        $this->_statement = $this->_db_connect->prepare($query);
    }

    public function bind($params, $value, $type = null){
        if(is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default :
                    $type = PDO::PARAM_STR;
            }
        }
        $this->_statement->bindValue($params, $value, $type);
    }

    public function execute(){
        $this->_statement->execute();
    }

    public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = '') {
        $query = "SELECT {$fields} FROM {$table} "
                .($where ? "WHERE {$where} " : '' )
                .($limit ? "LIMIT {$limit} " : '' )
                .(($offset && $limit) ? "OFFSET {$offset} ": '')
                .($order ? "ORDER BY {$order} " : '');

        $this->prepares($query);

    }

    public function insert($table, array $data){
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(", :", array_keys($data));
        $query = "INSERT INTO {$table}({$fieldNames}) VALUES({$fieldValues})";

        $this->prepares($query);

        foreach($data as $key=>$value){
            $this->bind(":$key", $value);
        }
        $this->execute();
    }

    public function update($table, array $data, $where = '') {
        ksort($data);
        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ",");

        $query = "UPDATE {$table} SET {$fieldDetails} ". ($where ? "WHERE {$where}": ' ');

        $this->prepares($query);

        foreach ($data as $key => $value){
            $this->bind(":$key", $value);
        }

        $this->execute();
    }

    public function delete($table, $where, $limit = 1) {
        $this->prepares("DELETE FROM {$table} WHERE {$where} LIMIT $limit");
        $this->execute();
    }

    public function resultArray(){
        $this->execute();

        return $this->_statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function singleArray(){
        $this->execute();

        return $this->_statement->fetch(PDO::FETCH_ASSOC);
    }

    public function objectResult($class , $ctorargs = [] ) {
        $this->execute();
        $this->_statement->setFetchMode(PDO::FETCH_CLASS, $class, $ctorargs);
        return $this->_statement->fetchAll();
    }

    public function objectSingle($class, $ctorargs = [] ) {
        $this->execute();
        $this->_statement->setFetchMode(PDO::FETCH_CLASS, $class, $ctorargs);
        return $this->_statement->fetch();
    }

    public function rowCount(){
        return $this->_statement->rowCount();
    }

    public function lastInsertId(){
        return $this->_statement->lastInsertId();
    }

    public function beginTransaction(){
        return $this->_statement->beginTransaction();
    }

    public function commit(){
        return $this->_statement->commit();
    }

    public function rollBack(){
        return $this->_statement->rollBack();
    }

    public function debugDumpParams(){
        return $this->_statement->debugDumpParams();
    }

    public function exec($query){
        return $this->_db_connect->exec($query);
    }
} 