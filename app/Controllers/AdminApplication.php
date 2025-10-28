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

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email_address' => $this->request->getPost('email_address'),
            'phone_number' => $this->request->getPost('phone_number'),
            'viber_number' => $this->request->getPost('viber_number'),
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
