<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateDeprecatedStatus extends Migration
{
    public function up()
    {
        // Migrate deprecated status values to the supported one
        // Decision: map 'pending_for_next_interview' -> 'for_review'
        $this->db->query("UPDATE applications SET status='for_review' WHERE status='pending_for_next_interview'");
    }

    public function down()
    {
        // Revert the mapping (not strictly necessary, but provided for symmetry)
        $this->db->query("UPDATE applications SET status='pending_for_next_interview' WHERE status='for_review'");
    }
}
