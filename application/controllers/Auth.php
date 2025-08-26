<?php
require_once BASEPATH . 'core/Controller.php';

class Auth extends CI_Controller
{
    private $userModel;
    private $sessionModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->userModel = $this->User_model;

        $this->load->model('Session_model');
        $this->sessionModel = $this->Session_model;
    }

    public function index()
    {
        $this->twig->render('login.twig');
    }

    public function register()
    {
        $this->twig->render('register.twig');
    }

    public function register_user()
    {
        $this->form_validation->set_rules([
            [
                'field' => 'name',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد نام کاربری الزامی است'
                ]
            ],
            [
                'field' => 'email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => '* فیلد ایمیل الزامی است',
                    'valid_email' => '* ایمیل وارد شده معتبر نمی باشد'
                ]
            ],
            [
                'field' => 'password',
                'rules' => 'required|min_length[8]|matches[confirm-password]',
                'errors' => [
                    'required' => '* فیلد رمزعبور الزامی است',
                    'min_length' => '* رمزعبور باید حداقل ۸ کاراکتر داشته باشد',
                    'matches' => '* تکرار رمز عبور مطابقت ندارد'
                ]
            ],
            [
                'field' => 'confirm-password',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد تکرار رمزعبور الزامی است'
                ]
            ]
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('errors',validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $newUserId = $this->userModel->addUser([
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password')
            ]);
            $this->session->set_flashdata('success',$this->input->post('name')." عزیز، ثبت نام شما با موفقت انجام شد");
            $this->session->set_userdata('auth',$newUserId);
            redirect(base_url());
        }
    }

    private function createSessionToken()
    {
        $token = bin2hex(random_bytes(16));
        return $token;
    }

    public function login()
    {
        $this->form_validation->set_rules([
            [
                'field' => 'name',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد نام کاربری الزامی است'
                ]
            ],
            [
                'field' => 'password',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد رمزعبور الزامی است'
                ]
            ]
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('errors', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $userName = $this->input->post('name');
            $password = $this->input->post('password');
            $checkLogin = $this->userModel->checkLogin($userName, $password);

            if ($checkLogin['status']) {
                $this->session->set_userdata('auth', $checkLogin['id']);
                $this->session->set_userdata('name', $checkLogin['name']);
                $this->session->set_userdata('url', $checkLogin['url']);

                if ($this->input->post('remember-me') == 'on') {
                    $token = $this->createSessionToken();
                    $this->sessionModel->addSession([
                        'user_id' => $checkLogin['id'],
                        'session_token' => $token
                    ]);
                    setcookie('token', $token, time() + (60 * 60 * 24 * 10), '/'); //10 days
                }

                redirect(base_url());
            } else {
                $this->session->set_flashdata('errors', 'نام کاربری یا رمزعبور اشتباه است');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function logout()
    {
        if ($this->session->has_userdata('auth')) {
            $this->session->sess_destroy();

            $this->sessionModel->deletesession();
            if (isset($_COOKIE['token'])) {
                setcookie('token', '', time() - (60 * 60 * 10), '/');
            }

            redirect(base_url());
        }
    }
}
