<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Company Questions — <?= esc($company['name'] ?? 'Company') ?> | Application #<?= (int)$application['id'] ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=<?= time() ?>">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/sidebar') ?>
    <main class="main-content">
        <header class="top-bar">
            <h1>Company Questions — <?= esc($company['name'] ?? 'Company') ?></h1>
            <div class="user-info" style="gap:8px;">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>
        <div class="dashboard-content">
            <div class="progress-steps" style="margin-bottom:12px;">
                <span class="step done">Step 1 of 2: Basic Application</span>
                <span class="step current">Step 2 of 2: Company Questions</span>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="card" style="max-width:1000px;">
                <?php $fields = $schema['fields'] ?? []; ?>
                <?php if (empty($fields)): ?>
                    <p style="margin:0 0 12px; color:#475569;">There are no company-specific questions for <strong><?= esc($company['name']) ?></strong>. You can proceed.</p>
                    <div class="form-actions">
                        <a class="btn btn-primary" href="<?= base_url('interviewer/applications/' . (int)$application['id']) ?>">Back to Application</a>
                    </div>
                <?php else: ?>
                <form method="post" action="<?= base_url('interviewer/applications/' . (int)$application['id'] . '/company-fields/save') ?>">
                    <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                    <div class="form-row">
                        <?php foreach ($fields as $f): $key = (string)($f['key'] ?? ''); if ($key==='') continue; $label = (string)($f['label'] ?? $key); $type = (string)($f['type'] ?? 'text'); $required = !empty($f['required']);
                            $val = $values[$key] ?? '';
                        ?>
                        <div class="form-group full-width">
                            <label for="cf_<?= esc($key) ?>">
                                <?= esc($label) ?><?= $required ? ' <span class="required">*</span>' : '' ?>
                            </label>
                            <?php if ($type === 'textarea'): ?>
                                <textarea id="cf_<?= esc($key) ?>" name="custom[<?= esc($key) ?>]" rows="3" <?= $required ? 'required' : '' ?>><?= esc($val) ?></textarea>
                            <?php elseif ($type === 'select'): $opts = (array)($f['options'] ?? []); ?>
                                <select id="cf_<?= esc($key) ?>" name="custom[<?= esc($key) ?>]" <?= $required ? 'required' : '' ?>>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($opts as $o): $sel = ((string)$val === (string)$o) ? 'selected' : ''; ?>
                                        <option value="<?= esc($o) ?>" <?= $sel ?>><?= esc($o) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($type === 'checkbox'): ?>
                                <input type="checkbox" id="cf_<?= esc($key) ?>" name="custom[<?= esc($key) ?>]" value="1" <?= !empty($val) ? 'checked' : '' ?> />
                            <?php else: ?>
                                <input type="<?= in_array($type,['email','tel','number','date','text']) ? $type : 'text' ?>" id="cf_<?= esc($key) ?>" name="custom[<?= esc($key) ?>]" value="<?= esc($val) ?>" <?= $required ? 'required' : '' ?> />
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Company Form</button>
                        <a href="<?= base_url('interviewer/applications/' . (int)$application['id']) ?>" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?= view('components/sidebar_script') ?>
</body>
</html>
