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

                        <form action="<?= base_url('admin/application/save') ?>" method="POST" id="applicationForm" enctype="multipart/form-data">
                            <div class="form-group full-width">
                                <label for="company_name">Company Application <span class="required">*</span></label>
                                <select id="company_name" name="company_name" required>
                                    <option value="">Select Company</option>
                                    <option value="RSD">RSD</option>
                                    <option value="IGT">IGT</option>
                                </select>
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
        // Load saved form data on page load
        window.addEventListener('DOMContentLoaded', function() {
            loadFormData();
        });

        // Save form data to localStorage on input
        const formInputs = document.querySelectorAll('#applicationForm input:not([type="file"]), #applicationForm select, #applicationForm textarea');
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

        // Clear localStorage when form is successfully submitted
        const form = document.getElementById('applicationForm');
        form.addEventListener('submit', function() {
            // Check if there's a success message (will be on next page load)
            setTimeout(() => {
                const successAlert = document.querySelector('.alert-success');
                if (successAlert) {
                    localStorage.removeItem('applicationFormData');
                }
            }, 100);
        });

        // Add clear button functionality to also clear localStorage
        const clearButton = form.querySelector('button[type="reset"]');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                localStorage.removeItem('applicationFormData');
            });
        }

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
