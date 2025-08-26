<?php
require_once BASEPATH.'core/Controller.php';

class BaseController extends CI_Controller{

    private $sessionModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Session_model');
        $this->sessionModel = $this->Session_model;

        if (!$this->isAuth()) {
            if (!$this->hasActiveSession()) {
                redirect(base_url('/Auth/index'));
            }
        }
    }

    private function isAuth(){
        if ($this->session->has_userdata('auth')) {
            return true;
        }
        return false;
    }

    private function hasActiveSession(){
        if (isset($_COOKIE['token'])) {
            $result = $this->sessionModel->hasSession($_COOKIE['token']);
            if ($result['status']) {
                $this->session->set_userdata('auth',$result['user_id']);
                $this->session->set_userdata('name',$result['name']);
                $this->session->set_userdata('url',$result['url']);
                return true;
            }
            return false;
        }
        return false;
    }

}