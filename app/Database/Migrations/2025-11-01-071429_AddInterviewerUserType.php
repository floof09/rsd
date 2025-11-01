<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInterviewerUserType extends Migration
{
    public function up()
    {
        // Modify the user_type ENUM to include 'interviewer'
        $this->db->query("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'applicant', 'interviewer') DEFAULT 'applicant'");
    }

    public function down()
    {
        // Revert back to original ENUM values
        $this->db->query("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'applicant') DEFAULT 'applicant'");
    }
}
