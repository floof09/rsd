<?php

namespace App\Controllers;

class ApplicantDashboard extends BaseController
{
    public function __construct()
    {
        helper('auth');
    }
    
    public function index()
    {
        // Check if user is logged in and is applicant
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'applicant') {
            return redirect()->to('/auth/login')->with('error', 'Please login as applicant to access this page');
        }
        
        return view('applicant/dashboard');
    }
}
