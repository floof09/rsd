(function(){
  // Theme toggle
  const btn = document.getElementById('themeBtn');
  const sun = document.getElementById('sunIcon');
  const moon = document.getElementById('moonIcon');
  const key = 'rsd-theme';

  function applyTheme(theme){
    const dark = theme === 'dark';
    document.body.classList.toggle('theme-dark', dark);
    if (btn) btn.setAttribute('aria-pressed', String(dark));
    if (sun) sun.style.display = dark ? 'none' : '';
    if (moon) moon.style.display = dark ? '' : 'none';
  }

  const preferred = localStorage.getItem(key) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  applyTheme(preferred);

  if (btn) {
    btn.addEventListener('click', ()=>{
      const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';
      localStorage.setItem(key, next);
      applyTheme(next);
    });
  }

  // Sidebar toggle (no inline handlers)
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.getElementById('sidebarToggle');
  if (sidebar) {
    // Restore persisted state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) sidebar.classList.add('collapsed');
  }
  if (toggle && sidebar) {
    toggle.addEventListener('click', ()=>{
      sidebar.classList.toggle('collapsed');
      const collapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem('sidebarCollapsed', String(collapsed));
      toggle.setAttribute('aria-pressed', String(collapsed));
    });
  }
})();
