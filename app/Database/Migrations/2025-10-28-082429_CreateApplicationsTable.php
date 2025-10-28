<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            // Name fields (atomized)
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            // Contact Information
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            // Address fields (atomized)
            'home_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'municipality' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            // Education and Experience
            'educational_attainment' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'bpo_experience' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            // Application Status
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'incomplete'],
                'default' => 'pending',
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('applications');
    }

    public function down()
    {
        $this->forge->dropTable('applications');
    }
}
