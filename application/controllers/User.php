<?php

class User extends BaseController
{

    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->userModel = $this->User_model;
    }

    public function index()
    {
        $userData = $this->userModel->getUser();
        $this->twig->render('user.twig', ['user' => $userData]);
    }


    public function update_user()
    {
        $this->form_validation->set_rules([
            [
                'field' => 'name',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد نام الزامی است'
                ]
            ],
            [
                'field' => 'email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => '* فیلد ایمیل الزامی است',
                    'valid_email' => '* ایمیل وارد شده معتبر نمی باشد'
                ]
            ]
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('errors', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->userModel->updateUser([
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email')
            ]);
            $this->session->set_flashdata('success', 'اطلاعات کاربری بروزرسانی شدند');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_password()
    {

        $this->form_validation->set_rules([
            [
                'field' => 'current_password',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد رمزعبور فعلی الزامی می باشد'
                ]
            ],
            [
                'field' => 'new_password',
                'rules' => 'required|matches[confirm_password]',
                'errors' => [
                    'required' => '* فیلد رمزعبور جدید الزامی می باشد',
                    'matches' => '* تکرار رمزعبور اشتباه است'
                ]
            ],
            [
                'field' => 'confirm_password',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد تکرار رمزعبور الزامی می باشد'
                ]
            ]
        ]);

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('errors', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {

            $userData = $this->userModel->getUser();
            if ($this->input->post('current_password') != $userData->password) {
                $this->session->set_flashdata('errors', "رمزعبور وارد شده نامعتبر می باشد");
                redirect($_SERVER['HTTP_REFERER']);
            }

            $this->userModel->updateUser([
                'password' => $this->input->post('new_password'),
            ]);
            $this->session->set_flashdata('success', "رمزعبور بروزرسانی شد");
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function uploadProfileImage(){
        $name = $this->session->userdata('name');
        $config['upload_path'] = "./uploads/profiles";
        $config['allowed_types'] = 'gif|jpg|png';
        $config['file_name'] = $name.mt_rand(100000,999999);
        
        $this->load->library('upload', $config);

        $result = $this->upload->do_upload('profile_image');
        if (!$result) {
            $this->session->set_flashdata('errors',$this->upload->display_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $url = base_url('/uploads/profiles/'.$config['file_name']);
            $this->userModel->uploadProfileImage($url);
            $this->session->set_userdata('url',$url);
            $this->session->set_flashdata('success','تصویر جدید با موفقیت آپلود شد');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
}
