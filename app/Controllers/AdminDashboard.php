<?php

namespace App\Controllers;

class AdminDashboard extends BaseController
{
    public function __construct()
    {
        helper('auth');
    }
    
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login')->with('error', 'Please login as admin to access this page');
        }
        
        return view('admin/dashboard');
    }
}
