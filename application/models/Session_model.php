<?php

class Session_model extends CI_Model{

    private $userId;

    public function __construct()
    {
        parent::__construct();
        $this->userId = $this->session->userdata('auth');
    }

    public function addSession(array $data){
        $data['created_at']= date('Y-m-d H:i:s');
        $data['expires_at']= date('Y-m-d H:i:s',time()+(60*60*24*10));
        $this->db->insert('sessions',$data);
    }

    public function deleteSession(){
        $this->db->delete('sessions',['user_id'=>$this->userId]);
    }

    public function hasSession(string $token){
        $result = $this->db->select('user_id')
        ->from('sessions')
        ->where('session_token',$token)
        ->get()
        ->row();

        if ($result) {
            $user = $this->db->select('name,profile_image_url')
            ->from('users')
            ->where('id',$result->user_id)
            ->get()
            ->row();
            return ['status'=>true,'user_id'=>$result->user_id,'name'=>$user->name,'url'=>$user->profile_image_url];
        }

        return ['status'=>false,'user_id'=>null,'name'=>null,'url'=>null];
    } 
}