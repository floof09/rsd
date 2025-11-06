<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminUsers extends BaseController
{
    public function activeList()
    {
        // Basic guard: only admins should see this; fallback to login otherwise
        if (session()->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $model = new UserModel();
        // Show all active accounts (any role). Accept common 'active' markers.
        $users = $model->groupStart()
                           ->where('status', 'active')
                           ->orWhereIn('status', ['Active', 'ACTIVE', 'enabled'])
                           ->orWhere('status', 1)
                           ->orWhere('status', '1')
                       ->groupEnd()
                       ->orderBy('first_name', 'asc')
                       ->orderBy('last_name', 'asc')
                       ->findAll();

        // Gather any flash data for form feedback
        $success = session()->getFlashdata('success');
        $errors  = session()->getFlashdata('errors');
        $old     = session()->getFlashdata('old');

        return view('admin/recruiters_list', [
            'users'   => $users,
            'success' => $success,
            'errors'  => $errors,
            'old'     => $old,
        ]);
    }

    /**
     * Handle POST from "Add Recruiter" form.
     * Creates a new interviewer account with status active.
     */
    public function create()
    {
        // Guard: only admins can create accounts
        if (session()->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $request = $this->request;
        $postedType = strtolower(trim((string) ($request->getPost('user_type') ?? $request->getPost('role') ?? 'interviewer')));
        // Map common labels
        if ($postedType === 'recruiter') {
            $postedType = 'interviewer';
        }
        $allowedTypes = ['interviewer', 'admin'];
        if (!in_array($postedType, $allowedTypes, true)) {
            session()->setFlashdata('errors', ['user_type' => 'Invalid role selected.']);
            $old = [
                'first_name' => trim((string) $request->getPost('first_name')),
                'last_name'  => trim((string) $request->getPost('last_name')),
                'email'      => strtolower(trim((string) $request->getPost('email'))),
                'user_type'  => $postedType,
            ];
            session()->setFlashdata('old', $old);
            return redirect()->to(base_url('admin/recruiters'));
        }

        $data = [
            'first_name' => trim((string) $request->getPost('first_name')),
            'last_name'  => trim((string) $request->getPost('last_name')),
            'email'      => strtolower(trim((string) $request->getPost('email'))),
            'password'   => (string) $request->getPost('password'),
            'user_type'  => $postedType,
            'status'     => 'active',
        ];

        $model = new UserModel();

        if (!$model->insert($data)) {
            // Validation or insert failed
            session()->setFlashdata('errors', $model->errors());
            // Preserve user input except password
            $old = $data;
            unset($old['password']);
            session()->setFlashdata('old', $old);
            return redirect()->to(base_url('admin/recruiters'));
        }

        session()->setFlashdata('success', 'Recruiter account created successfully.');
        return redirect()->to(base_url('admin/recruiters'));
    }
}
