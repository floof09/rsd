<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .charts-grid{ display:grid; grid-template-columns:repeat(12,1fr); gap:16px; }
        .card{ background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,.04), 0 10px 25px rgba(0,0,0,.06); }
        .card h3{ margin:0 0 8px; font-size:16px; }
        .col-6{ grid-column: span 6; }
        .col-12{ grid-column: span 12; }
        @media (max-width: 900px){ .col-6{ grid-column: span 12; } }
        .toolbar{ display:flex; gap:8px; align-items:center; margin: 0 0 12px; }
        .toolbar select, .toolbar button{ padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; cursor:pointer; }
        .toolbar button{ background:linear-gradient(90deg,#f59e0b,#f97316); color:#fff; border:0; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const API_URL = '<?= base_url('admin/reports/data') ?>';
        let currentDays = 30;
        async function fetchData(){
            const url = `${API_URL}?days=${encodeURIComponent(currentDays)}`;
            const res = await fetch(url, { credentials:'same-origin' });
            if(!res.ok){ throw new Error('Failed to load data'); }
            return await res.json();
        }
        function makePalette(n){
            const base = ['#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#f97316','#14b8a6','#8b5cf6','#22c55e','#eab308'];
            const out=[]; for(let i=0;i<n;i++){ out.push(base[i%base.length]); } return out;
        }
        let charts = [];
        function clearCharts(){ charts.forEach(c=>{ try{ c.destroy(); }catch(e){} }); charts = []; }
        function renderCharts(data){
            // Status doughnut
            const statusLabels = Object.keys(data.status || {});
            const statusData = Object.values(data.status || {});
            charts.push(new Chart(document.getElementById('statusChart'), {
                type:'doughnut',
                data:{ labels: statusLabels, datasets:[{ data: statusData, backgroundColor: makePalette(statusLabels.length) }] },
                options:{ plugins:{ legend:{ position:'bottom' } } }
            }));

            // Company over time (stacked line)
            const labels = (data.companyOverTime && data.companyOverTime.labels) || [];
            const series = (data.companyOverTime && data.companyOverTime.series) || [];
            const datasets = series.map((s,idx)=>({
                label: s.label,
                data: s.data,
                borderColor: makePalette(series.length)[idx],
                backgroundColor: 'transparent',
                tension:.3
            }));
            charts.push(new Chart(document.getElementById('companyChart'), {
                type:'line',
                data:{ labels, datasets },
                options:{ scales:{ y:{ beginAtZero:true } } }
            }));

            // Interviewer bar chart
            const iLabels = (data.interviewers && data.interviewers.labels) || [];
            const iData   = (data.interviewers && data.interviewers.data) || [];
            charts.push(new Chart(document.getElementById('interviewerChart'), {
                type:'bar',
                data:{ labels: iLabels, datasets:[{ label:'Applications', data:iData, backgroundColor: makePalette(iLabels.length) }] },
                options:{ indexAxis:'y', scales:{ x:{ beginAtZero:true } } }
            }));
        }
        async function load(){
            try{ clearCharts(); const data = await fetchData(); renderCharts(data); }
            catch(e){ console.error(e); const el=document.getElementById('error'); if(el){ el.textContent = 'Failed to load data.'; } }
        }
        function onRangeChange(sel){ currentDays = parseInt(sel.value,10); load(); }
        function exportCSV(){ const url = '<?= base_url('admin/reports/export') ?>' + `?days=${encodeURIComponent(currentDays)}`; window.location.href = url; }
        document.addEventListener('DOMContentLoaded', load);
    </script>
    </head>
<body>
    <div class="dashboard-container">
    <?= view('components/sidebar') ?>
        <main class="main-content">
            <header class="top-bar">
                <h1>Reports</h1>
                <div class="user-info">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>
            <div class="dashboard-content">
                <div class="toolbar">
                    <label for="rangeSelect">Range:</label>
                    <select id="rangeSelect" onchange="onRangeChange(this)">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                    <button type="button" onclick="exportCSV()">Download CSV</button>
                </div>
                <div id="error" style="color:#ef4444"></div>
                <div class="charts-grid">
                    <div class="card col-6">
                        <h3>Status Breakdown</h3>
                        <canvas id="statusChart" height="220"></canvas>
                    </div>
                    <div class="card col-6">
                        <h3>Applications by Company (Last 30 Days)</h3>
                        <canvas id="companyChart" height="220"></canvas>
                    </div>
                    <div class="card col-12">
                        <h3>Top Interviewers</h3>
                        <canvas id="interviewerChart" height="260"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?= view('components/sidebar_script') ?>
</body>
</html>
