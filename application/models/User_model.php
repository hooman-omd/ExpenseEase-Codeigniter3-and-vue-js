<?php

class User_model extends CI_Model{
    private $userId;

    public function __construct()
    {
        parent::__construct();
        $this->userId = $this->session->userdata('auth');
    }

    public function getUser(){
        $user = $this->db->where('id',$this->userId)->from('users')->get();
        return $user->row();
    }

    public function addUser(array $data){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('users',$data);
        return $this->db->insert_id();
    }

    public function updateUser(array $data){
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update('users',$data,['id'=>$this->userId]);
    }

    public function checkLogin(string $userName,string $password){
        $login = $this->db->select('id,name,profile_image_url,password')
        ->where('name',$userName)
        ->from('users')
        ->get()
        ->row();

        if ($login && password_verify($password,$login->password)) {
            return ['status'=>true,'id'=>$login->id,'name'=>$login->name,'url'=>$login->profile_image_url];
        }

        return ['status'=>false,'id'=>null,'name'=>null,'url'=>null];
    }

    public function uploadProfileImage(string $url){
        $this->db->update('users',[
            'profile_image_url' => $url
        ],['id'=>$this->userId]);
    }
}