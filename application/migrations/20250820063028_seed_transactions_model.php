<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_seed_transactions_model extends CI_Migration
{

    public function up()
{
    echo "UPING 20250820063028 ,";

    $table_name = "transactions";

    // ---- Insert dummy data ----
    $categories = [2, 3, 12, 13, 14];
    $types      = ['income', 'expense'];

    for ($i = 0; $i < 60; $i++) {
        $category_id = $categories[array_rand($categories)];
        $type        = $types[array_rand($types)];
        $amount      = rand(50000, 2000000); // random amount
        $title       = ucfirst($type) . " Transaction " . ($i + 1);

        // random date within last 30 days
        $created_at  = date("Y-m-d H:i:s", strtotime("-" . rand(0, 30) . " days"));

        $data = [
            'user_id'     => 1,
            'category_id' => $category_id,
            'title'       => $title,
            'amount'      => $amount,
            'type'        => $type,
            'created_at'  => $created_at,
            'updated_at'  => $created_at,
        ];

        $this->db->insert($table_name, $data);
    }

    echo " DONE!.<br/>";
}

    // ---------------------------------------------------------------------

    public function down()
    {
        echo "no action for 20250820063028 ,";
    }

    // ---------------------------------------------------------------------
}
