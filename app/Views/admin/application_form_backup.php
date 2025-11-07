<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        .map-button:hover {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
        }
        .map-button svg {
            width: 18px;
            height: 18px;
        }
        .map-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .map-modal.active {
            display: flex;
        }
        .map-modal-content {
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .map-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .map-modal-header h3 {
            margin: 0;
            font-size: 20px;
            color: #1a202c;
        }
        .map-close-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #718096;
            transition: color 0.2s;
        }
        .map-close-btn:hover {
            color: #1a202c;
        }
        #map {
            height: 500px;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .map-search-box {
            margin-bottom: 16px;
            position: relative;
        }
        .map-search-box input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .search-suggestions.active {
            display: block;
        }
        .suggestion-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f7fafc;
            transition: background 0.2s;
        }
        .suggestion-item:hover {
            background: #f7fafc;
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-name {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 4px;
        }
        .suggestion-address {
            font-size: 12px;
            color: #718096;
        }
        .search-loading {
            padding: 12px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .map-confirm-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .map-confirm-btn:hover {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php if (session()->get('user_type') === 'interviewer'): ?>
            <?= view('components/sidebar') ?>
        <?php else: ?>
            
        <?php endif; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Application Form</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="application-form-container">
                    <div class="form-card">
                        <div class="form-header">
                            <h2>Company Application Form</h2>
                            <p>Fill in the applicant details below</p>
                            <div id="autoSaveIndicator" style="display: none;">
                                <small style="color: var(--dark-text); opacity: 0.8;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Form data auto-saved
                                </small>
                            </div>
                        </div>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                            <script>
                                // Clear localStorage on successful submission
                                localStorage.removeItem('applicationFormData');
                            </script>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-error">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <!-- Initial Interview Form -->
                        <form action="<?= base_url('admin/application/save') ?>" method="POST" id="applicationForm" enctype="multipart/form-data">
                            
                            <!-- Company Selection at Top -->
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="company_name">Choose Company <span class="required">*</span></label>
                                    <select id="company_name" name="company_name" required>
                                        <option value="">-- Select Company --</option>
                                        <option value="RSD">RSD</option>
                                        <option value="IGT">IGT</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="required">*</span></label>
                                    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="required">*</span></label>
                                    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email_address">Email Address <span class="required">*</span></label>
                                    <input type="email" id="email_address" name="email_address" placeholder="applicant@email.com" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <div class="phone-input-wrapper">
                                        <span class="phone-prefix">+63</span>
                                        <input type="tel" id="phone_number" name="phone_number" placeholder="912 345 6789" maxlength="12">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="viber_number">Viber Number</label>
                                    <div class="phone-input-wrapper">
                                        <span class="phone-prefix">+63</span>
                                        <input type="tel" id="viber_number" name="viber_number" placeholder="912 345 6789" maxlength="12">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="street_address">Complete Street Address</label>
                                    <input type="text" id="street_address" name="street_address" placeholder="House No., Street Name, Building, Zip Code">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="barangay">Barangay / Subdivision</label>
                                    <input type="text" id="barangay" name="barangay" placeholder="Enter barangay or subdivision">
                                </div>

                                <div class="form-group">
                                    <label for="municipality">Municipality / City</label>
                                    <input type="text" id="municipality" name="municipality" placeholder="Enter city or municipality">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="province">Province / Region</label>
                                    <input type="text" id="province" name="province" placeholder="Enter province or region">
                                </div>

                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" id="birthdate" name="birthdate">
                                    <small class="field-hint">Must be at least 18 years old</small>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <button type="button" class="map-button" onclick="openMapModal()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="10" r="3"/>
                                        <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"/>
                                    </svg>
                                    Click Map to Auto-Fill Address
                                </button>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bpo_experience">Total BPO Experience</label>
                                    <input type="text" id="bpo_experience" name="bpo_experience" placeholder="e.g., 2 years 6 months">
                                </div>

                                <div class="form-group">
                                    <label for="educational_attainment">Educational Attainment</label>
                                    <input type="text" id="educational_attainment" name="educational_attainment" placeholder="e.g., Bachelor's Degree">
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="resume">Resume (PDF only)</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="resume" name="resume" accept=".pdf" onchange="updateFileName(this)">
                                    <div class="file-upload-display">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        <span class="file-name">Choose PDF file or drag here</span>
                                        <span class="file-size"></span>
                                    </div>
                                </div>
                                <div class="file-preview" id="filePreview" style="display: none;">
                                    <div class="preview-header">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                            <polyline points="10 9 9 9 8 9"/>
                                        </svg>
                                        <div class="preview-info">
                                            <span class="preview-filename"></span>
                                            <span class="preview-filesize"></span>
                                        </div>
                                    </div>
                                    <div class="preview-actions">
                                        <button type="button" class="btn-preview" onclick="previewPDF()">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Preview
                                        </button>
                                        <button type="button" class="btn-remove" onclick="removeFile()">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- PDF Preview Modal -->
                            <div class="modal" id="pdfModal" onclick="closeModal(event)">
                                <div class="modal-content" onclick="event.stopPropagation()">
                                    <div class="modal-header">
                                        <h3>Resume Preview</h3>
                                        <button type="button" class="modal-close" onclick="closeModal(event)">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe id="pdfViewer" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group full-width recruiter-box">
                                <label for="recruiter_email">Send Details To <span class="required">*</span></label>
                                <input type="email" id="recruiter_email" name="recruiter_email" placeholder="recruiter@company.com" required>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Entry</button>
                                <button type="button" class="btn btn-secondary" onclick="downloadCSV()">Download CSV</button>
                                <button type="button" class="btn btn-primary" onclick="emailInfo()">Email This Info</button>
                                <button type="reset" class="btn btn-outline" onclick="clearForm()">Clear Form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?= view('components/sidebar_script') ?>
    
    <script>
                            
                            <h3 style="text-align: center; margin-bottom: 30px; color: #2d3748;">IGT Application Form</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_candidate">CANDIDATE</label>
                                    <input type="text" id="igt_candidate" name="first_name" placeholder="Candidate Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="igt_program">PROGRAM</label>
                                    <input type="text" id="igt_program" name="program" placeholder="Travel Account - Blended" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_date">DATE</label>
                                    <input type="date" id="igt_date" name="application_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="igt_tag_result">TAG Result</label>
                                    <select id="igt_tag_result" name="tag_result" required>
                                        <option value="">Select Result</option>
                                        <option value="Passed">Passed</option>
                                        <option value="Failed">Failed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_interviewer">Interviewer's Name</label>
                                    <input type="text" id="igt_interviewer" name="interviewer_name" placeholder="Interviewer Name">
                                </div>

                                <div class="form-group">
                                    <label for="igt_basic_checkpoints">BASIC CHECKPOINTS</label>
                                    <input type="text" id="igt_basic_checkpoints" name="basic_checkpoints">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_opportunity">OPPORTUNITY</label>
                                    <input type="text" id="igt_opportunity" name="opportunity">
                                </div>

                                <div class="form-group">
                                    <label for="igt_availability">AVAILABILITY</label>
                                    <input type="text" id="igt_availability" name="availability" placeholder="ASAP">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_validated_source">VALIDATED SOURCE</label>
                                    <select id="igt_validated_source" name="validated_source">
                                        <option value="">Select Source</option>
                                        <option value="RSD">RSD</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="igt_shift_preference">SHIFT PREFERENCE</label>
                                    <input type="text" id="igt_shift_preference" name="shift_preference" placeholder="Good working with AM/Night/Graveyard">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_work_preference">WORK PREFERENCE</label>
                                    <input type="text" id="igt_work_preference" name="work_preference" placeholder="Good work at home">
                                </div>

                                <div class="form-group">
                                    <label for="igt_expected_salary">EXPECTED / NON-NEGOTIABLE SALARY</label>
                                    <input type="number" id="igt_expected_salary" name="expected_salary" placeholder="19,000">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_on_hold_salary">ON-HOLD SALARY</label>
                                    <input type="number" id="igt_on_hold_salary" name="on_hold_salary" placeholder="20,000">
                                </div>

                                <div class="form-group">
                                    <label for="igt_pending_applications">PENDING APPLICATIONS</label>
                                    <select id="igt_pending_applications" name="pending_applications">
                                        <option value="NONE">NONE</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_current_location">CURRENT LOCATION</label>
                                <input type="text" id="igt_current_location" name="street_address" placeholder="Dasmarinas Cavite, or Dasmarinas - 30 mins - Balic-Balic">
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_commute">COMMUTE</label>
                                <input type="text" id="igt_commute" name="municipality" placeholder="NONE">
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_govt_numbers">GOVERNMENT NUMBERS</label>
                                <input type="text" id="igt_govt_numbers" name="barangay" placeholder="Completed">
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_education">EDUCATION</label>
                                <textarea id="igt_education" name="educational_attainment" rows="3" placeholder="College - any course, Batch year 2018 with Diploma Industrial technology - 2005&#10;Telephone operator Years: WAH:1/20 until account closed&#10;Voice International healthcare- seasonal account"></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_work_experience">WORK EXPERIENCE (Company / Position / inclusive dates / Reason for leaving)</label>
                                <textarea id="igt_work_experience" name="bpo_experience" rows="4" placeholder="Concentrix - Lipa Batangas - 2.00005 - Personal account&#10;VXI - Telco - 25,0000 - Blended - 2"></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label for="igt_communication">COMMUNICATION ASSESSMENT</label>
                                <select id="igt_communication" name="province">
                                    <option value="Good">Good</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Poor">Poor</option>
                                </select>
                            </div>

                            <div class="form-group full-width recruiter-box">
                                <label for="igt_recruiter_email">Send Details To <span class="required">*</span></label>
                                <input type="email" id="igt_recruiter_email" name="recruiter_email" placeholder="recruiter@company.com" required>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Entry</button>
                                <button type="button" class="btn btn-secondary" onclick="downloadCSV()">Download CSV</button>
                                <button type="button" class="btn btn-primary" onclick="emailInfo()">Email This Info</button>
                                <button type="reset" class="btn btn-outline" onclick="resetToCompanySelection()">Clear Form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?= view('components/sidebar_script') ?>
    
    <script>
        // Company form selection
        function showCompanyForm() {
            const selectedCompany = document.getElementById('company_select').value;
            const companySelection = document.getElementById('companySelection');
            const rsdForm = document.getElementById('rsdForm');
            const igtForm = document.getElementById('igtForm');

            if (selectedCompany === 'RSD') {
                companySelection.style.display = 'none';
                rsdForm.style.display = 'block';
                igtForm.style.display = 'none';
            } else if (selectedCompany === 'IGT') {
                companySelection.style.display = 'none';
                rsdForm.style.display = 'none';
                igtForm.style.display = 'block';
            }
        }

        function resetToCompanySelection() {
            document.getElementById('companySelection').style.display = 'block';
            document.getElementById('rsdForm').style.display = 'none';
            document.getElementById('igtForm').style.display = 'none';
            document.getElementById('company_select').value = '';
        }

        // Clear form data if user just logged in
        <?php if (session()->get('justLoggedIn')): ?>
            localStorage.removeItem('applicationFormData');
            <?php session()->remove('justLoggedIn'); ?>
        <?php endif; ?>

        // Load saved form data on page load
        window.addEventListener('DOMContentLoaded', function() {
            loadFormData();
        });

        // Save form data to localStorage on input (works for both RSD and IGT forms)
        const formInputs = document.querySelectorAll('#rsdForm input:not([type="file"]), #rsdForm select, #rsdForm textarea, #igtForm input:not([type="file"]), #igtForm select, #igtForm textarea');
        formInputs.forEach(input => {
            input.addEventListener('input', saveFormData);
            input.addEventListener('change', saveFormData);
        });

        function saveFormData() {
            const formData = {};
            formInputs.forEach(input => {
                if (input.type !== 'file') {
                    formData[input.id || input.name] = input.value;
                }
            });
            localStorage.setItem('applicationFormData', JSON.stringify(formData));
            
            // Show auto-save indicator
            const indicator = document.getElementById('autoSaveIndicator');
            indicator.style.display = 'block';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 2000);
        }

        function loadFormData() {
            const savedData = localStorage.getItem('applicationFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                let hasData = false;
                formInputs.forEach(input => {
                    const key = input.id || input.name;
                    if (formData[key]) {
                        input.value = formData[key];
                        hasData = true;
                    }
                });
                
                // Show notification if data was restored
                if (hasData) {
                    const indicator = document.getElementById('autoSaveIndicator');
                    indicator.innerHTML = '<small style="color: var(--dark-text); opacity: 0.8;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path d="M12 2v12l4 2"/><circle cx="12" cy="12" r="10"/></svg>Previous data restored</small>';
                    indicator.style.display = 'block';
                    setTimeout(() => {
                        indicator.style.display = 'none';
                    }, 3000);
                }
            }
        }

        // Clear localStorage when form is successfully submitted (both RSD and IGT)
        document.querySelectorAll('#rsdForm, #igtForm').forEach(form => {
            form.addEventListener('submit', function() {
                // Check if there's a success message (will be on next page load)
                setTimeout(() => {
                    const successAlert = document.querySelector('.alert-success');
                    if (successAlert) {
                        localStorage.removeItem('applicationFormData');
                    }
                }, 100);
            });
        });

        // Phone number formatting and validation
        const phoneInput = document.getElementById('phone_number');
        const viberInput = document.getElementById('viber_number');

        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, ''); // Remove non-digits
            
            // Ensure it starts with 9
            if (value.length > 0 && value[0] !== '9') {
                value = '9' + value;
            }
            
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            // Format as 9XX XXX XXXX
            if (value.length > 6) {
                value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
            } else if (value.length > 3) {
                value = value.substring(0, 3) + ' ' + value.substring(3);
            }
            
            input.value = value;
        }

        phoneInput.addEventListener('input', function() {
            formatPhoneNumber(this);
        });

        viberInput.addEventListener('input', function() {
            formatPhoneNumber(this);
        });

        // Prevent non-digit input
        [phoneInput, viberInput].forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (!/\d/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                    e.preventDefault();
                }
            });
        });

        // Set max date for birthdate (must be at least 18 years old)
        const birthdateInput = document.getElementById('birthdate');
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        const maxDateString = maxDate.toISOString().split('T')[0];
        birthdateInput.setAttribute('max', maxDateString);

        // Validate birthdate on change
        birthdateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const eighteenYearsAgo = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
            
            if (selectedDate > eighteenYearsAgo) {
                alert('Applicant must be at least 18 years old.');
                this.value = '';
            }
        });

        function updateFileName(input) {
            const fileDisplay = input.parentElement.querySelector('.file-upload-display');
            const fileName = fileDisplay.querySelector('.file-name');
            const fileSize = fileDisplay.querySelector('.file-size');
            const filePreview = document.getElementById('filePreview');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Update upload display
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                fileDisplay.classList.add('has-file');
                
                // Show preview card
                filePreview.style.display = 'block';
                document.querySelector('.preview-filename').textContent = file.name;
                document.querySelector('.preview-filesize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                
                // Hide upload box
                fileDisplay.parentElement.style.display = 'none';
            } else {
                fileName.textContent = 'Choose PDF file or drag here';
                fileSize.textContent = '';
                fileDisplay.classList.remove('has-file');
                filePreview.style.display = 'none';
                fileDisplay.parentElement.style.display = 'block';
            }
        }

        function previewPDF() {
            const fileInput = document.getElementById('resume');
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const fileURL = URL.createObjectURL(file);
                const modal = document.getElementById('pdfModal');
                const viewer = document.getElementById('pdfViewer');
                
                viewer.src = fileURL;
                modal.style.display = 'flex';
            }
        }

        function closeModal(event) {
            const modal = document.getElementById('pdfModal');
            modal.style.display = 'none';
            document.getElementById('pdfViewer').src = '';
        }

        function removeFile() {
            const fileInput = document.getElementById('resume');
            const fileDisplay = document.querySelector('.file-upload-display');
            const filePreview = document.getElementById('filePreview');
            
            fileInput.value = '';
            fileDisplay.querySelector('.file-name').textContent = 'Choose PDF file or drag here';
            fileDisplay.querySelector('.file-size').textContent = '';
            fileDisplay.classList.remove('has-file');
            filePreview.style.display = 'none';
            fileDisplay.parentElement.style.display = 'block';
        }

        function downloadCSV() {
            // Get the active form (either RSD or IGT)
            const rsdForm = document.getElementById('rsdForm');
            const igtForm = document.getElementById('igtForm');
            const form = rsdForm.style.display !== 'none' ? rsdForm : igtForm;
            
            const formData = new FormData(form);
            let csv = 'Field,Value\n';
            
            for (let [key, value] of formData.entries()) {
                if (key !== 'resume') {
                    csv += `"${key.replace(/_/g, ' ')}","${value}"\n`;
                }
            }
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'application_' + Date.now() + '.csv';
            a.click();
        }
        
        function emailInfo() {
            const recruiterEmail = document.getElementById('recruiter_email').value;
            if (!recruiterEmail) {
                alert('Please enter a recruiter email address first.');
                return;
            }
            alert('Email functionality will send the application details to: ' + recruiterEmail);
            // You can implement actual email sending here
        }

        // Drag and drop functionality
        const fileInput = document.getElementById('resume');
        const fileWrapper = document.querySelector('.file-upload-wrapper');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, () => {
                fileWrapper.classList.add('drag-over');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, () => {
                fileWrapper.classList.remove('drag-over');
            });
        });
        
        fileWrapper.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === 'application/pdf') {
                fileInput.files = files;
                updateFileName(fileInput);
            } else {
                alert('Please upload a PDF file only.');
            }
        });
    </script>

    <!-- Map Modal -->
    <div class="map-modal" id="mapModal">
        <div class="map-modal-content">
            <div class="map-modal-header">
                <h3>Select Location on Map</h3>
                <button class="map-close-btn" onclick="closeMapModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="map-search-box">
                <input type="text" id="mapSearch" placeholder="Search for a location..." oninput="handleMapSearchInput()" onkeypress="handleMapSearch(event)">
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>
            <div id="map"></div>
            <button class="map-confirm-btn" onclick="confirmLocation()">Confirm Location</button>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        let selectedLocation = null;
        let searchTimeout;

        function openMapModal() {
            document.getElementById('mapModal').classList.add('active');
            
            if (!map) {
                // Initialize map centered on Philippines
                map = L.map('map').setView([14.5995, 120.9842], 11);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                // Add click event to map
                map.on('click', function(e) {
                    if (marker) {
                        map.removeLayer(marker);
                    }
                    
                    marker = L.marker(e.latlng).addTo(map);
                    selectedLocation = e.latlng;
                    
                    // Reverse geocode to get address using our proxy
                    fetch(`<?= base_url('api/geocode/reverse') ?>?lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Map click geocode:', data);
                            if (data && data.display_name) {
                                marker.bindPopup(`
                                    <strong>Selected Location</strong><br>
                                    ${data.display_name}
                                `).openPopup();
                            } else {
                                marker.bindPopup(`
                                    <strong>Selected Location</strong><br>
                                    Lat: ${e.latlng.lat.toFixed(6)}<br>
                                    Lng: ${e.latlng.lng.toFixed(6)}
                                `).openPopup();
                            }
                        })
                        .catch(error => {
                            console.error('Map click error:', error);
                            marker.bindPopup(`
                                <strong>Selected Location</strong><br>
                                Lat: ${e.latlng.lat.toFixed(6)}<br>
                                Lng: ${e.latlng.lng.toFixed(6)}
                            `).openPopup();
                        });
                });
            }
            
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }

        function closeMapModal() {
            document.getElementById('mapModal').classList.remove('active');
            document.getElementById('searchSuggestions').classList.remove('active');
            document.getElementById('searchSuggestions').innerHTML = '';
        }

        function handleMapSearchInput() {
            clearTimeout(searchTimeout);
            const searchQuery = document.getElementById('mapSearch').value.trim();
            const suggestionsDiv = document.getElementById('searchSuggestions');

            if (searchQuery.length < 3) {
                suggestionsDiv.classList.remove('active');
                suggestionsDiv.innerHTML = '';
                return;
            }

            suggestionsDiv.innerHTML = '<div class="search-loading">Searching...</div>';
            suggestionsDiv.classList.add('active');

            searchTimeout = setTimeout(() => {
                const url = `<?= base_url('api/geocode/search') ?>?q=${encodeURIComponent(searchQuery + ', Philippines')}&limit=5`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Search results:', data);
                        if (data && data.length > 0) {
                            let html = '';
                            data.forEach(result => {
                                const name = result.display_name.split(',')[0];
                                const fullAddress = result.display_name;
                                html += `
                                    <div class="suggestion-item" data-lat="${result.lat}" data-lon="${result.lon}" data-name="${fullAddress.replace(/"/g, '&quot;')}">
                                        <div class="suggestion-name">${name}</div>
                                        <div class="suggestion-address">${fullAddress}</div>
                                    </div>
                                `;
                            });
                            suggestionsDiv.innerHTML = html;
                            
                            // Add click listeners
                            document.querySelectorAll('.suggestion-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const lat = parseFloat(this.getAttribute('data-lat'));
                                    const lon = parseFloat(this.getAttribute('data-lon'));
                                    const name = this.getAttribute('data-name');
                                    selectSuggestion(lat, lon, name);
                                });
                            });
                        } else {
                            suggestionsDiv.innerHTML = '<div class="search-loading">No results found</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        suggestionsDiv.innerHTML = '<div class="search-loading">Search temporarily unavailable</div>';
                    });
            }, 800);
        }

        function selectSuggestion(lat, lon, displayName) {
            const suggestionsDiv = document.getElementById('searchSuggestions');
            
            map.setView([lat, lon], 16);
            
            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker([lat, lon]).addTo(map);
            selectedLocation = { lat: lat, lng: lon };
            
            marker.bindPopup(`
                <strong>Selected Location</strong><br>
                ${displayName}
            `).openPopup();

            suggestionsDiv.classList.remove('active');
            suggestionsDiv.innerHTML = '';
            document.getElementById('mapSearch').value = displayName;
        }

        function handleMapSearch(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const searchQuery = document.getElementById('mapSearch').value.trim();
                
                if (searchQuery.length > 0) {
                    const url = `<?= base_url('api/geocode/search') ?>?q=${encodeURIComponent(searchQuery + ', Philippines')}`;
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Enter search results:', data);
                            if (data && data.length > 0) {
                                const result = data[0];
                                selectSuggestion(parseFloat(result.lat), parseFloat(result.lon), result.display_name);
                            } else {
                                alert('Location not found. Please try another search.');
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            alert('Error searching location. Please try again or click directly on the map.');
                        });
                }
            }
        }

        function confirmLocation() {
            if (!selectedLocation) {
                alert('Please select a location on the map first.');
                return;
            }

            // Get detailed address information using our proxy
            const url = `<?= base_url('api/geocode/reverse') ?>?lat=${selectedLocation.lat}&lon=${selectedLocation.lng}`;
            console.log('Fetching address from:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Reverse geocode data:', data);
                    
                    if (data && data.address) {
                        const addr = data.address;
                        
                        // Build the most complete street address with ALL details
                        let streetParts = [];
                        if (addr.house_number) streetParts.push(addr.house_number);
                        if (addr.road) streetParts.push(addr.road);
                        if (addr.building) streetParts.push(addr.building);
                        if (addr.commercial) streetParts.push(addr.commercial);
                        if (addr.retail) streetParts.push(addr.retail);
                        if (addr.amenity) streetParts.push(addr.amenity);
                        if (addr.shop) streetParts.push(addr.shop);
                        
                        // Add postal code if available
                        if (addr.postcode) {
                            streetParts.push(`(Zip: ${addr.postcode})`);
                        }
                        
                        const street = streetParts.join(' ') || addr.display_name.split(',')[0] || '';
                        
                        // Get barangay with fallbacks
                        const barangay = addr.suburb || addr.neighbourhood || addr.hamlet || addr.village || addr.quarter || addr.residential || '';
                        
                        // Get municipality with fallbacks
                        const municipality = addr.city || addr.town || addr.municipality || addr.city_district || addr.county || '';
                        
                        // Get province with fallbacks
                        const province = addr.state || addr.province || addr.region || '';
                        
                        console.log('Parsed address:', { 
                            street, 
                            barangay, 
                            municipality, 
                            province,
                            postcode: addr.postcode,
                            country: addr.country,
                            fullAddress: data.display_name 
                        });
                        
                        document.getElementById('street_address').value = street;
                        document.getElementById('barangay').value = barangay;
                        document.getElementById('municipality').value = municipality;
                        document.getElementById('province').value = province;
                        
                        closeMapModal();
                        
                        // Show success message
                        const streetInput = document.getElementById('street_address');
                        streetInput.style.borderColor = '#48bb78';
                        setTimeout(() => {
                            streetInput.style.borderColor = '';
                        }, 2000);
                    } else {
                        console.error('No address data returned');
                        alert('Could not get address details for this location. Please enter the address manually.');
                        closeMapModal();
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error getting address details: ' + error.message + '. Please enter the address manually.');
                    closeMapModal();
                });
        }

        // Close modal when clicking outside
        document.getElementById('mapModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMapModal();
            }
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            const searchBox = document.querySelector('.map-search-box');
            const suggestionsDiv = document.getElementById('searchSuggestions');
            if (searchBox && !searchBox.contains(e.target)) {
                suggestionsDiv.classList.remove('active');
            }
        });
    </script>
</body>
</html>
