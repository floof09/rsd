<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

class AdminApplication extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        return view('admin/application_form');
    }

    public function save()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();

        // Validate birthdate (must be at least 18 years old)
        $birthdate = $this->request->getPost('birthdate');
        if ($birthdate) {
            $birthdateObj = new \DateTime($birthdate);
            $today = new \DateTime();
            $age = $today->diff($birthdateObj)->y;
            
            if ($age < 18) {
                return redirect()->back()->with('error', 'Applicant must be at least 18 years old.')->withInput();
            }
        }

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email_address' => $this->request->getPost('email_address'),
            'phone_number' => $this->request->getPost('phone_number') ? '+63' . preg_replace('/\D/', '', $this->request->getPost('phone_number')) : null,
            'viber_number' => $this->request->getPost('viber_number') ? '+63' . preg_replace('/\D/', '', $this->request->getPost('viber_number')) : null,
            'street_address' => $this->request->getPost('street_address'),
            'barangay' => $this->request->getPost('barangay'),
            'municipality' => $this->request->getPost('municipality'),
            'province' => $this->request->getPost('province'),
            'birthdate' => $this->request->getPost('birthdate'),
            'bpo_experience' => $this->request->getPost('bpo_experience'),
            'educational_attainment' => $this->request->getPost('educational_attainment'),
            'recruiter_email' => $this->request->getPost('recruiter_email'),
            'interviewed_by' => session()->get('user_id'),
            'status' => 'pending',
        ];

        // Handle file upload
        $resume = $this->request->getFile('resume');
        if ($resume && $resume->isValid() && !$resume->hasMoved()) {
            // Validate file type
            if ($resume->getMimeType() !== 'application/pdf') {
                return redirect()->back()->with('error', 'Only PDF files are allowed for resume.')->withInput();
            }

            // Validate file size (max 5MB)
            if ($resume->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Resume file size should not exceed 5MB.')->withInput();
            }

            // Generate unique filename
            $newName = $resume->getRandomName();
            
            // Move file to uploads directory
            $resume->move(WRITEPATH . 'uploads/resumes', $newName);
            
            // Save file path to database
            $data['resume_path'] = 'writable/uploads/resumes/' . $newName;
        }

        if ($applicationModel->insert($data)) {
            return redirect()->back()->with('success', 'Application saved successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to save application.')->withInput();
        }
    }

    public function list()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $data['applications'] = $applicationModel->findAll();

        return view('admin/applications_list', $data);
    }
}
