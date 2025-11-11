<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RSD Portal</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
    <?= view('components/sidebar') ?>

        <main class="main-content admin-dashboard-page">
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
                <section class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <div class="kpi-label">Total Users</div>
                            <div class="kpi-value"><?= $total_users ?? 0 ?></div>
                            <div class="kpi-meta">All roles</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <div class="kpi-label">Applications</div>
                            <div class="kpi-value"><?= $total_applications ?? 0 ?></div>
                            <div class="kpi-meta">All time</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <div class="kpi-label">Pending</div>
                            <div class="kpi-value"><?= $pending_applications ?? 0 ?></div>
                            <div class="kpi-meta">Awaiting review</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <div class="kpi-label">Approved</div>
                            <div class="kpi-value"><?= $approved_applications ?? 0 ?></div>
                            <?php $total = max(1, (int)($total_applications ?? 0)); $rate = round((($approved_applications ?? 0) / $total) * 100); ?>
                            <div class="kpi-progress"><div style="width: <?= $rate ?>%"></div></div>
                            <div class="kpi-meta">Approval rate <?= $rate ?>%</div>
                        </div>
                    </div>
                </section>

                <section class="quick-actions card-row">
                    <a class="qa-btn" href="<?= base_url('admin/companies/create') ?>">
                        <span class="icon" aria-hidden="true">🏢</span>
                        <span class="text">Add Company</span>
                    </a>
                    <a class="qa-btn" href="<?= base_url('admin/recruiters') ?>">
                        <span class="icon" aria-hidden="true">👤</span>
                        <span class="text">Manage Users</span>
                    </a>
                    <a class="qa-btn" href="<?= base_url('admin/reports') ?>">
                        <span class="icon" aria-hidden="true">📊</span>
                        <span class="text">View Reports</span>
                    </a>
                </section>

                <section class="content-grid">
                    <div class="content-section">
                        <div class="section-head">
                            <h2>Recent Applications</h2>
                            <a class="link" href="<?= base_url('admin/applications') ?>">View all →</a>
                        </div>
                        <div class="applications-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_applications)): ?>
                                        <?php foreach ($recent_applications as $app): ?>
                                            <tr>
                                                <td><?= esc($app['first_name']) ?> <?= esc($app['last_name']) ?></td>
                                                <td><?= esc($app['company_name']) ?></td>
                                                <td><?= esc($app['email_address']) ?></td>
                                                <td><?= time_ago($app['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" style="text-align:center;color:#64748b">No recent applications</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="section-head">
                            <h2>System Activity</h2>
                            <a class="link" href="<?= base_url('admin/system-logs') ?>">View all →</a>
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
                </section>
            </div>
        </main>
    </div>
    <?= view('components/sidebar_script') ?>
</body>
</html>
