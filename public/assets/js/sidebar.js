(function(){
  if(window.__APP_SIDEBAR__) return;window.__APP_SIDEBAR__=true;
  var STORAGE_KEY='appSidebarCollapsed';
  function el(){return document.querySelector('.app-sidebar');}
  function restore(){
    try{var collapsed=localStorage.getItem(STORAGE_KEY)==='true';var s=el();if(s&&collapsed){s.classList.add('collapsed');var t=document.getElementById('sidebarToggle');if(t) t.setAttribute('aria-expanded','false');}}catch(e){}
  }
  function toggle(){var s=el();if(!s) return;var willCollapse=!s.classList.contains('collapsed');s.classList.toggle('collapsed');var t=document.getElementById('sidebarToggle');if(t) t.setAttribute('aria-expanded', String(!willCollapse));try{localStorage.setItem(STORAGE_KEY, String(willCollapse));}catch(e){}
  }
  function init(){restore();var btn=document.getElementById('sidebarToggle');if(btn){btn.addEventListener('click',toggle);} }
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init);} else {init();}
})();