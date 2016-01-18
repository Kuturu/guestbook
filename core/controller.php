<?php

namespace core;

use controllers\MainpageController;
use controllers\GuestbookController;
use controllers\SecurityController;
use exceptions\HttpNotFoundException;

abstract class Controller {
    const MAINPAGE = "";
    const GUESTBOOK = "guestbook";
    const SECURITY = "security";
    protected $controller;
    
    public function __construct($controller) {
        $this->controller = $controller;
    }

    public static function getController($controller){
        if($controller == self::MAINPAGE){
            return new MainpageController($controller);
        }elseif ($controller == self::GUESTBOOK) {
            return new GuestbookController($controller);
        } elseif ($controller == self::SECURITY) {
            return new SecurityController($controller);
        } else {
            throw new HttpNotFoundException('This page does not exist.');
        }
    }
    
    public function render($file, $model_arr = null, $params = array()){
        $content_path = __DIR__ . '/../views/' . $this->controller . '/' . $file . '.html';
        $controller = $this->controller;
        require_once(__DIR__.'/../views/layout.html');
    }

    abstract public function defaultAction();
}
