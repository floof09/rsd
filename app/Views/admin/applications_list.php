<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications List - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/interviewer-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/applications-list.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container interviewer-dashboard">
        <?php if (session()->get('user_type') === 'interviewer'): ?>
            <?= view('components/interviewer_sidebar') ?>
        <?php else: ?>
            <?= view('components/admin_sidebar') ?>
        <?php endif; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Applications List</h1>
                <div class="user-info" style="gap:8px;">
                    <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">
                        <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                        <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        Theme
                    </button>
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="applications-container">
                    <div class="list-header">
                        <div class="header-left">
                            <h2><?= session()->get('user_type') === 'interviewer' ? 'My Applications' : 'All Applications' ?></h2>
                            <span class="count-badge"><?= count($applications) ?> Total</span>
                        </div>
                        <div class="header-actions">
                            <button class="btn btn-secondary" onclick="exportToCSV()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Export CSV
                            </button>
                        </div>
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

                    <div class="filter-bar">
                        <div class="search-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input type="text" id="searchInput" placeholder="Search by name, email, or company..." onkeyup="filterTable()">
                        </div>
                        <div class="filter-group">
                            <select id="statusFilter" onchange="filterTable()">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="for_review">For Review</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($applications)): ?>
                        <div class="empty-state">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <h3>No Applications Yet</h3>
                            <p><?= session()->get('user_type') === 'interviewer' ? 'Start by creating a new application' : 'No applications found' ?></p>
                            <?php if (session()->get('user_type') === 'interviewer'): ?>
                                <a href="<?= base_url('interviewer/application') ?>" class="btn btn-primary">Create Application</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="applicationsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Applicant Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Municipality</th>
                                        <th>Status</th>
                                        <th>Date Applied</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td>#<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= esc($app['company_name']) ?></td>
                                            <td>
                                                <div class="applicant-info">
                                                    <div class="avatar"><?= strtoupper(substr($app['first_name'], 0, 1)) ?></div>
                                                    <span><?= esc($app['first_name']) ?> <?= esc($app['last_name']) ?></span>
                                                </div>
                                            </td>
                                            <td><?= esc($app['email_address']) ?></td>
                                            <td><?= esc($app['phone_number'] ?? 'N/A') ?></td>
                                            <td><?= esc($app['municipality'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php $status = trim($app['status'] ?? '') ?: 'pending'; ?>
                                                <span class="status-badge status-<?= esc($status) ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-icon" onclick="viewApplication(<?= $app['id'] ?>)" title="View Details">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    </button>
                                                    <button class="btn-icon" onclick="editApplication(<?= $app['id'] ?>)" title="Edit">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                        </svg>
                                                    </button>
                                                    <button class="btn-icon btn-danger" onclick="deleteApplication(<?= $app['id'] ?>)" title="Delete">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    
    <script src="<?= base_url('assets/js/interviewer-dashboard.js') ?>?v=<?= time() ?>"></script>
    
    <script>
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
        const ROLE_PREFIX = '<?= session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin' ?>';
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const table = document.getElementById('applicationsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                const statusBadge = row.querySelector('.status-badge');
                const status = statusBadge ? statusBadge.textContent.toLowerCase().replace(' ', '_') : '';
                
                const matchesSearch = text.includes(searchInput);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            }
        }

        function exportToCSV() {
            const table = document.getElementById('applicationsTable');
            let csv = [];
            
            // Headers
            const headers = [];
            for (let th of table.querySelectorAll('thead th')) {
                if (th.textContent !== 'Actions') {
                    headers.push(th.textContent);
                }
            }
            csv.push(headers.join(','));
            
            // Rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cols = [];
                    const cells = row.querySelectorAll('td');
                    for (let i = 0; i < cells.length - 1; i++) {
                        cols.push('"' + cells[i].textContent.trim().replace(/"/g, '""') + '"');
                    }
                    csv.push(cols.join(','));
                }
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'applications_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
        }

        function viewApplication(id) {
            window.location.href = BASE_URL + ROLE_PREFIX + '/applications/' + id;
        }

        function editApplication(id) {
            alert('Edit application #' + id + ' - Feature coming soon!');
        }

        function deleteApplication(id) {
            if (confirm('Are you sure you want to delete this application?')) {
                alert('Delete application #' + id + ' - Feature coming soon!');
            }
        }
    </script>
</body>
</html>
