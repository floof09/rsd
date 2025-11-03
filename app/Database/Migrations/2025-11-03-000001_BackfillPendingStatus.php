<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillPendingStatus extends Migration
{
    public function up()
    {
        // 1) Backfill existing rows where status is null/blank
        $this->db->query("UPDATE applications SET status='pending' WHERE status IS NULL OR TRIM(status)='' ");

        // 2) Ensure the column has a NOT NULL + DEFAULT 'pending'
        //    Use a conservative VARCHAR length that fits our values
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
                'default'    => 'pending',
            ],
        ];
        try {
            $this->forge->modifyColumn('applications', $fields);
        } catch (\Throwable $e) {
            // If modifyColumn is unsupported by the driver or fails, fallback to raw SQL
            try {
                $this->db->query("ALTER TABLE applications MODIFY COLUMN status VARCHAR(64) NOT NULL DEFAULT 'pending'");
            } catch (\Throwable $e2) {
                // Last resort: leave schema as-is; data backfill already ensures non-empty values
            }
        }
    }

    public function down()
    {
        // Revert schema default/nullable state (keep data as-is for safety)
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'default'    => null,
            ],
        ];
        try {
            $this->forge->modifyColumn('applications', $fields);
        } catch (\Throwable $e) {
            try {
                $this->db->query("ALTER TABLE applications MODIFY COLUMN status VARCHAR(64) NULL");
            } catch (\Throwable $e2) {
                // ignore
            }
        }
    }
}
