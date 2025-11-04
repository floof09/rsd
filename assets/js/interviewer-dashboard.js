(function(){
  // Theme toggle (temporarily disabled: force light theme for all users)
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

  // Force light theme and remove preference
  localStorage.removeItem(key);
  applyTheme('light');
  if (btn) {
    // Hide/disable the control entirely for now
    btn.style.display = 'none';
    btn.disabled = true;
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
