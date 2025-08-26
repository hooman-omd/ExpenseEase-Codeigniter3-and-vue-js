<?php

class Category extends BaseController{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
        $this->categoryModel = $this->Category_model;
    }

    public function index(){
        $categories = $this->categoryModel->getCategories();
        $this->twig->render('category.twig',['categories' => $categories]);
    }

    public function insert_category(){
        $this->form_validation->set_rules([
            [
                'field' => 'title',
                'rules' => 'required',
                'errors' => [
                    'required' => '* فیلد نام دسته بندی الزامی است'
                ]
            ]
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('errors', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $message = "";
            if (!empty($this->input->post('category_id'))) {
                $this->categoryModel->updateCategory($this->input->post('category_id'),$this->input->post('title'));
                $message = 'دسته بندی مورد نظر بروزرسانی شد';
            }else{
                $this->categoryModel->insertCategory($this->input->post('title'));
                $message = 'دسته بندی مورد نظر ثبت شد';
            }

            $this->session->set_flashdata('success', $message);
            redirect($_SERVER['HTTP_REFERER']);
        }
        
    }

    public function delete_category(int $id){
        $this->categoryModel->deleteCategory($id);
        $this->session->set_flashdata('success','دسته بندی موردنظر حذف شد');
        redirect($_SERVER['HTTP_REFERER']);
    }
}