<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - RSD Portal</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar applicant-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <svg width="40" height="40" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="95" fill="rgba(255,255,255,0.3)"/>
                        <circle cx="100" cy="85" r="75" fill="rgba(255,255,255,0.4)"/>
                        <circle cx="85" cy="100" r="70" fill="rgba(255,255,255,0.4)"/>
                        <circle cx="115" cy="100" r="70" fill="rgba(255,255,255,0.4)"/>
                        <circle cx="100" cy="115" r="75" fill="rgba(255,255,255,0.4)"/>
                        <rect x="70" y="70" width="60" height="60" rx="8" fill="white"/>
                        <rect x="75" y="75" width="50" height="50" rx="6" fill="#fece83" opacity="0.5"/>
                    </svg>
                </div>
                <h2>RSD <span>Portal</span></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= base_url('applicant/dashboard') ?>" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/>
                        <rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Dashboard
                </a>
                <a href="<?= base_url('application/form') ?>" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M2 12h20"/>
                    </svg>
                    Apply Now
                </a>
                <a href="#" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profile
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="<?= base_url('auth/logout') ?>" class="logout-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Application Form</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar applicant-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="form-container-inner">
        <div class="form-header">
            <div class="logo">
                <svg width="50" height="50" viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="95" fill="#c5c5c5" opacity="0.3"/>
                    <circle cx="100" cy="85" r="75" fill="#b5b5b5" opacity="0.4"/>
                    <circle cx="85" cy="100" r="70" fill="#a5a5a5" opacity="0.4"/>
                    <circle cx="115" cy="100" r="70" fill="#a5a5a5" opacity="0.4"/>
                    <circle cx="100" cy="115" r="75" fill="#b5b5b5" opacity="0.4"/>
                    <rect x="70" y="70" width="60" height="60" rx="8" fill="#fece83"/>
                    <rect x="75" y="75" width="50" height="50" rx="6" fill="#f4a261" opacity="0.3"/>
                </svg>
            </div>
            <h1>Company Application Form</h1>
            <p>Fill in the applicant details below</p>
        </div>

        <div class="form-content">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('application/submit') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" value="<?= old('first_name') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" value="<?= old('last_name') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_number">Contact Number <span class="required">*</span></label>
                        <input type="tel" id="contact_number" name="contact_number" placeholder="+63 912 345 6789" value="<?= old('contact_number') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email_address">Email Address <span class="required">*</span></label>
                        <input type="email" id="email_address" name="email_address" placeholder="applicant@email.com" value="<?= old('email_address', session()->get('email')) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="home_address">Home Address</label>
                        <textarea id="home_address" name="home_address" rows="3" placeholder="Enter complete address"><?= old('home_address') ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="municipality">Municipality <span class="required">*</span></label>
                        <input type="text" id="municipality" name="municipality" placeholder="Enter municipality" value="<?= old('municipality') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="educational_attainment">Educational Attainment <span class="required">*</span></label>
                        <select id="educational_attainment" name="educational_attainment" required>
                            <option value="">Select educational attainment</option>
                            <option value="High School Graduate" <?= old('educational_attainment') == 'High School Graduate' ? 'selected' : '' ?>>High School Graduate</option>
                            <option value="Some College" <?= old('educational_attainment') == 'Some College' ? 'selected' : '' ?>>Some College</option>
                            <option value="College Graduate" <?= old('educational_attainment') == 'College Graduate' ? 'selected' : '' ?>>College Graduate</option>
                            <option value="Vocational Graduate" <?= old('educational_attainment') == 'Vocational Graduate' ? 'selected' : '' ?>>Vocational Graduate</option>
                            <option value="Master's Degree" <?= old('educational_attainment') == "Master's Degree" ? 'selected' : '' ?>>Master's Degree</option>
                            <option value="Doctorate" <?= old('educational_attainment') == 'Doctorate' ? 'selected' : '' ?>>Doctorate</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="bpo_experience">BPO Experience <span class="required">*</span></label>
                        <input type="text" id="bpo_experience" name="bpo_experience" placeholder="e.g., 2 years 6 months" value="<?= old('bpo_experience') ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('applicant/dashboard') ?>" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
