<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class Interviewer2Seeder extends Seeder
{
    public function run()
    {
        $email = 'interviewer2@rsd.com';
        $users = new UserModel();

        // Check if exists
        $existing = $users->where('email', $email)->first();
        if ($existing) {
            echo "User {$email} already exists (ID: {$existing['id']}).\n";
            return;
        }

        // Create with model so the password callback hashes it
        $data = [
            'email' => $email,
            'password' => 'Rsd@12345', // will be hashed by UserModel::hashPassword
            'first_name' => 'Interviewer',
            'last_name' => 'Two',
            'user_type' => 'interviewer',
            'status' => 'active',
        ];

        if (!$users->insert($data)) {
            $err = method_exists($users, 'errors') ? json_encode($users->errors()) : 'unknown error';
            echo "Failed to insert user: {$err}\n";
            return;
        }

        $id = $users->getInsertID();
        echo "Created interviewer user #{$id} ({$email}) with default password: Rsd@12345\n";
    }
}
