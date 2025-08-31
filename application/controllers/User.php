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
        $this->twig->render('user.twig');
    }

    public function getUser(){
        $userData = $this->userModel->getUser();
        $response = [
            'status' => 'success',
            'message' => 'user retrieved successfully',
            'data' => $userData
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
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
            $response = [
            'status' => 'failed',
            'message' => validation_errors()
            ];

            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            $this->userModel->updateUser([
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email')
            ]);
            $response = [
            'status' => 'success',
            'message' => 'اطلاعات کاربری بروزرسانی شدند'
            ];

            $this->output->set_content_type('application/json')->set_output(json_encode($response));
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
            $response = [
            'status' => 'failed',
            'message' => validation_errors()
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {

            $userData = $this->userModel->getUser();
            if (!password_verify($this->input->post('current_password'),$userData->password)) {
                $response = [
                'status' => 'failed',
                'message' => "رمزعبور فعلی وارد شده نامعتبر می باشد"
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
            }

            $newPassword = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);

            $this->userModel->updateUser([
                'password' => $newPassword,
            ]);
            $response = [
                'status' => 'success',
                'message' => "رمزعبور بروزرسانی شد"
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
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
            $response = [
            'status' => 'failed',
            'message' => $this->upload->display_errors()
            ];

            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }else{
            $data = $this->upload->data();
            $url = base_url('/uploads/profiles/'.$data['file_name']);
            $this->userModel->uploadProfileImage($url);
            $this->session->set_userdata('url',$url);
            $response = [
            'status' => 'success',
            'message' => 'تصویر جدید با موفقیت آپلود شد'
            ];

            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }
}
