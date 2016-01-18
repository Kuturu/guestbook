<?php

namespace models;

use core\Model;
use core\Service;

class SecurityModel extends Model {
    private $get_user = "SELECT * FROM users WHERE username = :username";
    
    public $username;
    public $password;
    
    public function getUser(){
        if($this->load()){
            $sth = $this->doStatement($this->get_user, array('username' => $this->username));
            $sth->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
            $row = $sth->fetch();
            if($row->password === $this->password){
                Service::getService()->setAuthenticate();
                return true;
            }
        }
        return false;
    }
    
    protected function load() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->username = $_POST['username'];
            $this->password = $_POST['password'];
            return true;
        }  else {
            return false;
        }
    }
}
