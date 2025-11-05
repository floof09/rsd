<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Config\Database;

class Tools extends BaseController
{
    private function hasValidKey(): bool
    {
        $keyProvided = $this->request->getGet('key');
        $keyExpected = env('app.migrateKey');
        return ($keyExpected && $keyProvided && hash_equals((string) $keyExpected, (string) $keyProvided));
    }

    public function migrate(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $migrate = Services::migrations();
        try {
            $migrate->latest();
            return $this->response->setStatusCode(200)->setBody('Migrations applied successfully.');
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Migration failed: ' . $e->getMessage());
        }
    }

    public function seedAdmin(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $email = trim((string) $this->request->getGet('email'));
        $password = (string) $this->request->getGet('password');
        $first = trim((string) $this->request->getGet('first')) ?: 'Admin';
        $last = trim((string) $this->request->getGet('last')) ?: 'User';

        if ($email === '' || $password === '') {
            return $this->response->setStatusCode(400)->setBody('Missing email or password');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = Database::connect();
        $builder = $db->table('users');

        $existing = $builder->select('id')->where('email', $email)->get()->getFirstRow();
        if ($existing) {
            $builder->where('id', $existing->id)->update([
                'password' => $hash,
                'first_name' => $first,
                'last_name' => $last,
                'user_type' => 'admin',
                'status' => 'active',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $builder->insert([
                'email' => $email,
                'password' => $hash,
                'first_name' => $first,
                'last_name' => $last,
                'user_type' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setStatusCode(200)->setBody('Admin user ensured/updated for ' . $email);
    }
}
