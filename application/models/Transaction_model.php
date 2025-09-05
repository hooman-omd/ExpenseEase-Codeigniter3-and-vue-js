<?php

class Transaction_model extends CI_Model
{

    private $userId;

    public function __construct()
    {
        parent::__construct();
        $this->userId = $this->session->userdata('auth');
    }

    private function can(int $id)
    {
        $userId = $this->db->select('user_id')
            ->from('transactions')
            ->where('id', $id)
            ->get()
            ->row()
            ->user_id;
        if ($userId != $this->userId) {
            show_error('شما مجوز دسترسی به این داده را ندارید', 403);
        }
    }

    public function getLastThree()
    {
        $result = $this->db->select('title,amount,type')
            ->from('transactions')
            ->where('user_id', $this->userId)
            ->limit(3, 0)
            ->order_by('created_at', 'desc')
            ->get();
        return $result->result();
    }

    public function getTransaction(int $id){
        $this->can($id);
        $category = $this->db->where('id',$id)
        ->from("transactions")
        ->get()
        ->row();
        return $category;
    }

    public function getTransactions(int|null $category_id = null, string|null $type = null, int|null $offset = 0, int $rowPerPage = 5)
    {
        $offset = $offset == 0 ? 0 : ($offset - 1) * $rowPerPage;


        $this->db->select('transactions.*, categories.title as category_title')
            ->from('transactions')
            ->where('transactions.user_id',$this->userId)
            ->join('categories', 'transactions.category_id = categories.id');

        if (isset($category_id) && $category_id != 0) {
            $this->db->where("category_id", $category_id);
        }
        if (!empty($type)) {
            $this->db->where('type', $type);
        }

        $this->db->limit($rowPerPage, $offset)->order_by('created_at', 'desc');
        $transactions = $this->db->get()->result();


        $this->db->select('COUNT(transactions.id) as total', false)
            ->from('transactions')
            ->where('transactions.user_id = ' . $this->userId)
            ->join('categories', 'transactions.category_id = categories.id');

        if (isset($category_id) && $category_id != 0) {
            $this->db->where("category_id", $category_id);
        }
        if (!empty($type)) {
            $this->db->where('type', $type);
        }

        $allRecords = $this->db->get()->row();
        $pages = ceil($allRecords->total / $rowPerPage);

        return [
            'transactions' => $transactions,
            'total_records' => $allRecords->total,
            'total_pages' => $pages,
            'offset' => $offset
        ];
    }

    public function insertTransaction(array $data)
    {
        $data['user_id'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('transactions', $data);
    }

    public function updateTransaction(int $id, array $data)
    {
        $this->can($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update('transactions', $data, ['id' => $id]);
    }

    public function deleteTransaction(int $id)
    {
        $this->can($id);
        $this->db->delete('transactions', ['id' => $id]);
    }

    public function getSum()
    {
        $income = $this->db->select_sum('amount')->where('user_id', $this->userId)
            ->where('type', 'income')->from('transactions')->get()->row();

        $expense = $this->db->select_sum('amount')->where('user_id', $this->userId)
            ->where('type', 'expense')->from('transactions')->get()->row();

        return ['income' => $income, 'expense' => $expense];
    }

    public function getChartData(int $k = 14)
    {
        $data = $this->db->select("
        DATE_FORMAT(created_at, '%Y-%m-%d') as txn_date,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as all_incomes,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as all_expenses
        ")
            ->from('transactions')
            ->where('user_id', $this->userId)
            //->where("created_at >=", "DATE_SUB(CURDATE(), INTERVAL {$k} DAY)", false)  k days from today
            ->group_by("DATE_FORMAT(created_at, '%Y-%m-%d')")
            ->order_by('txn_date', 'DESC')
            ->limit($k,0)
            ->get()
            ->result();

        return $data;
    }
}
