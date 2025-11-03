<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\SystemLogModel;

class InterviewerDashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is interviewer
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        
        // Get statistics
        $data = [
            'total_applications' => $applicationModel->countAll(),
            'igt_applications' => $applicationModel->where('company_name', 'IGT')->countAllResults(false),
            // Updated from RSD to Everise to reflect current company names
            'rsd_applications' => $applicationModel->where('company_name', 'Everise')->countAllResults(false),
            'recent_applications' => $applicationModel->orderBy('created_at', 'DESC')->findAll(5)
        ];

        // Use clean dashboard view (isolated from legacy corruption)
        return view('interviewer/dashboard2', $data);
    }
}
