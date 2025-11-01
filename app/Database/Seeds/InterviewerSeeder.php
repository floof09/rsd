<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InterviewerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email' => 'interviewer@rsd.com',
            'password' => password_hash('interviewer123', PASSWORD_DEFAULT),
            'first_name' => 'Interviewer',
            'last_name' => 'User',
            'user_type' => 'interviewer',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}
