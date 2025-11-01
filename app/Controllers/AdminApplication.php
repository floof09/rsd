<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\SystemLogModel;

class AdminApplication extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin or interviewer
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        return view('admin/application_form');
    }

    public function save()
    {
        // Check if user is logged in and is admin or interviewer
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $validation = \Config\Services::validation();

        // Define comprehensive validation rules
        $validationRules = [
            'company_name' => [
                'rules' => 'required|in_list[RSD,IGT]',
                'errors' => [
                    'required' => 'Please select a company',
                    'in_list' => 'Invalid company selection'
                ]
            ],
            'first_name' => [
                'rules' => 'required|min_length[2]|max_length[100]|alpha_space',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters',
                    'max_length' => 'First name cannot exceed 100 characters',
                    'alpha_space' => 'First name can only contain letters and spaces'
                ]
            ],
            'last_name' => [
                'rules' => 'required|min_length[2]|max_length[100]|alpha_space',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters',
                    'max_length' => 'Last name cannot exceed 100 characters',
                    'alpha_space' => 'Last name can only contain letters and spaces'
                ]
            ],
            'email_address' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required' => 'Email address is required',
                    'valid_email' => 'Please enter a valid email address',
                    'max_length' => 'Email address is too long'
                ]
            ],
            'phone_number' => [
                'rules' => 'required|regex_match[/^9\d{9}$/]',
                'errors' => [
                    'required' => 'Phone number is required',
                    'regex_match' => 'Phone number must be 10 digits starting with 9'
                ]
            ],
            'viber_number' => [
                'rules' => 'required|regex_match[/^9\d{9}$/]',
                'errors' => [
                    'required' => 'Viber number is required',
                    'regex_match' => 'Viber number must be 10 digits starting with 9'
                ]
            ],
            'street_address' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Street address is required',
                    'max_length' => 'Street address is too long'
                ]
            ],
            'barangay' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Barangay is required',
                    'max_length' => 'Barangay name is too long'
                ]
            ],
            'municipality' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Municipality/City is required',
                    'max_length' => 'Municipality name is too long'
                ]
            ],
            'province' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Province is required',
                    'max_length' => 'Province name is too long'
                ]
            ],
            'birthdate' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Birthdate is required',
                    'valid_date' => 'Please enter a valid date'
                ]
            ],
            'bpo_experience' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'BPO experience is required',
                    'max_length' => 'BPO experience description is too long'
                ]
            ],
            'educational_attainment' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Educational attainment is required',
                    'max_length' => 'Educational attainment description is too long'
                ]
            ],
            'recruiter_email' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required' => 'Recruiter email is required',
                    'valid_email' => 'Please enter a valid recruiter email address',
                    'max_length' => 'Recruiter email is too long'
                ]
            ]
        ];

        // Validate input
        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors())
                ->with('error', 'Please correct the errors below');
        }

        // Additional server-side validations
        
        // Sanitize and validate birthdate (must be at least 18 years old)
        $birthdate = $this->request->getPost('birthdate');
        if ($birthdate) {
            try {
                $birthdateObj = new \DateTime($birthdate);
                $today = new \DateTime();
                $age = $today->diff($birthdateObj)->y;
                
                if ($age < 18) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Applicant must be at least 18 years old')
                        ->with('field_error_birthdate', 'Applicant must be at least 18 years old');
                }
                
                // Prevent future dates
                if ($birthdateObj > $today) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Birthdate cannot be in the future')
                        ->with('field_error_birthdate', 'Birthdate cannot be in the future');
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid birthdate format')
                    ->with('field_error_birthdate', 'Invalid birthdate format');
            }
        }

        // Sanitize inputs (protect against XSS)
        $data = [
            'company_name' => esc($this->request->getPost('company_name')),
            'first_name' => esc(trim($this->request->getPost('first_name'))),
            'last_name' => esc(trim($this->request->getPost('last_name'))),
            'email_address' => filter_var($this->request->getPost('email_address'), FILTER_SANITIZE_EMAIL),
            'phone_number' => $this->request->getPost('phone_number') ? '+63' . preg_replace('/\D/', '', $this->request->getPost('phone_number')) : null,
            'viber_number' => $this->request->getPost('viber_number') ? '+63' . preg_replace('/\D/', '', $this->request->getPost('viber_number')) : null,
            'street_address' => esc(trim($this->request->getPost('street_address'))),
            'barangay' => esc(trim($this->request->getPost('barangay'))),
            'municipality' => esc(trim($this->request->getPost('municipality'))),
            'province' => esc(trim($this->request->getPost('province'))),
            'birthdate' => $birthdate,
            'bpo_experience' => esc(trim($this->request->getPost('bpo_experience'))),
            'educational_attainment' => esc(trim($this->request->getPost('educational_attainment'))),
            'recruiter_email' => filter_var($this->request->getPost('recruiter_email'), FILTER_SANITIZE_EMAIL),
            'interviewed_by' => session()->get('user_id'),
            'status' => 'pending',
        ];

        // Handle file upload with strict validation
        $resume = $this->request->getFile('resume');
        if ($resume && $resume->isValid() && !$resume->hasMoved()) {
            // Validate file type (strict check)
            $allowedMimeTypes = ['application/pdf'];
            $fileMimeType = $resume->getMimeType();
            
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Only PDF files are allowed for resume')
                    ->with('field_error_resume', 'Only PDF files are allowed');
            }

            // Validate file extension
            $fileExtension = $resume->getExtension();
            if (strtolower($fileExtension) !== 'pdf') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Only PDF files are allowed for resume')
                    ->with('field_error_resume', 'Only PDF files are allowed');
            }

            // Validate file size (max 5MB)
            if ($resume->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Resume file size must not exceed 5MB')
                    ->with('field_error_resume', 'File size must not exceed 5MB');
            }

            // Generate safe filename (prevent directory traversal)
            $safeName = $resume->getRandomName();
            
            // Ensure upload directory exists
            $uploadPath = WRITEPATH . 'uploads/resumes';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move file to uploads directory
            try {
                $resume->move($uploadPath, $safeName);
                $data['resume_path'] = 'writable/uploads/resumes/' . $safeName;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to upload resume file')
                    ->with('field_error_resume', 'Upload failed');
            }
        }

        // Insert data with error handling
        try {
            if ($applicationModel->insert($data)) {
                // Log application creation
                $systemLog = new SystemLogModel();
                $applicationId = $applicationModel->getInsertID();
                $systemLog->logActivity(
                    'Created Application',
                    'application',
                    'Created application for ' . $data['first_name'] . ' ' . $data['last_name'] . ' (ID: ' . $applicationId . ')',
                    session()->get('user_id')
                );
                
                return redirect()->back()->with('success', 'Application saved successfully!');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to save application. Please try again.');
            }
        } catch (\Exception $e) {
            // Log error for debugging
            log_message('error', 'Application save error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving the application. Please try again.');
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
