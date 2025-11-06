<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RSD Portal</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?= view('components/admin_sidebar') ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fece83;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p class="stat-number"><?= $total_users ?? 0 ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #c5c5c5;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Applications</h3>
                            <p class="stat-number"><?= $total_applications ?? 0 ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dddddd;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Pending</h3>
                            <p class="stat-number"><?= $pending_applications ?? 0 ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fece83;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Approved</h3>
                            <p class="stat-number"><?= $approved_applications ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2>Recent Activity</h2>
                    <div class="activity-list">
                        <?php if (!empty($recent_applications)): ?>
                            <?php foreach ($recent_applications as $app): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">📝</div>
                                    <div class="activity-details">
                                        <h4>New application from <?= esc($app['company_name']) ?></h4>
                                        <p><?= esc($app['first_name']) ?> <?= esc($app['last_name']) ?> - <?= esc($app['email_address']) ?></p>
                                        <span class="activity-time"><?= time_ago($app['created_at']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-icon">📋</div>
                                <div class="activity-details">
                                    <h4>No recent activity</h4>
                                    <p>No applications have been submitted yet</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="content-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2>System Activity</h2>
                        <a href="<?= base_url('admin/system-logs') ?>" style="color: #fece83; text-decoration: none; font-size: 14px;">View All →</a>
                    </div>
                    <div class="activity-list">
                        <?php if (!empty($recent_logs)): ?>
                            <?php foreach ($recent_logs as $log): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <?php
                                        $icons = [
                                            'auth' => '🔐',
                                            'application' => '📝',
                                            'user' => '👤',
                                            'system' => '⚙️'
                                        ];
                                        echo $icons[$log['module']] ?? '📋';
                                        ?>
                                    </div>
                                    <div class="activity-details">
                                        <h4><?= esc($log['action']) ?></h4>
                                        <p>
                                            <?php if (!empty($log['email'])): ?>
                                                <strong><?= esc($log['email']) ?></strong> - 
                                            <?php endif; ?>
                                            <?= esc($log['description']) ?>
                                        </p>
                                        <span class="activity-time"><?= time_ago($log['created_at']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-icon">📋</div>
                                <div class="activity-details">
                                    <h4>No system activity yet</h4>
                                    <p>System logs will appear here once actions are performed</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?= view('components/sidebar_script') ?>
</body>
</html>
