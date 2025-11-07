(function(){
  if (window.__SIDEBAR_WIRED__) return; // avoid duplicate listeners
  window.__SIDEBAR_WIRED__ = true;
  function restoreState(){
    try {
      var isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      var sidebar = document.querySelector('.sidebar');
      if (sidebar && isCollapsed) sidebar.classList.add('collapsed');
    } catch (e) {}
  }
  function toggle(){
    var sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;
    sidebar.classList.toggle('collapsed');
    try { localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed')); } catch(e) {}
  }
  function init(){
    restoreState();
    var btn = document.getElementById('sidebarToggle');
    if (btn) {
      btn.addEventListener('click', toggle);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
