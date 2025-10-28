<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

class Application extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please login to continue');
        }

        // Check if user type is applicant
        if (session()->get('user_type') !== 'applicant') {
            return redirect()->to('/admin/dashboard');
        }

        $applicationModel = new ApplicationModel();
        $userId = session()->get('user_id');
        
        // Check if user already has an application
        $existingApplication = $applicationModel->getUserApplication($userId);
        
        if ($existingApplication) {
            return redirect()->to('/applicant/dashboard')->with('info', 'You have already submitted an application');
        }

        return view('applicant/application_form');
    }

    public function submit()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'applicant') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $userId = session()->get('user_id');

        // Check if user already has an application
        if ($applicationModel->getUserApplication($userId)) {
            return redirect()->to('/applicant/dashboard')->with('error', 'You have already submitted an application');
        }

        // Validate input
        $validation = \Config\Services::validation();
        
        if (!$this->validate([
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'contact_number' => 'required|min_length[10]',
            'email_address' => 'required|valid_email',
            'municipality' => 'required',
            'educational_attainment' => 'required',
            'bpo_experience' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Prepare data
        $data = [
            'user_id' => $userId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email_address' => $this->request->getPost('email_address'),
            'home_address' => $this->request->getPost('home_address'),
            'municipality' => $this->request->getPost('municipality'),
            'educational_attainment' => $this->request->getPost('educational_attainment'),
            'bpo_experience' => $this->request->getPost('bpo_experience'),
            'status' => 'pending'
        ];

        if ($applicationModel->insert($data)) {
            return redirect()->to('/applicant/dashboard')->with('success', 'Application submitted successfully! Please wait for admin approval.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to submit application. Please try again.');
        }
    }
}
