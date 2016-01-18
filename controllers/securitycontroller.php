<?php

namespace controllers;

use core\Controller;
use core\Service;
use models\SecurityModel;

class SecurityController extends Controller {
    public function defaultAction() {
        $route = (array)Service::getService()->getRequestURI();
        if(array_key_exists(2, $route) && ($route[2] == "login" || $route[2] == "logout")){
            $this->$route[2]();
        } else {
            throw new HttpNotFoundException("Page not found!");
        }
    }
    
    public function login() {
        $model = new SecurityModel();
        $model->getUser();
        if(Service::getService()->isAuthenticated()){
            header('Location: /guestbook');
        }
        $this->render('login');
    }
    
    public function logout() {
        if(Service::getService()->isAuthenticated()){
            Service::getService()->clearAuthenticated();
            header('Location: /guestbook');
        }
    }
}
