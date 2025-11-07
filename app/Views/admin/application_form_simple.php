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
                                <input type="file" id="resume" name="resume" accept=".pdf">
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
        function downloadCSV() {
            const form = document.getElementById('applicationForm');
            const formData = new FormData(form);
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Field,Value\n";
            
            for (let [key, value] of formData.entries()) {
                if (key !== 'resume') {
                    csvContent += `"${key}","${value}"\n`;
                }
            }
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "application_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function emailInfo() {
            const recruiterEmail = document.getElementById('recruiter_email').value;
            if (!recruiterEmail) {
                alert('Please enter a recruiter email address');
                return;
            }
            
            const form = document.getElementById('applicationForm');
            form.submit();
        }
    </script>
</body>
</html>
