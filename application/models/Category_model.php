<?php

class Category_model extends CI_Model{
    private $userId;

    public function __construct()
    {
        parent::__construct();
        $this->userId = $this->session->userdata('auth');
    }

    private function can(int $id){
        $userId = $this->db->select('user_id')
        ->from('categories')
        ->where('id',$id)
        ->get()
        ->row()
        ->user_id;
        if ($userId != $this->userId) {
            show_error('شما مجوز دسترسی به این داده را ندارید',403);
        }
    }

    public function getCategories(){
        $user = $this->db->where('user_id',$this->userId)->from('categories')->get();
        return $user->result();
    }

    public function insertCategory(string $title){
        $data = [
            'user_id' => $this->userId,
            'title' => $title,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('categories',$data);
    }

    public function updateCategory(int $id,string $title){
        $this->can($id);
        $data = [
            'title' => $title,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->update('categories',$data,['id'=>$id]);
    }

    public function deleteCategory(int $id){
        $this->can($id);
        $this->db->delete('categories',['id'=>$id]);
    }
}