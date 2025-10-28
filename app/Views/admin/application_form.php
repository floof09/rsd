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
</head>
<body>
    <div class="dashboard-container">
        <?= view('components/admin_sidebar') ?>

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
                        </div>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-error">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/application/save') ?>" method="POST" id="applicationForm" enctype="multipart/form-data">
                            <div class="form-group full-width">
                                <label for="company_name">Company Application <span class="required">*</span></label>
                                <input type="text" id="company_name" name="company_name" placeholder="Enter company name" required>
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
                                    <input type="tel" id="phone_number" name="phone_number" placeholder="+63 912 345 6789">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="viber_number">Viber Number</label>
                                    <input type="tel" id="viber_number" name="viber_number" placeholder="+63 912 345 6789">
                                </div>

                                <div class="form-group">
                                    <label for="street_address">Street Address</label>
                                    <input type="text" id="street_address" name="street_address" placeholder="House No., Street Name">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <input type="text" id="barangay" name="barangay" placeholder="Enter barangay">
                                </div>

                                <div class="form-group">
                                    <label for="municipality">Municipality/City</label>
                                    <input type="text" id="municipality" name="municipality" placeholder="Enter municipality">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <input type="text" id="province" name="province" placeholder="Enter province">
                                </div>

                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" id="birthdate" name="birthdate">
                                    <small class="field-hint">Must be at least 18 years old</small>
                                </div>
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
                            </div>

                            <div class="form-group full-width recruiter-box">
                                <label for="recruiter_email">Send Details To <span class="required">*</span></label>
                                <input type="email" id="recruiter_email" name="recruiter_email" placeholder="recruiter@company.com" required>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Entry</button>
                                <button type="button" class="btn btn-secondary" onclick="downloadCSV()">Download CSV</button>
                                <button type="button" class="btn btn-primary" onclick="emailInfo()">Email This Info</button>
                                <button type="reset" class="btn btn-outline">Clear Form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?= view('components/sidebar_script') ?>
    
    <script>
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
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                fileDisplay.classList.add('has-file');
            } else {
                fileName.textContent = 'Choose PDF file or drag here';
                fileSize.textContent = '';
                fileDisplay.classList.remove('has-file');
            }
        }

        function downloadCSV() {
            const form = document.getElementById('applicationForm');
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
</body>
</html>
