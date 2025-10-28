<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResumeToApplications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applications', [
            'resume_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'educational_attainment'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('applications', 'resume_path');
    }
}
