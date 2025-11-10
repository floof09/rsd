<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configure Form - <?= esc($company['name']) ?> - RSD Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=<?= time() ?>">
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
            <h1>Customize Additional Applicant Fields – <?= esc($company['name']) ?></h1>
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

            <div class="card" style="max-width:1000px;">
                <p style="margin-top:0; color:#64748b; font-size:14px; line-height:1.5;">
                    Add extra questions you want interviewers to fill out for this company. These appear <strong>after</strong> the basic applicant details (name, contact, etc.). Keep labels short and clear. If unsure about an option, hover the help icon.
                </p>
                <ul style="margin:0 0 16px 18px; padding:0; font-size:13px; color:#475569;">
                    <li><strong>Label</strong>: What the interviewer sees (e.g. "Program Applied For")</li>
                    <li><strong>Type</strong>: How they answer (Text, Number, Date, Yes/No checkbox, Dropdown list, Long Answer)</li>
                    <li><strong>Required</strong>: Must be filled before saving</li>
                    <li><strong>Options</strong>: For a dropdown – write choices separated by commas (e.g. Basic,Premium,VIP)</li>
                </ul>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:12px 14px; border-radius:8px; margin-bottom:16px; font-size:12px; color:#475569; display:flex; gap:14px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span style="font-weight:600;">Need help?</span>
                        <span>Hover any <span style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-size:11px;">?</span> icon.</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span style="background:#eef2ff; color:#4338ca; padding:2px 6px; border-radius:4px; font-size:11px;">Advanced</span>
                        <span>You can expand an advanced panel for patterns & limits.</span>
                    </div>
                </div>
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
            <button type="button" class="remove-btn" onclick="removeField(this)">Remove</button>
            <h4 style="display:flex; align-items:center; gap:6px;">Field <span style="background:#e2e8f0; color:#475569; width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; font-size:11px; cursor:help;" title="This is one extra question shown on the application form.">?</span></h4>
            <div class="field-row">
                <div>
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Technical Key * <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:help;" title="Auto-filled. Lowercase letters, numbers, underscores. Used internally; users won't see this.">?</span></label>
                    <input oninput="autoSlug(this)" data-autokey name="fields[${idx}][key]" value="${escapeAttr(prefill.key||'')}" required placeholder="auto_generated" />
                </div>
                <div>
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Question Label * <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:help;" title="What interviewers will read.">?</span></label>
                    <input type="text" name="fields[${idx}][label]" value="${escapeAttr(prefill.label||'')}" required placeholder="e.g. Program Applied For" oninput="syncKeyIfBlank(this)" />
                </div>
                <div>
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Answer Type <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:help;" title="Choose how the user answers.">?</span></label>
                    <select name="fields[${idx}][type]" onchange="onTypeChange(this)">
                        ${selectOptions(prefill.type)}
                    </select>
                </div>
                <div class="small">
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Required? <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:help;" title="Make interviewer fill this before saving.">?</span></label>
                    <select name="fields[${idx}][required]">
                        <option value="">No</option>
                        <option value="1" ${prefill.required? 'selected':''}>Yes</option>
                    </select>
                </div>
            </div>
            <div class="field-row">
                <div>
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Dropdown Choices <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:help;" title="Only for 'select' type. Separate choices with commas.">?</span></label>
                    <input type="text" name="fields[${idx}][options]" value="${escapeAttr((prefill.options||[]).join(','))}" placeholder="Basic,Premium,VIP" data-role="options" />
                </div>
                <div>
                    <label style="font-size:11px;">Max Length</label>
                    <input type="number" name="fields[${idx}][maxLength]" value="${escapeAttr(prefill.maxLength||'')}" min="1" placeholder="e.g. 50" />
                </div>
                <div>
                    <label style="font-size:11px; display:flex; align-items:center; gap:4px;">Advanced <span style="background:#e2e8f0; padding:0 6px; border-radius:12px; font-size:10px; cursor:pointer;" onclick="toggleAdvanced(this)" title="Show pattern and number range inputs.">+</span></label>
                    <div class="advanced" style="display:none; gap:6px;">
                        <div style="margin-bottom:6px;">
                            <label style="font-size:10px;">Pattern (optional)</label>
                            <input style="font-size:11px;" type="text" name="fields[${idx}][pattern]" value="${escapeAttr(prefill.pattern||'')}" placeholder="^A.*$" />
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:6px;">
                            <div>
                                <label style="font-size:10px;">Min (number)</label>
                                <input style="font-size:11px;" type="number" step="any" name="fields[${idx}][min]" value="${escapeAttr(prefill.min||'')}" data-role="min" />
                            </div>
                            <div>
                                <label style="font-size:10px;">Max (number)</label>
                                <input style="font-size:11px;" type="number" step="any" name="fields[${idx}][max]" value="${escapeAttr(prefill.max||'')}" data-role="max" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(wrap);
        enhanceField(wrap);
    }

    function selectOptions(current) {
        const types = [
            ['text','Short Answer'],
            ['textarea','Long Answer'],
            ['number','Number'],
            ['date','Date'],
            ['email','Email'],
            ['tel','Phone'],
            ['select','Dropdown'],
            ['checkbox','Yes / No'],
        ];
        return types.map(([val,label]) => `<option value="${val}" ${val===current?'selected':''}>${label}</option>`).join('');
    }
    function escapeAttr(s){ return String(s).replace(/"/g,'&quot;'); }

    existing.forEach(f => addField(f));
    if (!existing.length) addField();

    function toggleAdvanced(el){
        const box = el.closest('div').querySelector('.advanced');
        if(!box) return; const open = box.style.display==='none';
        box.style.display = open ? 'block' : 'none';
        el.textContent = open ? '–' : '+';
    }

    function slugify(str){
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s]/g,'')
            .replace(/\s+/g,'_')
            .replace(/_+/g,'_')
            .replace(/^_|_$/g,'');
    }
    function syncKeyIfBlank(labelInput){
        const wrap = labelInput.closest('.schema-field');
        const keyInput = wrap.querySelector('input[data-autokey]');
        if(!keyInput) return; if(keyInput.value.trim()===''){ keyInput.value = slugify(labelInput.value); }
    }
    function autoSlug(keyInput){
        keyInput.value = keyInput.value.toLowerCase().replace(/[^a-z0-9_]/g,'').replace(/__+/g,'_');
    }

    function removeField(btn){
        const row = btn.closest('.schema-field');
        if(row) row.remove();
        reindexFields();
    }

    function enhanceField(wrap){
        // Disable options unless type is select; disable min/max unless number
        const typeSel = wrap.querySelector('select[name*="[type]"]');
        onTypeChange(typeSel);
        // Add move up/down controls
        if(!wrap.querySelector('.move-controls')){
            const mc = document.createElement('div');
            mc.className = 'move-controls';
            mc.style.cssText = 'position:absolute; right:70px; top:8px; display:flex; gap:6px;';
            mc.innerHTML = `<button type="button" title="Move up" style="background:#e2e8f0;border:none;border-radius:4px;padding:2px 6px;cursor:pointer;">▲</button>
                            <button type="button" title="Move down" style="background:#e2e8f0;border:none;border-radius:4px;padding:2px 6px;cursor:pointer;">▼</button>`;
            const [up,down] = mc.querySelectorAll('button');
            up.addEventListener('click',()=>{ const prev = wrap.previousElementSibling; if(prev){ container.insertBefore(wrap, prev); reindexFields(); }});
            down.addEventListener('click',()=>{ const next = wrap.nextElementSibling; if(next){ container.insertBefore(next, wrap); reindexFields(); }});
            wrap.appendChild(mc);
        }
    }

    function onTypeChange(sel){
        if(!sel) return; const wrap = sel.closest('.schema-field');
        const isSelect = sel.value === 'select';
        const isNumber = sel.value === 'number';
        const opt = wrap.querySelector('[data-role="options"]');
        const min = wrap.querySelector('[data-role="min"]');
        const max = wrap.querySelector('[data-role="max"]');
        if(opt){ opt.disabled = !isSelect; opt.placeholder = isSelect ? 'Choice A,Choice B' : '— only for Dropdown —'; if(!isSelect) opt.value = opt.value; }
        if(min){ min.disabled = !isNumber; if(!isNumber) min.value = min.value; }
        if(max){ max.disabled = !isNumber; if(!isNumber) max.value = max.value; }
    }

    function reindexFields(){
        const rows = Array.from(container.querySelectorAll('.schema-field'));
        rows.forEach((row,i)=>{
            row.querySelectorAll('[name^="fields["]').forEach(input=>{
                input.name = input.name.replace(/fields\[[0-9]+\]/, `fields[${i}]`);
            });
        });
    }
</script>
<?= view('components/sidebar_script') ?>
</body>
</html>
