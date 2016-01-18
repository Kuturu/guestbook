<?php

namespace core;

class Service {
    
    private $config;
    private static $inst;

    private function __construct(){
    }
    
    public static function getService(){
        if(empty(self::$inst)){
            self::$inst = new Service();
        }
        return self::$inst;
    }
    
    public function getRequestURI() {
        $routes = explode('/', $_SERVER['REQUEST_URI']);
        if(end($routes) === ""){
            array_pop($routes);
        }
        return $routes;
    }
    
    public function init($config_path) {
        $this->ensure(file_exists($config_path), "Configuration file not found");
        if(is_null($this->config)){
            $this->config = include($config_path);
        }
    }
    
    public function getPDO($param = null) {
        if(is_null($param)){
            return $this->config['pdo'];
        }
        return $this->config['pdo'][$param];
    }
    
    public function recordsPerPage() {
        return $this->config['guestbook']['records_per_page'];
    }
    
    private function ensure($expr, $message){
        if(!$expr){
            throw new \Exception($message);
        }
    }
    
    public function setAuthenticate() {
        if(!isset($_SESSION)){ 
            session_start(); 
        } 
        $_SESSION['authenticate'] = true;
    }
    
    public function isAuthenticated() {
        if(!isset($_SESSION)){ 
            session_start(); 
        } 
        if(isset($_SESSION['authenticate'])){
            return true;
        } else {
            return false;
        }
    }
    
    public function clearAuthenticated() {
        if(!isset($_SESSION)){ 
            session_start(); 
        } 
        if(isset($_SESSION['authenticate'])){
            unset($_SESSION['authenticate']);
        }
    }
    
    public function getCaptchaText() {
        if(!isset($_SESSION)){ 
            session_start(); 
        } 
        if(isset($_SESSION['c'])){
            return $_SESSION['c'];
        } else {
            return false;
        }
    }
}
