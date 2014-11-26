<?php
namespace app\core\DB;

use PDO;
class Database {
    private $__DB;
    public static $__INS;
    private $__type = "sqlite";


    private function __construct(){
        $this->_getConfig($this->__type);
    }

    private function __clone(){}

    private function _getConfig($db_type){
        $config = require_once(__DIR__."/../config.php");
        $sql = $config[$db_type];

        if(is_array($sql)) {
            $this->__DB = new PDO('mysql:host=' . $sql['host'] . ';dbname=' . $sql['dbname'], $sql['username'], $sql['password']);
            $this->__DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $this->__DB = new PDO($sql);
        }
    }

    public static function getInstance(){
        if(!(self::$__INS instanceof self)) {
            self::$__INS = new self();
        }
        return self::$__INS;
    }


} 