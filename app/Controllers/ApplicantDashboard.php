<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

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
        
        $applicationModel = new ApplicationModel();
        $userId = session()->get('user_id');
        
        // Get user's application if exists
        $application = $applicationModel->getUserApplication($userId);
        
        $data = [
            'application' => $application
        ];
        
        return view('applicant/dashboard', $data);
    }
}
