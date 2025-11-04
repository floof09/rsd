<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\SystemLogModel;
use App\Libraries\Mailer;

class AdminApplication extends BaseController
{
    public function index()
    {
        // Only interviewers can open the application form
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        if (session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/admin/applications')
                ->with('error', 'Only interviewers can access the application form.');
        }

        return view('admin/application_form');
    }

    public function save()
    {
        // Only interviewers can submit the application form
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        if (session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/admin/applications')->with('error', 'Only interviewers can submit the application form.');
        }

        $applicationModel = new ApplicationModel();
    $validation = \Config\Services::validation();

        // Pre-clean phone and viber numbers (strip spaces/dashes and leading zeros) BEFORE validation
        $rawPhone = (string) $this->request->getPost('phone_number');
        $rawViber = (string) $this->request->getPost('viber_number');
        $cleanPhone = preg_replace('/\D/', '', $rawPhone);
        $cleanViber = preg_replace('/\D/', '', $rawViber);
        if (strpos($cleanPhone, '0') === 0) { $cleanPhone = substr($cleanPhone, 1); }
        if (strpos($cleanViber, '0') === 0) { $cleanViber = substr($cleanViber, 1); }
        // Inject cleaned values so validator sees sanitized numbers
        if (method_exists($this->request, 'setGlobal')) {
            $this->request->setGlobal('post', array_merge($this->request->getPost(), [
                'phone_number' => $cleanPhone,
                'viber_number' => $cleanViber,
            ]));
        }

        // Define comprehensive validation rules
        $validationRules = [
            'company_name' => [
                'rules' => 'required|in_list[Everise,IGT]',
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
                // Require strict 10 digits starting with 9 (since +63 prefix is displayed separately)
                'rules' => 'permit_empty|regex_match[/^9\d{9}$/]',
                'errors' => [
                    'regex_match' => 'Phone number must be exactly 10 digits and start with 9'
                ]
            ],
            'viber_number' => [
                'rules' => 'permit_empty|regex_match[/^9\d{9}$/]',
                'errors' => [
                    'regex_match' => 'Viber number must be exactly 10 digits and start with 9'
                ]
            ],
            'street_address' => [
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Street address is too long'
                ]
            ],
            'barangay' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Barangay name is too long'
                ]
            ],
            'municipality' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Municipality name is too long'
                ]
            ],
            'province' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Province name is too long'
                ]
            ],
            'birthdate' => [
                'rules' => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Please enter a valid date'
                ]
            ],
            'bpo_experience' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'BPO experience description is too long'
                ]
            ],
            'educational_attainment' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
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
            ],
            // Optional scheduling for another interviewer
            'next_interviewer_email' => [
                'rules' => 'permit_empty|valid_email|max_length[255]',
                'errors' => [
                    'valid_email' => 'Please enter a valid interviewer email address',
                    'max_length' => 'Email is too long'
                ]
            ],
            'next_interview_datetime' => [
                'rules' => 'permit_empty', // will be parsed with DateTime below for extra validation
            ],
            'next_interview_notes' => [
                'rules' => 'permit_empty|max_length[1000]',
                'errors' => [
                    'max_length' => 'Notes are too long'
                ]
            ],
        ];

        // Validate input
        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
        // Use the validator attached by $this->validate() so we always have the actual errors
        ->with('errors', $this->validator ? $this->validator->getErrors() : $validation->getErrors())
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

        // Validate/normalize next interview schedule
        $nextInterviewerEmail = trim((string) $this->request->getPost('next_interviewer_email'));
        $nextInterviewDTInput = trim((string) $this->request->getPost('next_interview_datetime'));
        $nextInterviewNotes   = trim((string) $this->request->getPost('next_interview_notes'));
        $nextInterview = null;
        if ($nextInterviewerEmail !== '' || $nextInterviewDTInput !== '' || $nextInterviewNotes !== '') {
            // If any scheduling fields provided, require at least email + datetime to proceed with scheduling
            if ($nextInterviewerEmail === '' || $nextInterviewDTInput === '') {
                return redirect()->back()->withInput()->with('error', 'To schedule another interview, provide the interviewer email and date/time.');
            }
            try {
                // HTML datetime-local is like 2025-11-04T14:30
                $dt = new \DateTime($nextInterviewDTInput);
                $iso = $dt->format(DATE_ATOM); // ISO8601 with timezone
                $human = $dt->format('M d, Y g:i A');
                $nextInterview = [
                    'email' => $nextInterviewerEmail,
                    'datetime' => $iso,
                    'human' => $human,
                    'notes' => $nextInterviewNotes ?: null,
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('c'),
                ];
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Invalid date/time for the next interview.');
            }
        }

        // Sanitize inputs (protect against XSS)
        $data = [
            'company_name' => esc($this->request->getPost('company_name')),
            'first_name' => esc(trim($this->request->getPost('first_name'))),
            'last_name' => esc(trim($this->request->getPost('last_name'))),
            'email_address' => filter_var($this->request->getPost('email_address'), FILTER_SANITIZE_EMAIL),
            // Normalize contact numbers to E.164 +63 format. Accept inputs like 916..., 0916..., 63916..., +63916...
            'phone_number' => (function () {
                $raw = (string) $this->request->getPost('phone_number');
                $digits = preg_replace('/\D/', '', $raw ?? '');
                if ($digits === '' ) { return null; }
                // Strip leading country code or trunk prefix if present
                if (strpos($digits, '63') === 0) { $digits = substr($digits, 2); }
                if (strpos($digits, '0') === 0) { $digits = substr($digits, 1); }
                // At this point we expect 10 digits starting with 9
                return '+63' . $digits;
            })(),
            'viber_number' => (function () {
                $raw = (string) $this->request->getPost('viber_number');
                $digits = preg_replace('/\D/', '', $raw ?? '');
                if ($digits === '' ) { return null; }
                if (strpos($digits, '63') === 0) { $digits = substr($digits, 2); }
                if (strpos($digits, '0') === 0) { $digits = substr($digits, 1); }
                return '+63' . $digits;
            })(),
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

        // Include notes payload for next interview if provided
        if ($nextInterview) {
            $data['notes'] = json_encode(['next_interview' => $nextInterview]);
        }

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

                // Fire-and-forget email notifications (errors are logged but won't block the flow)
                try {
                    helper('url');
                    $applicantName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                    $company = $data['company_name'] ?? 'Company';
                    $viewLinkAdmin = base_url('admin/applications/' . $applicationId);
                    $viewLinkInterviewer = base_url('interviewer/applications/' . $applicationId);

                    // Email to recruiter
                    $recruiterTo = $data['recruiter_email'] ?? null;
                    if ($recruiterTo) {
                        $subject = 'New Application: ' . $applicantName . ' for ' . $company;
                        $html = '<p>Hello,</p>'
                            . '<p>A new application has been submitted by <strong>' . esc($applicantName) . '</strong> for <strong>' . esc($company) . '</strong>.</p>'
                            . '<ul>'
                            . '<li>Email: ' . esc($data['email_address'] ?? '') . '</li>'
                            . '<li>Phone: ' . esc($data['phone_number'] ?? 'N/A') . '</li>'
                            . '<li>Location: ' . esc(trim(($data['street_address'] ?? '') . ', ' . ($data['barangay'] ?? '') . ', ' . ($data['municipality'] ?? '') . ', ' . ($data['province'] ?? '')), 'html') . '</li>'
                            . '</ul>'
                            . '<p>View application:</p>'
                            . '<p><a href="' . $viewLinkInterviewer . '">Interviewer view</a> | <a href="' . $viewLinkAdmin . '">Admin view</a></p>'
                            . '<p>— RSD System</p>';
                        Mailer::send($recruiterTo, $subject, $html, ['log' => true]);
                    }

                    // Acknowledgement to applicant (include schedule if provided)
                    $applicantTo = $data['email_address'] ?? null;
                    if ($applicantTo) {
                        $subjectApplicant = 'We received your application' . ($company ? (' — ' . $company) : '') . ' | RSD';
                        $scheduleSection = '';
                        if ($nextInterview) {
                            $scheduleSection = '<p><strong>Second interview scheduled</strong></p>'
                                . '<ul>'
                                . '<li>Date/Time: ' . esc($nextInterview['human'] ?? '') . '</li>'
                                . (!empty($nextInterview['notes']) ? ('<li>Notes: ' . esc($nextInterview['notes']) . '</li>') : '')
                                . '</ul>';
                        }
                        $htmlApplicant = '<p>Hi ' . esc($data['first_name'] ?? 'there') . ',</p>'
                            . '<p>Thanks for submitting your application to <strong>' . esc($company) . '</strong>. Your information has been saved.</p>'
                            . $scheduleSection
                            . '<p>If you have questions, reply to this email.</p>'
                            . '<p>— RSD Recruitment</p>';
                        Mailer::send($applicantTo, $subjectApplicant, $htmlApplicant, ['log' => true]);
                    }

                    // Schedule email to another interviewer, if provided
                    if ($nextInterview && !empty($nextInterview['email'])) {
                        $subjectNI = 'Interview scheduled: ' . $applicantName . ' — ' . ($nextInterview['human'] ?? '');
                        $htmlNI = '<p>Hello,</p>'
                            . '<p>' . esc(session()->get('first_name') . ' ' . session()->get('last_name')) . ' scheduled an interview for <strong>' . esc($applicantName) . '</strong>.</p>'
                            . '<ul>'
                            . '<li>Company: ' . esc($company) . '</li>'
                            . '<li>Date/Time: ' . esc($nextInterview['human'] ?? '') . '</li>'
                            . '<li>Applicant Email: ' . esc($data['email_address'] ?? '') . '</li>'
                            . '<li>Applicant Phone: ' . esc($data['phone_number'] ?? 'N/A') . '</li>'
                            . '</ul>'
                            . (!empty($nextInterview['notes']) ? ('<p><strong>Notes:</strong> ' . esc($nextInterview['notes']) . '</p>') : '')
                            . '<p>View application:</p>'
                            . '<p><a href="' . $viewLinkInterviewer . '">Interviewer view</a> | <a href="' . $viewLinkAdmin . '">Admin view</a></p>'
                            . '<p>— RSD System</p>';
                        $opts = ['log' => true];
                        if (!empty($data['recruiter_email'])) { $opts['cc'] = $data['recruiter_email']; }
                        Mailer::send($nextInterview['email'], $subjectNI, $htmlNI, $opts);
                    }
                } catch (\Throwable $e) {
                    // Log but do not interrupt the user flow
                    log_message('error', 'Post-save email notifications failed: ' . $e->getMessage());
                }
                
                return redirect()->back()->with('success', 'Application saved successfully!');
            } else {
                // Surface model-level errors if any (in case of DB/Model validation)
                $modelErrors = method_exists($applicationModel, 'errors') ? $applicationModel->errors() : [];
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $modelErrors)
                    ->with('error', 'Failed to save application. Please review the highlighted fields.');
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

    public function interviewerList()
    {
        // Interviewers can view only their submitted applications
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $data['applications'] = $applicationModel
            ->where('interviewed_by', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/applications_list', $data);
    }

    public function show($id)
    {
        // Admins and interviewers can view records
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);

        if (!$application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found');
        }

        // Decode notes JSON for any stage data (e.g., IGT interview)
        $application['decoded_notes'] = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $application['decoded_notes'] = $decoded;
            }
        }
        $data['application'] = $application;
        return view('admin/application_view', $data);
    }

    public function resume($id)
    {
        // Admins and interviewers can view resumes
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);

        if (!$application || empty($application['resume_path'])) {
            return redirect()->to('/admin/applications')->with('error', 'Resume not found');
        }

        // Build the absolute path and ensure it stays within WRITEPATH/uploads/resumes for safety
        $relativePath = $application['resume_path']; // e.g. writable/uploads/resumes/xyz.pdf
        $absolutePath = realpath(ROOTPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));
        $safeBase = realpath(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'resumes');

        if (!$absolutePath || !$safeBase || strpos($absolutePath, $safeBase) !== 0 || !is_file($absolutePath)) {
            return redirect()->to('/admin/applications')->with('error', 'Invalid resume path');
        }

        // Stream PDF; inline or as download based on query param
        $forceDownload = (bool) $this->request->getGet('download');
        $disposition = $forceDownload ? 'attachment' : 'inline';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . basename($absolutePath) . '"')
            ->setBody(file_get_contents($absolutePath));
    }

    // ========== Admin: Update application status ==========
    public function updateStatus($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $status = $this->request->getPost('status');
        // Removed deprecated 'pending_for_next_interview'
        $allowed = ['pending', 'for_review', 'hired', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid status value');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found');
        }

        try {
            $applicationModel->update($id, [
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Status update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status');
        }

        return redirect()->back()->with('success', 'Status updated to ' . str_replace('_', ' ', $status));
    }
    // ========== IGT ADDITIONAL INTERVIEW (Interviewer Only) ==========
    public function igtForm($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            return redirect()->to('/interviewer/applications')->with('error', 'Application not found');
        }

        $existing = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['igt'])) {
                $existing = $decoded['igt'];
            }
        }

        return view('interviewer/igt_form', [
            'application' => $application,
            'igt' => $existing,
        ]);
    }

    public function igtSave($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            return redirect()->to('/interviewer/applications')->with('error', 'Application not found');
        }

        // Basic validation for IGT fields
        $validator = \Config\Services::validation();
        $rules = [
            // Core IGT fields (based on previous IGT application questions)
            'igt_program' => 'required|max_length[255]',
            'igt_application_date' => 'required|valid_date',
            'igt_tag_result' => 'required|in_list[Passed,Failed]',
            'igt_interviewer_name' => 'permit_empty|max_length[255]',
            'igt_basic_checkpoints' => 'permit_empty|max_length[255]',
            'igt_opportunity' => 'permit_empty|max_length[255]',
            'igt_availability' => 'permit_empty|max_length[255]',
            'igt_validated_source' => 'permit_empty|in_list[RSD,Other]',
            'igt_shift_preference' => 'permit_empty|max_length[255]',
            'igt_work_preference' => 'permit_empty|max_length[255]',
            'igt_expected_salary' => 'permit_empty|numeric',
            'igt_on_hold_salary' => 'permit_empty|numeric',
            'igt_pending_applications' => 'permit_empty|in_list[NONE,Pending]',
            'igt_current_location' => 'permit_empty|max_length[255]',
            'igt_commute' => 'permit_empty|max_length[255]',
            'igt_govt_numbers' => 'permit_empty|max_length[255]',
            'igt_education' => 'permit_empty|max_length[1500]',
            'igt_work_experience' => 'permit_empty|max_length[2000]',
            'igt_communication' => 'permit_empty|in_list[Good,Excellent,Fair,Poor]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Please correct the errors below');
        }

        // Merge existing notes and add/update the IGT section
        $notes = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $notes = $decoded;
            }
        }
        $notes['igt'] = [
            'candidate' => trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')) ?: null,
            'program' => trim((string) $this->request->getPost('igt_program')) ?: null,
            'application_date' => $this->request->getPost('igt_application_date') ?: null,
            'tag_result' => $this->request->getPost('igt_tag_result') ?: null,
            'interviewer_name' => trim((string) ($this->request->getPost('igt_interviewer_name') ?: (session()->get('first_name') . ' ' . session()->get('last_name')))) ?: null,
            'basic_checkpoints' => trim((string) $this->request->getPost('igt_basic_checkpoints')) ?: null,
            'opportunity' => trim((string) $this->request->getPost('igt_opportunity')) ?: null,
            'availability' => trim((string) $this->request->getPost('igt_availability')) ?: null,
            'validated_source' => $this->request->getPost('igt_validated_source') ?: null,
            'shift_preference' => trim((string) $this->request->getPost('igt_shift_preference')) ?: null,
            'work_preference' => trim((string) $this->request->getPost('igt_work_preference')) ?: null,
            'expected_salary' => $this->request->getPost('igt_expected_salary') !== '' ? (float) $this->request->getPost('igt_expected_salary') : null,
            'on_hold_salary' => $this->request->getPost('igt_on_hold_salary') !== '' ? (float) $this->request->getPost('igt_on_hold_salary') : null,
            'pending_applications' => $this->request->getPost('igt_pending_applications') ?: null,
            'current_location' => trim((string) $this->request->getPost('igt_current_location')) ?: null,
            'commute' => trim((string) $this->request->getPost('igt_commute')) ?: null,
            'govt_numbers' => trim((string) $this->request->getPost('igt_govt_numbers')) ?: null,
            'education' => trim((string) $this->request->getPost('igt_education')) ?: null,
            'work_experience' => trim((string) $this->request->getPost('igt_work_experience')) ?: null,
            'communication' => $this->request->getPost('igt_communication') ?: null,
            'updated_by' => session()->get('user_id'),
            'updated_at' => date('c'),
        ];

        try {
            $applicationModel->update($id, [
                'notes' => json_encode($notes),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'IGT save error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to save IGT interview.');
        }

        // Optional: update status if desired (commented out)
        // $applicationModel->update($id, ['status' => 'for_review']);

        // Notify applicant about the scheduled/updated IGT interview details (non-blocking)
        try {
            helper('url');
            $company = $application['company_name'] ?? 'Company';
            $applicantName = trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? ''));
            $to = $application['email_address'] ?? null;
            if ($to) {
                $subject = 'Your additional interview details — ' . $company;
                $html = '<p>Hi ' . esc($application['first_name'] ?? 'there') . ',</p>'
                    . '<p>We updated your additional interview details for <strong>' . esc($company) . '</strong>.</p>'
                    . '<ul>'
                    . '<li>Program: ' . esc($notes['igt']['program'] ?? 'N/A') . '</li>'
                    . '<li>Interview Date: ' . esc($notes['igt']['application_date'] ?? 'TBA') . '</li>'
                    . '<li>Result/Tag: ' . esc($notes['igt']['tag_result'] ?? 'TBA') . '</li>'
                    . '</ul>'
                    . '<p>If you have questions, just reply to this email.</p>'
                    . '<p>— RSD Recruitment</p>';
                // CC recruiter if available
                $opts = [];
                if (!empty($application['recruiter_email'])) {
                    $opts['cc'] = $application['recruiter_email'];
                }
                Mailer::send($to, $subject, $html, $opts);
            }
        } catch (\Throwable $e) {
            log_message('error', 'IGT email notification failed: ' . $e->getMessage());
        }

        return redirect()->to('/interviewer/applications/' . $id)->with('success', 'IGT interview saved.');
    }

    
}
