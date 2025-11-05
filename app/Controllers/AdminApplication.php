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

    public function edit($id)
    {
        // Admins and interviewers can edit; interviewers only their own
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            $prefix = session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin';
            return redirect()->to('/' . $prefix . '/applications')->with('error', 'Application not found');
        }

        if (session()->get('user_type') === 'interviewer' && (int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
            return redirect()->to('/interviewer/applications/' . $id)->with('error', 'You can only edit applications you created.');
        }

        // Decode notes for scheduling/igt checks
        $application['decoded_notes'] = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $application['decoded_notes'] = $decoded;
            }
        }

        return view('admin/application_edit', [ 'application' => $application ]);
    }

    public function update($id)
    {
        // Admins and interviewers can update; interviewers only their own
        if (!session()->get('isLoggedIn') || !in_array(session()->get('user_type'), ['admin', 'interviewer'])) {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            $prefix = session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin';
            return redirect()->to('/' . $prefix . '/applications')->with('error', 'Application not found');
        }
        if (session()->get('user_type') === 'interviewer' && (int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
            return redirect()->to('/interviewer/applications/' . $id)->with('error', 'You can only update applications you created.');
        }

        // Normalize human name inputs (trim, collapse spaces, NFC when available)
        $normalize = function ($s) {
            $s = is_string($s) ? $s : '';
            $s = preg_replace('/\s+/u', ' ', trim($s));
            if (class_exists('Normalizer')) {
                $s = \Normalizer::normalize($s, \Normalizer::FORM_C);
            }
            return $s;
        };
        $fn = $normalize($this->request->getPost('first_name'));
        $mn = $normalize($this->request->getPost('middle_name'));
        $ln = $normalize($this->request->getPost('last_name'));
        $sx = $normalize($this->request->getPost('suffix'));

        if (method_exists($this->request, 'setGlobal')) {
            $this->request->setGlobal('post', array_merge($this->request->getPost(), [
                'first_name' => $fn,
                'middle_name' => $mn,
                'last_name' => $ln,
                'suffix' => $sx,
            ]));
        }

        // Pre-clean mobile numbers
        $rawPhone = (string) $this->request->getPost('phone_number');
        $rawViber = (string) $this->request->getPost('viber_number');
        $cleanPhone = preg_replace('/\D/', '', $rawPhone);
        $cleanViber = preg_replace('/\D/', '', $rawViber);
        if (strpos($cleanPhone, '0') === 0) { $cleanPhone = substr($cleanPhone, 1); }
        if (strpos($cleanViber, '0') === 0) { $cleanViber = substr($cleanViber, 1); }
        if (method_exists($this->request, 'setGlobal')) {
            $this->request->setGlobal('post', array_merge($this->request->getPost(), [
                'phone_number' => $cleanPhone,
                'viber_number' => $cleanViber,
            ]));
        }

        // Validation rules (same as save)
        $rules = [
            'company_name' => 'required|in_list[Everise,IGT]',
            // Unicode-friendly name validation
            'first_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]",
                'errors' => [
                    'required' => 'First name is required.',
                    'min_length' => 'First name must not be empty.',
                    'max_length' => 'First name can be at most 100 characters.',
                    'regex_match' => 'First name can include letters, spaces, hyphens, and apostrophes only.',
                ],
            ],
            'middle_name' => [
                'rules' => "permit_empty|max_length[100]|regex_match[/^([\\p{L}\\p{M}]\\.?|[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*)$/u]",
                'errors' => [
                    'max_length' => 'Middle name can be at most 100 characters.',
                    'regex_match' => 'Middle name can include letters, a single initial with optional period, spaces, hyphens, and apostrophes.',
                ],
            ],
            'last_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]",
                'errors' => [
                    'required' => 'Last name is required.',
                    'min_length' => 'Last name must not be empty.',
                    'max_length' => 'Last name can be at most 100 characters.',
                    'regex_match' => 'Last name can include letters, spaces, hyphens, and apostrophes only.',
                ],
            ],
            'suffix' => [
                'rules' => "permit_empty|max_length[20]|regex_match[/^[A-Za-z0-9\.\\sIVXLCDMivxlcdm]{1,20}$/]",
                'errors' => [
                    'max_length' => 'Suffix can be at most 20 characters.',
                    'regex_match' => 'Suffix may include letters, periods, spaces, roman numerals, or small digits (e.g., Jr., III, 2nd).',
                ],
            ],
            'email_address' => 'required|valid_email|max_length[255]',
            'phone_number' => 'permit_empty|regex_match[/^9\d{9}$/]',
            'viber_number' => 'permit_empty|regex_match[/^9\d{9}$/]',
            'street_address' => 'permit_empty|max_length[255]',
            'barangay' => 'permit_empty|max_length[100]',
            'municipality' => 'permit_empty|max_length[100]',
            'province' => 'permit_empty|max_length[100]',
            'birthdate' => 'permit_empty|valid_date',
            'bpo_experience' => 'permit_empty|max_length[100]',
            'educational_attainment' => 'permit_empty|max_length[100]',
            'recruiter_email' => 'required|valid_email|max_length[255]',
            'next_interviewer_email' => 'permit_empty|valid_email|max_length[255]',
            'next_interview_datetime' => 'permit_empty',
            'next_interview_notes' => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Please correct the errors below');
        }

        // Age check
        $birthdate = $this->request->getPost('birthdate');
        if ($birthdate) {
            try {
                $birthdateObj = new \DateTime($birthdate);
                $today = new \DateTime();
                if ($birthdateObj > $today) {
                    return redirect()->back()->withInput()->with('error', 'Birthdate cannot be in the future')->with('field_error_birthdate', 'Birthdate cannot be in the future');
                }
                if ($today->diff($birthdateObj)->y < 18) {
                    return redirect()->back()->withInput()->with('error', 'Applicant must be at least 18 years old')->with('field_error_birthdate', 'Applicant must be at least 18 years old');
                }
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Invalid birthdate format')->with('field_error_birthdate', 'Invalid birthdate format');
            }
        }

        // Handle optional schedule update, but only if none exists yet (respect single-interview rule)
        $existingNotes = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $existingNotes = $decoded;
            }
        }
        $hasExistingSecond = !empty($existingNotes['next_interview']) || !empty($existingNotes['igt']);

        $nextInterviewerEmail = trim((string) $this->request->getPost('next_interviewer_email'));
        $nextInterviewDTInput = trim((string) $this->request->getPost('next_interview_datetime'));
        $nextInterviewNotes   = trim((string) $this->request->getPost('next_interview_notes'));
        $nextInterview = null;

        if (!$hasExistingSecond && ($nextInterviewerEmail !== '' || $nextInterviewDTInput !== '' || $nextInterviewNotes !== '')) {
            if ($nextInterviewerEmail === '' || $nextInterviewDTInput === '') {
                return redirect()->back()->withInput()->with('error', 'To schedule another interview, provide the interviewer email and date/time.');
            }
            try {
                $dt = new \DateTime($nextInterviewDTInput);
                $iso = $dt->format(DATE_ATOM);
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

        // Prepare update data
        $data = [
            'company_name' => esc($this->request->getPost('company_name')),
            'first_name' => esc(trim($this->request->getPost('first_name'))),
            'middle_name' => $mn !== '' ? esc($mn) : null,
            'last_name' => esc(trim($this->request->getPost('last_name'))),
            'suffix' => $sx !== '' ? esc($sx) : null,
            'email_address' => filter_var($this->request->getPost('email_address'), FILTER_SANITIZE_EMAIL),
            'phone_number' => (function () {
                $raw = (string) $this->request->getPost('phone_number');
                $digits = preg_replace('/\D/', '', $raw ?? '');
                if ($digits === '') { return null; }
                if (strpos($digits, '63') === 0) { $digits = substr($digits, 2); }
                if (strpos($digits, '0') === 0) { $digits = substr($digits, 1); }
                return '+63' . $digits;
            })(),
            'viber_number' => (function () {
                $raw = (string) $this->request->getPost('viber_number');
                $digits = preg_replace('/\D/', '', $raw ?? '');
                if ($digits === '') { return null; }
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
        ];

        // Merge notes for new schedule if applicable
        $notesChanged = false;
        if ($nextInterview) {
            $existingNotes['next_interview'] = $nextInterview;
            $notesChanged = true;
        }

        // Apply IGT edits if present in the edit form
        if ($this->request->getPost('igt_present')) {
            $newIgt = [
                'candidate' => trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')) ?: null,
                'program' => trim((string) $this->request->getPost('igt_program')) ?: null,
                'application_date' => $this->request->getPost('igt_application_date') ?: null,
                'tag_result' => $this->request->getPost('igt_tag_result') ?: null,
                'interviewer_name' => trim((string) ($this->request->getPost('igt_interviewer_name') ?: ($existingNotes['igt']['interviewer_name'] ?? ''))) ?: null,
                'basic_checkpoints' => trim((string) $this->request->getPost('igt_basic_checkpoints')) ?: null,
                'opportunity' => trim((string) $this->request->getPost('igt_opportunity')) ?: null,
                'availability' => trim((string) $this->request->getPost('igt_availability')) ?: null,
                'validated_source' => $this->request->getPost('igt_validated_source') ?: null,
                'shift_preference' => $existingNotes['igt']['shift_preference'] ?? null,
                'work_preference' => $existingNotes['igt']['work_preference'] ?? null,
                'expected_salary' => $this->request->getPost('igt_expected_salary') !== '' ? (float) $this->request->getPost('igt_expected_salary') : ($existingNotes['igt']['expected_salary'] ?? null),
                'on_hold_salary' => $this->request->getPost('igt_on_hold_salary') !== '' ? (float) $this->request->getPost('igt_on_hold_salary') : ($existingNotes['igt']['on_hold_salary'] ?? null),
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
            $existingNotes['igt'] = array_merge($existingNotes['igt'] ?? [], $newIgt);
            $notesChanged = true;

            // If IGT tag_result becomes Passed, keep existing endorsement flow logic intact (no direct status change here)
        }

        if ($notesChanged) {
            $data['notes'] = json_encode($existingNotes);
        }

        // Handle resume replacement (optional)
        $resume = $this->request->getFile('resume');
        if ($resume && $resume->isValid() && !$resume->hasMoved()) {
            $allowedMimeTypes = ['application/pdf'];
            $fileMimeType = $resume->getMimeType();
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                return redirect()->back()->withInput()->with('error', 'Only PDF files are allowed for resume')->with('field_error_resume', 'Only PDF files are allowed');
            }
            if (strtolower($resume->getExtension()) !== 'pdf') {
                return redirect()->back()->withInput()->with('error', 'Only PDF files are allowed for resume')->with('field_error_resume', 'Only PDF files are allowed');
            }
            if ($resume->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Resume file size must not exceed 5MB')->with('field_error_resume', 'File size must not exceed 5MB');
            }
            $safeName = $resume->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/resumes';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0755, true); }
            try {
                $resume->move($uploadPath, $safeName);
                $data['resume_path'] = 'writable/uploads/resumes/' . $safeName;
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload resume file')->with('field_error_resume', 'Upload failed');
            }
        }

        try {
            $applicationModel->update($id, $data);
            // Log update and optionally IGT update
            try {
                $log = new SystemLogModel();
                $log->logActivity('Application Updated', 'application', 'Updated application #' . $id, session()->get('user_id'));
                if ($this->request->getPost('igt_present')) {
                    $log->logActivity('IGT Updated', 'application', 'Updated IGT for application #' . $id, session()->get('user_id'));
                }
            } catch (\Throwable $e) { /* ignore */ }
        } catch (\Throwable $e) {
            log_message('error', 'Application update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update application');
        }

        $prefix = session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin';
        return redirect()->to('/' . $prefix . '/applications/' . $id)->with('success', 'Application updated successfully.');
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

        // Normalize human name inputs (trim, collapse spaces, NFC when available)
        $normalize = function ($s) {
            $s = is_string($s) ? $s : '';
            $s = preg_replace('/\s+/u', ' ', trim($s));
            if (class_exists('Normalizer')) {
                $s = \Normalizer::normalize($s, \Normalizer::FORM_C);
            }
            return $s;
        };
        $fn = $normalize($this->request->getPost('first_name'));
        $mn = $normalize($this->request->getPost('middle_name'));
        $ln = $normalize($this->request->getPost('last_name'));
        $sx = $normalize($this->request->getPost('suffix'));
        if (method_exists($this->request, 'setGlobal')) {
            $this->request->setGlobal('post', array_merge($this->request->getPost(), [
                'first_name' => $fn,
                'middle_name' => $mn,
                'last_name' => $ln,
                'suffix' => $sx,
            ]));
        }

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

        // Define comprehensive validation rules (Unicode-friendly)
        $validationRules = [
            'company_name' => [
                'rules' => 'required|in_list[Everise,IGT]',
                'errors' => [
                    'required' => 'Please select a company',
                    'in_list' => 'Invalid company selection'
                ]
            ],
            // Unicode-friendly name validation (match update())
            'first_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]",
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must not be empty',
                    'max_length' => 'First name can be at most 100 characters',
                    'regex_match' => 'First name can include letters, spaces, hyphens, and apostrophes only',
                ]
            ],
            'middle_name' => [
                'rules' => "permit_empty|max_length[100]|regex_match[/^([\\p{L}\\p{M}]\\.?|[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*)$/u]",
                'errors' => [
                    'max_length' => 'Middle name can be at most 100 characters',
                    'regex_match' => 'Middle name can include letters, a single initial with optional period, spaces, hyphens, and apostrophes',
                ]
            ],
            'last_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]",
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must not be empty',
                    'max_length' => 'Last name can be at most 100 characters',
                    'regex_match' => 'Last name can include letters, spaces, hyphens, and apostrophes only',
                ]
            ],
            'suffix' => [
                'rules' => "permit_empty|max_length[20]|regex_match[/^[A-Za-z0-9\.\\sIVXLCDMivxlcdm]{1,20}$/]",
                'errors' => [
                    'max_length' => 'Suffix can be at most 20 characters',
                    'regex_match' => 'Suffix may include letters, periods, spaces, roman numerals, or small digits (e.g., Jr., III, 2nd)',
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
        $middleName = trim((string)$this->request->getPost('middle_name'));
        $suffix = trim((string)$this->request->getPost('suffix'));
        $data = [
            'company_name' => esc($this->request->getPost('company_name')),
            'first_name' => esc(trim($this->request->getPost('first_name'))),
            'middle_name' => $middleName !== '' ? esc($middleName) : null,
            'last_name' => esc(trim($this->request->getPost('last_name'))),
            'suffix' => $suffix !== '' ? esc($suffix) : null,
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

        // Include notes payload for next interview and name meta if provided
        $notesPayload = [];
        if ($nextInterview) { $notesPayload['next_interview'] = $nextInterview; }
        if ($middleName !== '' || $suffix !== '') {
            $notesPayload['name'] = [
                'middle_name' => $middleName !== '' ? $middleName : null,
                'suffix' => $suffix !== '' ? $suffix : null,
            ];
        }
        if (!empty($notesPayload)) { $data['notes'] = json_encode($notesPayload); }

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
                    $applicantName = trim((
                        ($data['first_name'] ?? '') . ' ' .
                        ($middleName ? ($middleName . ' ') : '') .
                        ($data['last_name'] ?? '') .
                        ($suffix ? (' ' . $suffix) : '')
                    ));
                    $company = $data['company_name'] ?? 'Company';
                    $viewLinkAdmin = base_url('admin/applications/' . $applicationId);
                    $viewLinkInterviewer = base_url('interviewer/applications/' . $applicationId);

                    // Email to recruiter
                    $recruiterTo = $data['recruiter_email'] ?? null;
                    if ($recruiterTo) {
                        $subject = 'New Application: ' . $applicantName . ' for ' . $company;
                        $location = trim(($data['street_address'] ?? '') . ', ' . ($data['barangay'] ?? '') . ', ' . ($data['municipality'] ?? '') . ', ' . ($data['province'] ?? ''));
                        $html = view('emails/recruiter_notice', [
                            'applicantName' => $applicantName,
                            'company' => $company,
                            'email' => $data['email_address'] ?? '',
                            'phone' => $data['phone_number'] ?? 'N/A',
                            'location' => $location,
                            'linkAdmin' => $viewLinkAdmin,
                            'linkInterviewer' => $viewLinkInterviewer,
                        ]);
                        Mailer::send($recruiterTo, $subject, $html, ['log' => true, 'replyTo' => $data['email_address'] ?? null]);
                    }

                    // Acknowledgement to applicant (include schedule if provided)
                    $applicantTo = $data['email_address'] ?? null;
                    if ($applicantTo) {
                        $subjectApplicant = 'We received your application' . ($company ? (' — ' . $company) : '') . ' | RSD';
                        $htmlApplicant = view('emails/applicant_ack', [
                            'firstName' => $data['first_name'] ?? 'there',
                            'company' => $company,
                            'scheduleHuman' => $nextInterview['human'] ?? null,
                            'notes' => $nextInterview['notes'] ?? null,
                        ]);
                        $opts = ['log' => true];
                        // Attach ICS to applicant too if schedule exists
                        if ($nextInterview) {
                            $opts['ics'] = [
                                'summary' => 'Interview — ' . $company,
                                'description' => 'Interview with ' . $company . ' for ' . $applicantName,
                                'start' => $nextInterview['datetime'] ?? null,
                                'end' => null, // default +1h
                                'organizer' => [
                                    'name' => trim((session()->get('first_name') . ' ' . session()->get('last_name'))),
                                    'email' => config('Email')->fromEmail ?: config('Email')->SMTPUser,
                                ],
                                'attendees' => [ ['name' => $data['first_name'] ?? 'Applicant', 'email' => $applicantTo] ],
                            ];
                        }
                        Mailer::send($applicantTo, $subjectApplicant, $htmlApplicant, $opts);
                    }

                    // Schedule email to another interviewer, if provided
                    if ($nextInterview && !empty($nextInterview['email'])) {
                        $subjectNI = 'Interview scheduled: ' . $applicantName . ' — ' . ($nextInterview['human'] ?? '');
                        $htmlNI = view('emails/interviewer_schedule', [
                            'scheduledBy' => trim((session()->get('first_name') . ' ' . session()->get('last_name'))),
                            'applicantName' => $applicantName,
                            'company' => $company,
                            'scheduleHuman' => $nextInterview['human'] ?? '',
                            'notes' => $nextInterview['notes'] ?? null,
                            'email' => $data['email_address'] ?? '',
                            'phone' => $data['phone_number'] ?? 'N/A',
                            'linkAdmin' => $viewLinkAdmin,
                            'linkInterviewer' => $viewLinkInterviewer,
                        ]);
                        $opts = ['log' => true];
                        if (!empty($data['recruiter_email'])) { $opts['cc'] = $data['recruiter_email']; }
                        // Attach ICS invite
                        $opts['ics'] = [
                            'summary' => 'Interview — ' . $company . ' — ' . $applicantName,
                            'description' => 'Interview for ' . $applicantName . ' (' . $company . ')',
                            'start' => $nextInterview['datetime'] ?? null,
                            'end' => null,
                            'organizer' => [
                                'name' => trim((session()->get('first_name') . ' ' . session()->get('last_name'))),
                                'email' => config('Email')->fromEmail ?: config('Email')->SMTPUser,
                            ],
                            'attendees' => [
                                ['name' => $applicantName, 'email' => $data['email_address'] ?? ''],
                                ['name' => 'Interviewer', 'email' => $nextInterview['email']]
                            ],
                        ];
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
        $apps = $applicationModel->orderBy('created_at', 'DESC')->findAll();

        // Map interviewer IDs to names/emails
        $interviewers = [];
        $ids = [];
        foreach ($apps as $a) {
            if (!empty($a['interviewed_by'])) { $ids[(int)$a['interviewed_by']] = true; }
        }
        if (!empty($ids)) {
            $userModel = new \App\Models\UserModel();
            $rows = $userModel->whereIn('id', array_keys($ids))->findAll();
            foreach ($rows as $u) {
                $label = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                $email = $u['email'] ?? '';
                $interviewers[(int)$u['id']] = $label !== '' ? ($label . ' (' . $email . ')') : $email;
            }
        }

        $data['applications'] = $apps;
        $data['interviewers'] = $interviewers;

        return view('admin/applications_list', $data);
    }

    public function interviewerList()
    {
        // Interviewers can view only their submitted applications
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $apps = $applicationModel
            ->where('interviewed_by', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();
        $data['applications'] = $apps;
        $label = trim((session()->get('first_name') . ' ' . session()->get('last_name')));
        $email = session()->get('email');
        $data['interviewers'] = [ (int)session()->get('user_id') => ($label ? ($label . ' (' . $email . ')') : $email) ];
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

        // Ownership check: interviewers can only view their own applications
        if (session()->get('user_type') === 'interviewer') {
            if ((int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
                return redirect()->to('/interviewer/applications')->with('error', 'You can only view your own applications.');
            }
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

        // Ownership check for interviewers
        if (session()->get('user_type') === 'interviewer') {
            if ((int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
                return redirect()->to('/interviewer/applications')->with('error', 'You can only access resumes for your own applications.');
            }
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
        // Allowed statuses (interviewer-triggered approval uses a dedicated endpoint)
        $allowed = ['pending', 'for_review', 'hired', 'rejected', 'approved_for_endorsement'];
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
            // Log status change
            try {
                $log = new SystemLogModel();
                $log->logActivity(
                    'Status Update',
                    'application',
                    'Updated application #' . $id . ' to ' . $status,
                    session()->get('user_id')
                );
            } catch (\Throwable $e) {
                // non-blocking
            }
        } catch (\Exception $e) {
            log_message('error', 'Status update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status');
        }

        return redirect()->back()->with('success', 'Status updated to ' . str_replace('_', ' ', $status));
    }
    
    // ========== Interviewer: Approve for endorsement ==========
    public function approveForEndorsement($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'interviewer') {
            return redirect()->to('/auth/login');
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($id);
        if (!$application) {
            return redirect()->to('/interviewer/applications')->with('error', 'Application not found');
        }

        // Optional: scope so interviewers can only endorse their own candidates
        if ((int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
            return redirect()->to('/interviewer/applications/' . $id)->with('error', 'You can only endorse applications you created.');
        }

        // Require evidence of a second interview step (schedule or IGT Passed)
        $hasNextInterview = false;
        $igtPassed = false;
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $hasNextInterview = !empty($decoded['next_interview']) && !empty($decoded['next_interview']['datetime']);
                if (!empty($decoded['igt']) && is_array($decoded['igt'])) {
                    $igtPassed = (isset($decoded['igt']['tag_result']) && strtoupper($decoded['igt']['tag_result']) === 'PASSED');
                }
            }
        }

        if (!$hasNextInterview && !$igtPassed) {
            return redirect()->to('/interviewer/applications/' . $id)
                ->with('error', 'Please complete the second interview (schedule or IGT with Passed result) before endorsing.');
        }

        try {
            $applicationModel->update($id, [ 'status' => 'approved_for_endorsement' ]);
        } catch (\Throwable $e) {
            log_message('error', 'Approval error: ' . $e->getMessage());
            return redirect()->to('/interviewer/applications/' . $id)->with('error', 'Failed to set status.');
        }

        // Log action
        try {
            $systemLog = new SystemLogModel();
            $systemLog->logActivity(
                'Approved for Endorsement',
                'application',
                'Application #' . $id . ' marked as approved for endorsement',
                session()->get('user_id')
            );
        } catch (\Throwable $e) { /* ignore */ }

        // Notify recruiter and applicant
        try {
            helper('url');
            $applicantName = trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? ''));
            $company = $application['company_name'] ?? 'Company';
            $viewLinkAdmin = base_url('admin/applications/' . $id);
            $viewLinkInterviewer = base_url('interviewer/applications/' . $id);

            // To recruiter (primary)
            $recruiterTo = $application['recruiter_email'] ?? null;
            if ($recruiterTo) {
                $subject = 'Endorsed: ' . $applicantName . ' — ' . $company;
                $html = view('emails/endorsement_recruiter', [
                    'applicantName' => $applicantName,
                    'company' => $company,
                    'email' => $application['email_address'] ?? '',
                    'phone' => $application['phone_number'] ?? 'N/A',
                    'linkAdmin' => $viewLinkAdmin,
                    'linkInterviewer' => $viewLinkInterviewer,
                ]);
                $opts = ['log' => true, 'replyTo' => $application['email_address'] ?? null];
                // CC the interviewer who endorsed
                if (session()->get('email')) { $opts['cc'] = session()->get('email'); }
                \App\Libraries\Mailer::send($recruiterTo, $subject, $html, $opts);
            }

            // Inform applicant
            $applicantTo = $application['email_address'] ?? null;
            if ($applicantTo) {
                $subjectA = 'You have been endorsed — ' . $company;
                $htmlA = view('emails/endorsement_applicant', [
                    'firstName' => $application['first_name'] ?? 'there',
                    'company' => $company,
                    'recruiterEmail' => $application['recruiter_email'] ?? null,
                ]);
                \App\Libraries\Mailer::send($applicantTo, $subjectA, $htmlA, ['log' => true]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Endorsement email error: ' . $e->getMessage());
        }

        return redirect()->to('/interviewer/applications/' . $id)->with('success', 'Marked as Approved for Endorsement and notifications sent.');
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

        // Ownership check: interviewer may only add IGT to their own applications
        if ((int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
            return redirect()->to('/interviewer/applications')->with('error', 'You can only add IGT to applications you created.');
        }

        $existing = [];
        if (!empty($application['notes'])) {
            $decoded = json_decode($application['notes'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (isset($decoded['igt'])) { $existing = $decoded['igt']; }
                // New rule: IGT is the required second interview. Allow creating IGT
                // regardless of any scheduled next interview; block only if IGT already exists.
                if (!empty($decoded['igt'])) {
                    return redirect()->to('/interviewer/applications/' . $id)
                        ->with('error', 'IGT already exists for this application.');
                }
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

        // Ownership check
        if ((int)($application['interviewed_by'] ?? 0) !== (int)session()->get('user_id')) {
            return redirect()->to('/interviewer/applications')->with('error', 'You can only update IGT for applications you created.');
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
            // Log IGT save
            try {
                $log = new SystemLogModel();
                $log->logActivity(
                    'IGT Saved',
                    'application',
                    'Saved IGT for application #' . $id . ' (' . ($notes['igt']['program'] ?? 'N/A') . ')',
                    session()->get('user_id')
                );
            } catch (\Throwable $e) {
                // non-blocking
            }
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
                $html = view('emails/igt_update', [
                    'firstName' => $application['first_name'] ?? 'there',
                    'company' => $company,
                    'program' => $notes['igt']['program'] ?? 'N/A',
                    'applicationDate' => $notes['igt']['application_date'] ?? 'TBA',
                    'tagResult' => $notes['igt']['tag_result'] ?? 'TBA',
                ]);
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
