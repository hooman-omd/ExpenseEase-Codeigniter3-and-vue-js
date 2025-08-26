<?php

class Session extends BaseController{
    public function index(){
        $this->twig->render('dashboard.twig');
    }
}