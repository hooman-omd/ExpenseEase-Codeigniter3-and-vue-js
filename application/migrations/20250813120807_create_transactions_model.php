<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_transactions_model extends CI_Migration
{

    public function up()
    {
        echo "UPING 20250813120807 ,";

        $table_name = "transactions";
        $pk_name = "id";
        $query = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                    `" . $pk_name . "` int AUTO_INCREMENT,
                    `user_id` int,
                    `category_id` int,
                    `title` varchar(255),
                    `amount` bigint,
                    `type` enum('income','expense'),
                    `created_at` datetime,
                    `updated_at` datetime,
                    PRIMARY KEY (`" . $pk_name . "`),
                    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
                    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($query);

        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------

    public function down()
    {
        echo "DOWNING 20250813120807 ,";
        $table_name = "transactions";
        $this->dbforge->drop_table($table_name);
        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------
}
