<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicationModel;

class AdminDashboard extends BaseController
{
    public function __construct()
    {
        helper(['auth', 'date']);
    }
    
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login')->with('error', 'Please login as admin to access this page');
        }
        
        // Get statistics from database
        $userModel = new UserModel();
        $applicationModel = new ApplicationModel();
        
        $data = [
            'total_users' => $userModel->countAll(),
            'total_applications' => $applicationModel->countAll(),
            'pending_applications' => $applicationModel->where('status', 'pending')->countAllResults(false),
            'approved_applications' => $applicationModel->where('status', 'approved')->countAllResults(),
            'recent_applications' => $applicationModel->orderBy('created_at', 'DESC')->limit(5)->find()
        ];
        
        return view('admin/dashboard', $data);
    }
}
