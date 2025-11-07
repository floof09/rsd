<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configure Form - <?= esc($company['name']) ?> - RSD Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <style>
        .field-row { display:grid; grid-template-columns: repeat(auto-fit,minmax(140px,1fr)); gap:8px; margin-bottom:8px; }
        .field-row .small { max-width:160px; }
        .schema-field { border:1px solid #e5e7eb; background:#fff; padding:12px; border-radius:8px; margin-bottom:10px; position:relative; }
        .schema-field h4 { margin:0 0 8px; font-size:14px; font-weight:600; color:#334155; }
        .remove-btn { position:absolute; top:8px; right:8px; background:#fee2e2; color:#b91c1c; border:none; padding:4px 8px; font-size:11px; border-radius:4px; cursor:pointer; }
        .remove-btn:hover { background:#fca5a5; }
        .drag-handle { cursor:move; position:absolute; left:8px; top:8px; font-size:12px; color:#64748b; }
        .schema-field input[type=text], .schema-field input[type=number], .schema-field select, .schema-field textarea { width:100%; padding:6px 8px; border:1px solid #d1d5db; border-radius:6px; font-size:12px; }
        .schema-field textarea { resize:vertical; }
        .pill { display:inline-block; padding:2px 6px; background:#f1f5f9; border-radius:4px; font-size:11px; margin-right:4px; }
        .badge-info { background:#eef2ff; color:#4338ca; }
    </style>
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/sidebar') ?>
    <main class="main-content">
        <header class="top-bar">
            <h1>Configure Form: <?= esc($company['name']) ?></h1>
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

            <div class="card" style="max-width:900px;">
                <p style="margin-top:0; color:#64748b;">Add or modify custom fields. These will appear after the standard applicant fields in the interviewer application form. Validation is enforced server-side.</p>
                <form action="<?= base_url('admin/companies/' . (int)$company['id'] . '/form/save') ?>" method="post" id="schemaForm">
                    <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                    <div id="fieldsContainer"></div>
                    <button type="button" class="btn btn-secondary" onclick="addField()">Add Field</button>
                    <div style="margin-top:16px; display:flex; gap:12px;">
                        <button type="submit" class="btn btn-primary">Save Schema</button>
                        <a class="btn btn-outline" href="<?= base_url('admin/companies') ?>">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    const existing = <?= json_encode($schema['fields'] ?? []) ?>;
    const container = document.getElementById('fieldsContainer');

    function addField(prefill = {}) {
        const idx = container.querySelectorAll('.schema-field').length;
        const wrap = document.createElement('div');
        wrap.className = 'schema-field';
        wrap.innerHTML = `
            <span class="drag-handle" title="(Static ordering)">⋮⋮</span>
            <button type="button" class="remove-btn" onclick="this.closest('.schema-field').remove()">Remove</button>
            <h4>Field</h4>
            <div class="field-row">
                <div>
                    <label style="font-size:11px;">Key *</label>
                    <input type="text" name="fields[${idx}][key]" value="${escapeAttr(prefill.key||'')}" required placeholder="internal_key" />
                </div>
                <div>
                    <label style="font-size:11px;">Label *</label>
                    <input type="text" name="fields[${idx}][label]" value="${escapeAttr(prefill.label||'')}" required placeholder="Field label" />
                </div>
                <div>
                    <label style="font-size:11px;">Type</label>
                    <select name="fields[${idx}][type]">
                        ${selectOptions(prefill.type)}
                    </select>
                </div>
                <div class="small">
                    <label style="font-size:11px;">Required</label>
                    <select name="fields[${idx}][required]">
                        <option value="">No</option>
                        <option value="1" ${prefill.required? 'selected':''}>Yes</option>
                    </select>
                </div>
            </div>
            <div class="field-row">
                <div>
                    <label style="font-size:11px;">Options (comma separated)</label>
                    <input type="text" name="fields[${idx}][options]" value="${escapeAttr((prefill.options||[]).join(','))}" placeholder="Option1,Option2" />
                </div>
                <div>
                    <label style="font-size:11px;">Pattern (regex)</label>
                    <input type="text" name="fields[${idx}][pattern]" value="${escapeAttr(prefill.pattern||'')}" placeholder="^\\d+$" />
                </div>
                <div>
                    <label style="font-size:11px;">Max Length</label>
                    <input type="number" name="fields[${idx}][maxLength]" value="${escapeAttr(prefill.maxLength||'')}" min="1" />
                </div>
            </div>
            <div class="field-row">
                <div>
                    <label style="font-size:11px;">Min (number)</label>
                    <input type="number" step="any" name="fields[${idx}][min]" value="${escapeAttr(prefill.min||'')}" />
                </div>
                <div>
                    <label style="font-size:11px;">Max (number)</label>
                    <input type="number" step="any" name="fields[${idx}][max]" value="${escapeAttr(prefill.max||'')}" />
                </div>
            </div>
        `;
        container.appendChild(wrap);
    }

    function selectOptions(current) {
        const types = ['text','email','tel','select','number','date','textarea','checkbox'];
        return types.map(t => `<option value="${t}" ${t===current?'selected':''}>${t}</option>`).join('');
    }
    function escapeAttr(s){ return String(s).replace(/"/g,'&quot;'); }

    existing.forEach(f => addField(f));
    if (!existing.length) addField();
</script>
<?= view('components/sidebar_script') ?>
</body>
</html>
