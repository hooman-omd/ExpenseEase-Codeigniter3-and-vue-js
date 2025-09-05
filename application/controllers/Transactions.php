<?php

class Transactions extends BaseController
{

    private $transactionModel;
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->transactionModel = $this->Transaction_model;
        $this->load->model('Category_model');
        $this->categoryModel = $this->Category_model;
    }

    public function index()
    {
        $this->twig->render('transaction.twig');
    }

    public function getData()
    {
        $categoryId = $this->input->get('category_id');
        $type = $this->input->get('type');
        $page = $this->input->get('page') ?? 1;


        $sum = $this->transactionModel->getSum();
        $data['categories'] = $this->categoryModel->getCategories();

        $transactionData = $this->transactionModel->getTransactions($categoryId, $type, $page);
        $data['transactions'] = $transactionData['transactions'];

        $data['currentRecords'] = count($data['transactions']);
        $totalPages = $transactionData['total_pages'];
        $range = 2;

        $start = max(1, $page - $range);
        $end   = min($totalPages, $page + $range);

        $pages = range($start, $end);
        $data['pages'] = $pages;
        $data['lastPage'] = $totalPages;
        $data['currentPage'] = $page;
        $data['totalRecords'] = $transactionData['total_records'];
        $data['offset'] = $transactionData['offset'];

        $data['income'] = $sum['income']->amount ?? 0;
        $data['expense'] = $sum['expense']->amount ?? 0;

        $response = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function getTransaction(){
        $id = $this->input->get("id");
        $transaction = $this->transactionModel->getTransaction($id);
        $response = [
            'status' => 'success',
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function insert_transaction()
    {
        $this->form_validation->set_rules(
            [
                [
                    'field' => 'title',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '* فیلد نام تراکنش الزامی است'
                    ]
                ],
                [
                    'field' => 'amount',
                    'rules' => 'required|integer',
                    'errors' => [
                        'required' => '* فیلد مبلغ تراکنش الزامی است',
                        'integer' => '* فیلد مبلغ باید عدد باشد'
                    ]
                ],
                [
                    'field' => 'type',
                    'rules' => 'required|in_list[income,expense]',
                    'errors' => [
                        'required' => '* فیلد نوع تراکنش الزامی است',
                        'in_list' => '* فیلد نوع تراکنش غیرمعتبر می باشد'
                    ]
                ],
                [
                    'field' => 'category_id',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '* فیلد دسته بندی الزامی است'
                    ]
                ]

            ]
        );

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status' => 'failed',
                'message' => validation_errors()
            ];
        } else {
            $message = '';
            if (empty($this->input->post('transaction_id'))) {
                $this->transactionModel->insertTransaction([
                    'title' => $this->input->post('title'),
                    'amount' => $this->input->post('amount'),
                    'type' => $this->input->post('type'),
                    'category_id' => $this->input->post('category_id')
                ]);
                $response = [
                    'status' => 'success',
                    'message' => 'تراکنش جدید ثبت شد'
                ];
            } else {
                $this->transactionModel->updateTransaction($this->input->post('transaction_id'), [
                    'title' => $this->input->post('title'),
                    'amount' => $this->input->post('amount'),
                    'type' => $this->input->post('type'),
                    'category_id' => $this->input->post('category_id')
                ]);
                $response = [
                    'status' => 'success',
                    'message' => 'تراکنش مورد نظر بروزرسانی شد'
                ];
            }
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function delete_transaction(int $id)
    {
        $this->transactionModel->deleteTransaction($id);
        $response = [
            'status' => 'success',
            'message' => 'تراکنش مورد نظر حذف شد'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}
