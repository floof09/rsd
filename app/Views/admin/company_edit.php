<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $company ? 'Edit Company' : 'Add Company' ?> - RSD Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/admin_sidebar') ?>
    <main class="main-content">
        <header class="top-bar">
            <h1><?= $company ? 'Edit Company' : 'Add Company' ?></h1>
            <div class="user-info" style="gap:8px;">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if (session()->getFlashdata('errors')): $errs = session()->getFlashdata('errors'); ?>
                <div class="alert alert-error">
                    <?php foreach ($errs as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="card" style="max-width:640px;">
                <form method="post" action="<?= base_url('admin/companies/save') ?>">
                    <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                    <?php if ($company): ?>
                        <input type="hidden" name="id" value="<?= (int)$company['id'] ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="name">Company Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?= esc(old('name', $company['name'] ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <?php $statusVal = old('status', $company['status'] ?? 'active'); ?>
                        <select id="status" name="status">
                            <option value="active" <?= $statusVal==='active'?'selected':'' ?>>Active</option>
                            <option value="inactive" <?= $statusVal==='inactive'?'selected':'' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="<?= base_url('admin/companies') ?>" class="btn btn-outline">Cancel</a>
                        <?php if ($company): ?>
                            <a href="<?= base_url('admin/companies/' . (int)$company['id'] . '/form') ?>" class="btn btn-secondary">Configure Form</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
