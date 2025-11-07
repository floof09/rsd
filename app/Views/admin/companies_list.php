<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Companies - RSD Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/users-list.css') ?>?v=<?= time() ?>">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/admin_sidebar') ?>
    <main class="main-content">
        <header class="top-bar">
            <h1>Companies</h1>
            <div class="user-info" style="gap:8px;">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <p style="margin:0; color:#64748b;">Manage companies and configure their custom application fields.</p>
                <a class="btn btn-primary" href="<?= base_url('admin/companies/create') ?>">Add Company</a>
            </div>

            <div class="card" style="padding:0; overflow:hidden;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Custom Fields</th>
                            <th style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($companies)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:16px; color:#64748b;">No companies yet.</td></tr>
                        <?php else: foreach ($companies as $c): $schema = json_decode($c['form_schema'] ?? '[]', true); $count = is_array($schema) && !empty($schema['fields']) ? count($schema['fields']) : 0; ?>
                            <tr>
                                <td><?= (int)$c['id'] ?></td>
                                <td><?= esc($c['name']) ?></td>
                                <td><span class="badge <?= $c['status']==='active'?'badge-success':'badge-secondary' ?>"><?= esc(ucfirst($c['status'])) ?></span></td>
                                <td><?= (int)$count ?></td>
                                <td>
                                    <a class="btn btn-small" href="<?= base_url('admin/companies/' . (int)$c['id'] . '/edit') ?>">Edit</a>
                                    <a class="btn btn-small btn-secondary" href="<?= base_url('admin/companies/' . (int)$c['id'] . '/form') ?>">Configure Form</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
