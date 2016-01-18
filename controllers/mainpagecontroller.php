<?php

namespace controllers;

use \core\Controller;

class MainpageController extends Controller{
    public function defaultAction() {
        $this->render('index');
    }
}
