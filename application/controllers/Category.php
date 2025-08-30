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
        $this->twig->render('category.twig');
    }

    public function getCategories(){
        $categories = $this->categoryModel->getCategories();
        $response = [
            'status' => 'success',
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function showCategory(){
        $categoryId = $this->input->post('category_id');
        $category = $this->categoryModel->showCategory($categoryId);
        $response = [
            'status' => 'success',
            'message' => 'Categoriy retrieved successfully',
            'data' => $category
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
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
            $response = [
            'status' => 'failed',
            'message' => validation_errors()
            ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }else{
            $message = "";
            if (!empty($this->input->post('category_id'))) {
                $this->categoryModel->updateCategory($this->input->post('category_id'),$this->input->post('title'));
                $message = 'دسته بندی مورد نظر بروزرسانی شد';
            }else{
                $this->categoryModel->insertCategory($this->input->post('title'));
                $message = 'دسته بندی مورد نظر ثبت شد';
            }

            $response = [
            'status' => 'success',
            'message' => $message
            ];

             $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        
    }

    public function delete_category(int $id){
        $this->categoryModel->deleteCategory($id);
        $response = [
            'status' => 'success',
            'message' => 'دسته بندی مورد نظر حذف شد'
        ];

        $this->output->set_content_type('application/json')
        ->set_output(json_encode($response));
    }
}