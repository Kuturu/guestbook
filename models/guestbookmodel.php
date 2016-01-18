<?php

namespace models;

use core\Model;
use exceptions\HttpNotFoundException;
use core\Service;

class GuestbookModel extends Model{
    
    private $add_record = "INSERT INTO records (name, email, website, ip, browser, content, date) VALUES (:name, :email, :website, :ip, :browser, :content, :date)";
    private $get_limit_records = "SELECT * FROM records ORDER BY";
    private $get_count_records = "SELECT COUNT(*) FROM records";
    private $delete_record = "DELETE FROM records WHERE id = :id";
    private $get_edit_record = "SELECT * FROM records WHERE id = :id";
    private $update_record = "UPDATE records SET name = :name, email = :email, website = :website, content = :content WHERE id = :id";
    private $sort = 'date';
    private $order = 'DESC';


    private $valid_errs = array();
    private $captcha;
    
    public $name;
    public $email;
    public $website;
    public $ip;
    public $browser;
    public $content;
    public $date;   
    
    public function addRecord(){        
        if($this->load() && $this->validate()){
            $name = (array) $this->getPropByArray();
            $this->doStatement($this->add_record, $name);
            return true;
        }  else {
            return $this->valid_errs;
        }
    }
    
    public function deleteRecord($id){        
        $res = $this->doStatement($this->delete_record, array('id' => $id));
        return $res->rowCount();
    }
    
    public function editRecord($id){     
        if($this->load() && $this->validate()){
            $name = (array) $this->getPropByArray();
            $name['id'] = $id;
            unset($name['ip'], $name['browser'], $name['date']);
            $this->doStatement($this->update_record, $name);
            return true;
        }
        $sth = $this->doStatement($this->get_edit_record, array('id' => $id));
        $sth->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $row = $sth->fetch();
        return $row;
    }
    
    public function getNumberRecords() {
        $sth = $this->doStatement($this->get_count_records, array());
        $records_number = $sth->fetch();
        return $records_number[0];
    }
    
    public function getLimitRecords($offset, $limit) {
        $this->getSortOrder();
        if(is_int($offset) && is_int($limit)){
            $statement = $this->get_limit_records . " " . $this->sort . " " . $this->order . " LIMIT " . $offset . ", " . $limit;
        }else{
            throw new HttpNotFoundException();
        }
        $sth = $this->doStatement($statement, array());
        $sth->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $mod_obj_arr = array();
        while ($row = $sth->fetch()){
            array_push($mod_obj_arr, $row);
        }
        return $mod_obj_arr; 
    }
    
    private function getSortOrder() {
            if(!isset($_SESSION)){ 
                session_start(); 
            }
            if(isset($_SESSION['sort']) && isset($_SESSION['order'])){
                $this->sort = $_SESSION['sort'];
                $this->order = $_SESSION['order'];
            }
    }
    
    public function setSortOrder($sort, $order){
        if(($sort === 'name' || $sort === 'date' || $sort === 'email') && ($order === "asc" || $order === "desc")){
            if(!isset($_SESSION)){ 
                session_start(); 
            } 
            $_SESSION['sort'] = $sort;
            $_SESSION['order'] = $order;
            return true;
        } else {
            return false; 
        }
    }

        protected function load() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->name    = $this->prepareData($_POST['name']);
            $this->email   = $this->prepareData($_POST['email']);
            $this->website = $this->prepareData($_POST['website']);
            $this->content = $this->prepareData($_POST['content']);
            $date          = new \DateTime();
            $this->date    = $date->format('Y-m-d H:i:s');
            $this->ip      = $this->prepareData($_SERVER['REMOTE_ADDR']);
            $this->browser = $this->prepareData($_SERVER['HTTP_USER_AGENT']);
            $this->captcha = $this->prepareData($_POST['captcha']);
            return true;
        }  else {
            return false;
        }
    }
    
    public function validate($attributeNames = null){
        foreach ($this->getRequired() as $key => $required ) {
            if($required == ""){
                $this->valid_errs[$key] = "Must be filled!";
                return false;
            }
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->valid_errs['email'] = "Invalid email format";
        }
        if (!preg_match("/^[a-zA-Z ]*$/",  $this->name)) {
            $this->valid_errs['name']  = "Only letters and white space allowed"; 
        }
        if($this->website != ""){
            $parts = parse_url($this->website);
            if ( !isset($parts["scheme"]) ){
                $this->website = "http://{$this->website}";
            }
            if (!filter_var($this->website, FILTER_VALIDATE_URL)) {
                $this->valid_errs['website'] = "Invalid URL"; 
            }
        }
        if (Service::getService()->getCaptchaText() !== strtoupper($this->captcha)) {
            $this->valid_errs['captcha'] = "The text doesn't match. Try again";
        }
        return (count($this->valid_errs) > 0) ? false : true;
    }
      
    private function getRequired()
    {
        return array('name' => $this->name, 'email' => $this->email, 'content' => $this->content, 'captcha' => $this->captcha);
    }
}
