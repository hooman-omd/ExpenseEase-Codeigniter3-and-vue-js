<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_users_model extends CI_Migration
{

    public function up()
    {
        echo "UPING 20250813111832 ,";

        $table_name = "users";
        $pk_name = "id";
        $query = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                    `" . $pk_name . "` int AUTO_INCREMENT,
                    `name` varchar(255),
                    `email` varchar(255) UNIQUE,
                    `password` varchar(255),
                    `profile_image_url` varchar(255),
                    `created_at` datetime,
                    `updated_at` datetime,
                    PRIMARY KEY (`" . $pk_name . "`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($query);

        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------

    public function down()
    {
        echo "DOWNING 20250813111832 ,";
        $table_name = "users";
        $this->dbforge->drop_table($table_name);
        echo " DONE!.<br/>";
    }

    // ---------------------------------------------------------------------
}
