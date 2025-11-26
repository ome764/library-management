// Phase 1 - minimal JS: client-side search and simple borrow validation
document.addEventListener('DOMContentLoaded',()=>{
  // Helper fetch wrappers
  async function fetchJSON(url, opts){
    const res = await fetch(url, opts);
    return res.json();
  }

  // Debounce helper
  function debounce(fn,ms=250){
    let t;
    return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn(...args), ms); };
  }

  // Escape HTML
  function escapeHtml(s){return (s||'').replace(/[&<>\"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));}

  // Update auth area in header
  async function refreshAuth(){
    try{
      const json = await fetchJSON('php/session.php');
      const topnav = document.querySelector('.topnav');
      if(!topnav) return;
      const existing = document.querySelector('.auth-area');
      if(existing) existing.remove();
      const div = document.createElement('div');
      div.className = 'auth-area';
      if(json.status === 'ok' && json.user){
        div.innerHTML = `<span style="margin-right:10px">Hi, ${escapeHtml(json.user.name)}</span><a class="btn ghost" href="php/logout.php">Logout</a>`;
      } else {
        div.innerHTML = `<a class="btn ghost" href="login.html">Login</a> <a class="btn" href="register.html">Register</a>`;
      }
      topnav.parentNode.appendChild(div);
    }catch(e){
      // ignore silently
    }
  }
  refreshAuth();

  // Expose small API for other inline scripts if needed
  window.LIB = {
    fetchJSON, debounce, escapeHtml
  };

  // Toast / snackbar helper
  function showToast(message, type='info', timeout=6000){
    let container = document.querySelector('.toast-container');
    if(!container){
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const t = document.createElement('div');
    t.className = 'toast ' + (type||'info');
    t.innerHTML = `<div>${escapeHtml(message)}</div>`;
    container.appendChild(t);
    // auto remove
    setTimeout(()=>{
      t.style.transition = 'opacity .28s, transform .28s';
      t.style.opacity = '0';
      t.style.transform = 'translateY(-6px)';
      setTimeout(()=>t.remove(), 300);
    }, timeout);
    return t;
  }

  // SSE/live-updates removed by user request (no periodic "new book" notifications)
});
// هضيف دالة للتأثيرات الخاصة
function initDashboardEffects() {
    // تأثيرات الكروت عند التمرير
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    // مراقبة كل الكروت
    document.querySelectorAll('.stat-card, .section').forEach(card => {
        card.style.animationPlayState = 'paused';
        observer.observe(card);
    });

    // تأثير التحديث
    const refreshBtn = document.createElement('button');
    refreshBtn.innerHTML = '🔄';
    refreshBtn.className = 'btn-floating';
    refreshBtn.title = 'تحديث البيانات';
    refreshBtn.onclick = () => {
        refreshBtn.style.transform = 'rotate(180deg)';
        setTimeout(() => {
            fetchDashboardData();
            refreshBtn.style.transform = 'rotate(0deg)';
        }, 500);
    };
    document.body.appendChild(refreshBtn);
}

// دالة محسنة لعرض الإحصائيات
function displayStats(stats) {
    const statsGrid = document.getElementById('statsGrid');
    statsGrid.innerHTML = `
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-number">${formatNumber(stats.total_books)}</div>
            <div class="stat-label">إجمالي الكتب</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number">${formatNumber(stats.total_users)}</div>
            <div class="stat-label">المستخدمين المسجلين</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📖</div>
            <div class="stat-number">${formatNumber(stats.borrowed_books)}</div>
            <div class="stat-label">الكتب المستعارة</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⏰</div>
            <div class="stat-number">${formatNumber(stats.reservations)}</div>
            <div class="stat-label">الحجوزات النشطة</div>
        </div>
    `;
}

// في نهاية الـ DOMContentLoaded هضيف:
document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();
    startAutoRefresh();
    initDashboardEffects(); // تشغيل التأثيرات
});
