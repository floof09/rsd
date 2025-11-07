<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Accounts - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/interviewer-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/users-list.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/sidebar') ?>

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
                    <div class="header-right">
                        <button id="addUserBtn" class="btn-primary" type="button" aria-expanded="false" aria-controls="addUserPanel">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add User
                        </button>
                    </div>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="panel" style="border-left:4px solid #10b981; margin-bottom:16px;">
                        <p style="margin:0; color:#065f46; font-weight:700;">✅ <?= esc($success) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors) && is_array($errors)): ?>
                    <div class="panel" style="border-left:4px solid #ef4444; margin-bottom:16px;">
                        <p style="margin:0 0 8px; color:#991b1b; font-weight:800;">There were some problems creating the account:</p>
                        <ul style="margin:0 0 0 18px; color:#7f1d1d;">
                            <?php foreach ($errors as $field => $msg): ?>
                                <li><?= esc($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div id="addUserPanel" class="panel" style="margin-bottom:20px; display: <?= !empty($errors) || !empty($old) ? 'block' : 'none' ?>;">
                    <h3 style="margin-top:0;">Add User</h3>
                    <form action="<?= base_url('admin/recruiters/create') ?>" method="post" class="add-user-form" novalidate>
                        <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px;">
                            <div>
                                <label for="first_name" style="display:block; font-weight:700; margin-bottom:6px;">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?= esc($old['first_name'] ?? '') ?>" required style="width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:10px;">
                            </div>
                            <div>
                                <label for="last_name" style="display:block; font-weight:700; margin-bottom:6px;">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?= esc($old['last_name'] ?? '') ?>" required style="width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:10px;">
                            </div>
                            <div>
                                <label for="email" style="display:block; font-weight:700; margin-bottom:6px;">Email</label>
                                <input type="email" id="email" name="email" value="<?= esc($old['email'] ?? '') ?>" required style="width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:10px;">
                            </div>
                            <div>
                                <label for="user_type" style="display:block; font-weight:700; margin-bottom:6px;">Role</label>
                                <select id="user_type" name="user_type" required style="width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:10px; background:#fff;">
                                    <?php $sel = $old['user_type'] ?? 'interviewer'; ?>
                                    <option value="interviewer" <?= ($sel === 'interviewer' || $sel === 'recruiter') ? 'selected' : '' ?>>Recruiter</option>
                                    <option value="admin" <?= ($sel === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div>
                                <label for="password" style="display:block; font-weight:700; margin-bottom:6px;">Temporary Password</label>
                                <input type="password" id="password" name="password" minlength="8" required placeholder="Min 8 characters" style="width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:10px;">
                            </div>
                        </div>
                        <div style="display:flex; gap:12px; align-items:center; margin-top:12px;">
                            <button type="submit" class="btn-primary">Create Account</button>
                            <span class="hint" style="border:none; padding:0; background:none; color:var(--muted);">Status will be <strong>Active</strong>.</span>
                        </div>
                    </form>
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
<script>
(function(){
    var btn = document.getElementById('addUserBtn');
    var panel = document.getElementById('addUserPanel');
    if(btn && panel){
        btn.addEventListener('click', function(){
            var isOpen = panel.style.display !== 'none';
            panel.style.display = isOpen ? 'none' : 'block';
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    }
})();
</script>
</body>
</html>
