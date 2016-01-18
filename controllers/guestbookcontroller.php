<?php

namespace controllers;

use core\Controller;
use core\Service;
use models\GuestbookModel;
use exceptions\HttpNotFoundException;
use exceptions\SecurityExeption;

class GuestbookController extends Controller{
    public function defaultAction() {
        $route = (array)Service::getService()->getRequestURI();
        if(!array_key_exists(2, $route)){
            $this->mainAction();
        }elseif (is_numeric($route[2])) {
            $this->mainAction($route[2]);
        }
        elseif ($route[2] == "add"){
            $this->$route[2]();
        } elseif (($route[2] == "delete" || $route[2] == "edit") && array_key_exists(3, $route) && is_numeric($route[3])) {
            $this->$route[2]($route[3]);
        } elseif ($route[2] == "orderby" && array_key_exists(3, $route) && array_key_exists(4, $route)) {
            $this->$route[2]($route[3], $route[4]);
        } else {
            throw new HttpNotFoundException("Page not found!");
        }
    }
    
    private function edit($id) {
        if (!Service::getService()->isAuthenticated()) {
            throw new SecurityExeption("Don't have permission");
        }
        $model = new GuestbookModel();
        $res = $model->editRecord($id);
        if($res instanceof GuestbookModel){
            return $this->mainAction(1, $res);
        }
        return $this->mainAction(1, "Your message has been updated successfully!");
    }
    
    private function delete($id) {
        if (!Service::getService()->isAuthenticated()) {
            throw new SecurityExeption("Don't have permission");
        }
        $model = new GuestbookModel();
        $res = $model->deleteRecord($id);
        return $this->mainAction(1, "You deleted " . $res . " message.");
    }
    
    private function orderby($sort, $order){
        $model = new GuestbookModel();
        if(!$model->setSortOrder($sort, $order)){
            throw new HttpNotFoundException("Page not found!");
        }
        $this->mainAction();
    }

    private function add() {
        $model = new GuestbookModel();
        $res = $model->addRecord();
        if(is_array($res)){
            return $this->mainAction(1, $res, $model);
        }
        return $this->mainAction(1, "Your message has been saved successfully!");
    }
    
    private function mainAction($active_page = 1, $message = null, GuestbookModel $model_data = null){ 
        $model = new GuestbookModel();
        $rec_num = $model->getNumberRecords();
        $per_page = Service::getService()->recordsPerPage();
        $pages = ceil($rec_num/$per_page);
        if($active_page <= $pages || $pages == 0){
            $model = (array) $model->getLimitRecords($active_page*$per_page-$per_page, $per_page);
        }  else {
            throw new HttpNotFoundException("Page not found");
        }
        return $this->render('index', $model, array('pages' => $pages, 'active_page' => $active_page, 'message' => $message, 'model_data' => $model_data));
    }
    
}
