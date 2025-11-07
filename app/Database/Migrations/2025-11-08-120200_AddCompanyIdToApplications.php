<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdToApplications extends Migration
{
    public function up()
    {
        // Add nullable company_id; backfill from existing company_name if possible later via manual script.
        $fields = [
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'company_name',
            ],
        ];
        $this->forge->addColumn('applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('applications', 'company_id');
    }
}
