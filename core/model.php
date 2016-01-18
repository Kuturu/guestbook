<?php

namespace core;

use exceptions\ConfigExeption;

abstract class Model {
    
    //protected $id;
    
    protected static $db;
    protected static $statements = array();

    public function __construct() {
        $pdo = Service::getService()->getPDO();
        if(is_null($pdo)){
            throw new ConfigExeption();
        }
        self::$db = new \PDO($pdo['dns'], $pdo['user'], $pdo['password']);
        self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function prepareStatement($statement){
        if(isset(self::$statements[$statement])){
            return self::$statements[$statement];
        }
        $stmt_handle = self::$db->prepare($statement);
        self::$statements[$statement] = $stmt_handle;
        return $stmt_handle;
    }
    
    public function doStatement($statement, array $values) {
        $sth = $this->prepareStatement($statement);
        $sth->closeCursor();
        $db_result = $sth->execute($values);
        return $sth;
    }
    
    protected function getPropByArray(){
        $p = array();
        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $p[$prop->getName()] = $prop->getValue($this);
        }
        return $p;
    }
    
    protected function prepareData($data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
}