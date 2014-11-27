<?php
namespace app\ORM\DB;

use PDO;
class Database {

    public static $DB;

    private $_db_engine = 'mysql';

    private function __construct(){
        $config = $this->dataConfig($this->_db_engine);

        if(!is_null($config)){
            try {
                if($this->_db_engine == 'mysql'){
                    $sql = "mysql::host=" . $config['host'] . ";dbname=" . $config['db'];
                    self::$DB = new PDO($sql, $config['user'], $config['pass']);
                } else if ($this->_db_engine == 'sqlite'){

                }
                    self::$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (\PDOException $e) {
                echo "Connection Error: " . $e->getMessage();
            }
        }
    }

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
} 