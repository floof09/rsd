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

        return view('admin/recruiters_list', [
            'users' => $users,
        ]);
    }
}
