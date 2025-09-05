<?php

class Dashboard extends BaseController{
    private $categoryModel;
    private $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
        $this->categoryModel = $this->Category_model;
        $this->load->model('Transaction_model');
        $this->transactionModel = $this->Transaction_model;
    }

    public function index(){
        $this->twig->render('dashboard.twig');
    }

    public function getData(){
        $sum = $this->transactionModel->getSum();
        $data['income'] = $sum['income']->amount ?? 0;
        $data['expense'] = $sum['expense']->amount ?? 0;
        $data['remain'] = $sum['income']->amount - $sum['expense']->amount;

        $data['categories'] = $this->categoryModel->getCategories();
        $data['transactions'] = $this->transactionModel->getLastThree();

        $dates = $this->transactionModel->getChartData();
        $data['dates'] = array_column($dates, 'txn_date');
        $data['allIncomes'] = array_column($dates, 'all_incomes');
        $data['allExpenses'] = array_column($dates, 'all_expenses');

        $response = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data
        ];

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}