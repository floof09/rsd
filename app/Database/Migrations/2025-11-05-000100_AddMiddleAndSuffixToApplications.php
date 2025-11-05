<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMiddleAndSuffixToApplications extends Migration
{
    public function up()
    {
        $fields = [
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'first_name',
            ],
            'suffix' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'last_name',
            ],
        ];
        $this->forge->addColumn('applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('applications', ['middle_name', 'suffix']);
    }
}
