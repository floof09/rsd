<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Accounts - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/interviewer-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/users-list.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/admin_sidebar') ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Active Accounts</h1>
            <div class="user-info" style="gap:8px;">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="users-container">
                <div class="list-header">
                    <div class="header-left">
                        <h2>Active Accounts</h2>
                        <span class="count-badge"><?= is_array($users) ? count($users) : 0 ?> Total</span>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users) && is_array($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="avatar"><?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) ?></div>
                                            <span><?= esc(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></span>
                                        </div>
                                    </td>
                                    <td><?= esc($u['email'] ?? '') ?></td>
                                    <td><span class="badge role-<?= esc($u['user_type'] ?? 'unknown') ?>"><?= esc(ucfirst($u['user_type'] ?? '')) ?></span></td>
                                    <td><?= !empty($u['last_login']) ? date('M d, Y H:i', strtotime($u['last_login'])) : '—' ?></td>
                                    <td><?= !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding:24px; color:var(--muted);">No active accounts</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="<?= base_url('assets/js/interviewer-dashboard.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
