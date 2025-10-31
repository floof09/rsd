<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/system-logs.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?= view('components/admin_sidebar') ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>System Logs</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="logs-header">
                    <div class="logs-stats">
                        <div class="stat-box">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <div>
                                <span class="stat-label">Total Logs</span>
                                <span class="stat-value"><?= number_format($total_logs ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="logs-actions">
                        <div class="filter-group">
                            <select id="moduleFilter" onchange="filterByModule(this.value)">
                                <option value="">All Modules</option>
                                <option value="auth" <?= isset($filter_module) && $filter_module === 'auth' ? 'selected' : '' ?>>Authentication</option>
                                <option value="application" <?= isset($filter_module) && $filter_module === 'application' ? 'selected' : '' ?>>Applications</option>
                                <option value="user" <?= isset($filter_module) && $filter_module === 'user' ? 'selected' : '' ?>>Users</option>
                                <option value="system" <?= isset($filter_module) && $filter_module === 'system' ? 'selected' : '' ?>>System</option>
                            </select>
                        </div>
                        <button class="btn btn-secondary" onclick="window.location.href='<?= base_url('admin/system-logs/clear-old') ?>'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                            Clear Old Logs
                        </button>
                        <button class="btn btn-primary" onclick="exportLogs()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>

                <div class="logs-container">
                    <div class="logs-table-wrapper">
                        <table class="logs-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="log-entry">
                                            <td class="log-time">
                                                <span class="time"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                                                <span class="date"><?= date('M d, Y', strtotime($log['created_at'])) ?></span>
                                            </td>
                                            <td class="log-user">
                                                <?php if ($log['user_id']): ?>
                                                    <div class="user-info-cell">
                                                        <div class="user-avatar-small"><?= strtoupper(substr($log['first_name'] ?? 'U', 0, 1)) ?></div>
                                                        <div>
                                                            <div class="user-name"><?= esc($log['first_name'] ?? '') ?> <?= esc($log['last_name'] ?? '') ?></div>
                                                            <div class="user-email"><?= esc($log['email'] ?? '') ?></div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">System</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="module-badge module-<?= strtolower($log['module']) ?>">
                                                    <?= ucfirst($log['module']) ?>
                                                </span>
                                            </td>
                                            <td class="log-action"><?= esc($log['action']) ?></td>
                                            <td class="log-description"><?= esc($log['description'] ?? '-') ?></td>
                                            <td class="log-ip"><?= esc($log['ip_address'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="empty-state">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                <h3>No Logs Found</h3>
                                                <p>System activity logs will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?= view('components/sidebar_script') ?>
    
    <script>
        function filterByModule(module) {
            if (module) {
                window.location.href = '<?= base_url('admin/system-logs/filter') ?>/' + module;
            } else {
                window.location.href = '<?= base_url('admin/system-logs') ?>';
            }
        }

        function exportLogs() {
            const table = document.querySelector('.logs-table');
            let csv = [];
            
            // Get headers
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
            csv.push(headers.join(','));
            
            // Get data
            const rows = table.querySelectorAll('tbody tr:not(.empty-state)');
            rows.forEach(row => {
                const cols = Array.from(row.querySelectorAll('td')).map(td => {
                    return '"' + td.textContent.trim().replace(/"/g, '""') + '"';
                });
                csv.push(cols.join(','));
            });
            
            // Download
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'system_logs_' + Date.now() + '.csv';
            a.click();
        }
    </script>
</body>
</html>
