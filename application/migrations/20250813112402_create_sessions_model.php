<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_sessions_model extends CI_Migration
{

    public function up()
    {
        echo "UPING 20250813112402 ,";

        $table_name = "sessions";
        $pk_name = "id";
        $query = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                    `" . $pk_name . "` int AUTO_INCREMENT,
                    `user_id` int,
                    `session_token` varchar(255) UNIQUE,
                    `created_at` datetime,
                    `expires_at` datetime,
                    PRIMARY KEY (`" . $pk_name . "`),
                    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($query);

        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------

    public function down()
    {
        echo "DOWNING 20250813112402 ,";
        $table_name = "sessions";
        $this->dbforge->drop_table($table_name);
        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------
}
