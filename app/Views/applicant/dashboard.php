<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard - RSD Portal</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
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
                <a href="<?= base_url('applicant/dashboard') ?>" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/>
                        <rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Dashboard
                </a>
                <?php if (!isset($application)): ?>
                <a href="<?= base_url('application/form') ?>" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M2 12h20"/>
                    </svg>
                    Apply Now
                </a>
                <?php elseif ($application && $application['status'] === 'approved'): ?>
                <a href="#" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    Learnings
                </a>
                <?php endif; ?>
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
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar applicant-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-info">
                    <?= session()->getFlashdata('info') ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-content">
                <?php if (!isset($application)): ?>
                    <!-- No Application Yet -->
                    <div class="welcome-card">
                        <h2>Welcome to RSD Portal!</h2>
                        <p>You haven't submitted an application yet. Click the button below to get started.</p>
                        <a href="<?= base_url('application/form') ?>" class="btn-apply">Apply Now</a>
                    </div>
                <?php else: ?>
                    <!-- Application Status Card -->
                    <div class="application-status-card">
                        <h2>Your Application Status</h2>
                        <div class="status-details">
                            <div class="status-row">
                                <span class="label">Application ID:</span>
                                <span class="value">#<?= str_pad($application['id'], 4, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="status-row">
                                <span class="label">Submitted On:</span>
                                <span class="value"><?= date('F d, Y', strtotime($application['created_at'])) ?></span>
                            </div>
                            <div class="status-row">
                                <span class="label">Status:</span>
                                <span class="status-badge <?= $application['status'] ?>">
                                    <?= ucfirst($application['status']) ?>
                                </span>
                            </div>
                            <?php if ($application['status'] === 'approved'): ?>
                                <div class="approved-message">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <p>Congratulations! Your application has been approved. You now have access to the Learnings tab.</p>
                                </div>
                            <?php elseif ($application['status'] === 'pending'): ?>
                                <div class="pending-message">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <p>Your application is under review. We'll notify you once it's been processed.</p>
                                </div>
                            <?php elseif ($application['status'] === 'rejected'): ?>
                                <div class="rejected-message">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                    <p>Unfortunately, your application was not approved. <?= $application['review_notes'] ?? 'Please contact admin for more details.' ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fece83;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Application Status</h3>
                            <p class="stat-number"><?= isset($application) ? ucfirst($application['status']) : 'Not Applied' ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #c5c5c5;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Access Level</h3>
                            <p class="stat-number"><?= (isset($application) && $application['status'] === 'approved') ? 'Full' : 'Limited' ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dddddd;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Learnings</h3>
                            <p class="stat-number"><?= (isset($application) && $application['status'] === 'approved') ? 'Unlocked' : 'Locked' ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fece83;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Rejected</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2>My Applications</h2>
                    <div class="applications-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#001</td>
                                    <td>Job Application</td>
                                    <td>Oct 20, 2025</td>
                                    <td><span class="status-badge approved">Approved</span></td>
                                    <td><button class="view-btn">View</button></td>
                                </tr>
                                <tr>
                                    <td>#002</td>
                                    <td>Scholarship Form</td>
                                    <td>Oct 25, 2025</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td><button class="view-btn">View</button></td>
                                </tr>
                                <tr>
                                    <td>#003</td>
                                    <td>Internship Request</td>
                                    <td>Oct 28, 2025</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td><button class="view-btn">View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
