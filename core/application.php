<?php

namespace core;

use exceptions\ConfigExeption;
use exceptions\HttpNotFoundException;
use exceptions\SecurityExeption;

class Application {
    
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }
    
    public function run() {
        try {
            Service::getService()->init($this->config);
            $route = (array)Service::getService()->getRequestURI();
            $route = array_key_exists(1, $route) ? $route[1] : "";
            $controller = Controller::getController($route);
            $controller->defaultAction();
        } catch (HttpNotFoundException $exc) {
            $error = "This page does not exist.";
            $this->errorRender($error);
        } catch (SecurityExeption $exc) {
            $error = "Permission denied.";
            $this->errorRender($error);
        } catch (ConfigExeption $exc) {
            $error = "Problems with config file.";
            $this->errorRender($error);
        } catch (\Exception $exc) {
            $error = "Something goes wrong. Go back and try again.";
            $this->errorRender($error);
        }
    }
    
    private function errorRender($error) {
        $content_path = __DIR__ . '/../views/exceptions/index.html';
        require_once(__DIR__.'/../views/layout.html');       
    }
}
