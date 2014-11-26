<?php
namespace app\core\DB;

use PDO;
class DB {
    private $_DB;
    public static $_INS;
    private $_type = "sqlite";


    private function __construct(){
        echo "123";
        $this->_getConfig($this->_type);
    }

    private function __clone(){}

    private function _getConfig($db_type){
        $config = require_once(__DIR__."/../config.php");
        $sql = $config[$db_type];

        if(is_array($sql)) {
            $this->_DB = new PDO('mysql:host=' . $sql['host'] . ';dbname=' . $sql['dbname'], $sql['username'], $sql['password']);
        } else {
            $this->_DB = new PDO($sql);
        }
        $this->_DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance(){
        if(!(self::$_INS instanceof self)) {
            self::$_INS = new self();
        }
        return self::$_INS;
    }


}