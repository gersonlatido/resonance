<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu Management</title>

  <style>
    :root{
      --panel: #ffffff;
      --sidebar: #a9a9a9;
      --text: #222;
      --muted: #6b7280;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --card: #fff;
      --border: #e5e7eb;
      --shadow-soft: 0 6px 14px rgba(0,0,0,.10);
      --radius: 14px;
      --danger: #ef4444;
      --soft: #f5f5f5;
    }

    * { box-sizing: border-box; }

    body{
      margin:0;
      font-family: 'Figtree', sans-serif;
      background: #ffffff;
      color: var(--text);
    }

    .shell{
      width: 100%;
      min-height: 100vh;
      margin: 0;
      background: var(--panel);
      box-shadow: none;
      overflow: hidden;
      display: grid;
      grid-template-columns: 230px 1fr;
    }

    /* ===== Sidebar ===== */
    .sidebar{
      background: #e4e3e3;
      padding: 18px 14px;
      position: relative;
    }

    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      padding: 6px 6px 14px 6px;
    }

    .logo-box{
      width: 110px;
      height: 54px;
      background: #fff;
      border-radius: 8px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 6px;
    }

    .side-section-title{
      font-size: 11px;
      font-weight: 700;
      color: #1f2937;
      margin: 14px 6px 8px;
      text-transform: uppercase;
      opacity: .85;
    }

    .nav{
      display:flex;
      flex-direction:column;
      gap: 8px;
      padding: 0 6px;
    }

    .nav a{
      text-decoration:none;
      font-size: 13px;
      padding: 9px 10px;
      border-radius: 18px;
      color:#111;
      display:flex;
      align-items:center;
      gap: 8px;
      transition: .15s ease;
    }
    .nav a:hover{ background: rgba(255, 184, 30, 0.25); }

    .nav a.active{
      background: var(--orange);
      color:#111;
      font-weight: 700;
      box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }

    .dot-icon{
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: rgba(0,0,0,.18);
      display:none;
    }

    /* ===== Content ===== */
    .content{
      padding: 22px 24px;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      margin-bottom: 14px;
    }

    .topbar .title{
      font-size: 34px;
      font-weight: 900;
      color: var(--orange);
      letter-spacing: .2px;
    }

    .logout-btn{
      padding: 10px 18px;
      background: var(--orange);
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      box-shadow: 0 4px 10px rgba(0,0,0,.12);
    }
    .logout-btn:hover{ filter: brightness(.97); }

    /* ===== Stat cards row (same as yours) ===== */
    .stats{
      display:grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin: 12px 0 16px;
    }

    .stat{
      background: var(--card);
      border: 1px solid #f59e0b;
      border-radius: 10px;
      padding: 10px 12px;
      box-shadow: var(--shadow-soft);
      display:flex;
      align-items:center;
      justify-content:space-between;
      min-height: 54px;
    }

    .stat .label{
      font-size: 11px;
      color: var(--muted);
      font-weight: 700;
      text-transform: capitalize;
      margin-bottom: 2px;
    }
    .stat .value{
      font-size: 18px;
      font-weight: 800;
      color:#111;
      line-height: 1.1;
    }

    .stat .icon{
      width: 24px;
      height: 24px;
      border-radius: 8px;
      display:flex;
      align-items:center;
      justify-content:center;
      border: 1px solid var(--border);
      background: #fff;
      user-select:none;
      color: var(--orange);
    }

    /* ===== Menu Management Panel ===== */
    .menu-panel{
      border-radius: 12px;
      border: 2px solid rgba(245,158,11,.35);
      box-shadow: var(--shadow-soft);
      background: #fff;
      padding: 12px;
      margin-top: 6px;
    }

    .menu-panel-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      background: #f1f1f1;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 10px;
      padding: 10px 12px;
      margin-bottom: 10px;
    }

    .menu-panel-header .h-title{
      font-weight: 800;
      color:#111;
    }

    .btn-add{
      background: var(--orange);
      border: none;
      border-radius: 10px;
      padding: 8px 12px;
      font-weight: 800;
      cursor:pointer;
      display:flex;
      align-items:center;
      gap:8px;
    }
    .btn-add:hover{ filter: brightness(.97); }

    .menu-list{
      display:flex;
      flex-direction:column;
      gap: 10px;
    }

    .menu-row{
      border-radius: 10px;
      border: 2px solid rgba(245,158,11,.35);
      background: #fff;
      padding: 10px 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,.08);
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap: 12px;
    }

    .menu-left{
      flex: 1;
      min-width: 0;
    }

    .menu-top{
      display:flex;
      align-items:center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 4px;
    }

    .menu-name{
      font-weight: 900;
      letter-spacing: .3px;
      font-size: 12.5px;
      text-transform: uppercase;
    }

    .chip{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 800;
      border: 1px solid rgba(0,0,0,.12);
      background: #fff;
      color:#111;
      line-height: 1.4;
    }

    .chip.price{
      background: var(--orange-2);
      border-color: rgba(0,0,0,.08);
    }

    .desc{
      font-size: 10.5px;
      color: #444;
      margin-top: 2px;
    }
    .desc em{ font-style: normal; font-weight: 800; }

    /* availability toggle */
    .avail-wrap{
      display:flex;
      align-items:center;
      gap: 8px;
      margin-top: 6px;
    }

    .switch{
      position: relative;
      width: 40px;
      height: 20px;
      display:inline-block;
    }
    .switch input{ display:none; }

    .slider{
      position:absolute;
      inset:0;
      background: #d1d5db;
      border-radius: 999px;
      transition: .15s ease;
    }
    .slider:before{
      content:"";
      position:absolute;
      width: 16px; height: 16px;
      left: 2px; top: 2px;
      background: #fff;
      border-radius: 999px;
      transition: .15s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,.15);
    }

    .switch input:checked + .slider{
      background: var(--orange);
    }
    .switch input:checked + .slider:before{
      transform: translateX(20px);
    }

    .avail-text{
      font-size: 11px;
      color:#111;
      display:flex;
      align-items:center;
      gap: 6px;
    }
    .avail-dot{
      width: 8px; height: 8px;
      border-radius: 999px;
      background: var(--orange);
      display:inline-block;
    }
    .avail-dot.off{ background:#9ca3af; }

    /* action icons */
    .actions{
      display:flex;
      gap: 14px;
      padding-top: 4px;
    }
    .icon-btn{
      width: 28px;
      height: 28px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius: 8px;
      border: 1px solid rgba(0,0,0,.10);
      background: #fff;
      cursor:pointer;
    }
    .icon-btn:hover{ background: rgba(245,158,11,.12); }

    .icon-btn.delete:hover{ background: rgba(239,68,68,.12); border-color: rgba(239,68,68,.25); }

    /* responsive */
    @media (max-width: 980px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px){
      .shell{ grid-template-columns: 1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="shell">
    <!-- ===== Sidebar ===== -->
    <aside class="sidebar">
      <div class="brand">
        <div class="logo-box">
          <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
        </div>
      </div>

      <div class="side-section-title">Cashier Transaction</div>
      <nav class="nav">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <span class="dot-icon"></span>Order Management
        </a>

        <a href="{{ route('admin.table-management') }}"
           class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">
          <span class="dot-icon"></span>Table Management
        </a>

        <a href="#"><span class="dot-icon"></span>Daily Sales Report</a>
      </nav>

      <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
      <nav class="nav">
        <a href="{{ route('admin.menu-management') }}"
           class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">
          <span class="dot-icon"></span>Menu Management
        </a>
        <a href="#"><span class="dot-icon"></span>Inventory Management</a>
        <a href="#"><span class="dot-icon"></span>Sales and Stock Reports</a>
      </nav>
    </aside>

    <!-- ===== Main content ===== -->
    <main class="content">
      <div class="topbar">
        <div class="title">Menu Management</div>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <!-- Stats (you can change labels later) -->
      <section class="stats">
        <div class="stat">
          <div>
            <div class="label">Menu Items</div>
            <div class="value" id="menuCount">4</div>
          </div>
          <div class="icon">🍽</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Available</div>
            <div class="value" id="availCount">3</div>
          </div>
          <div class="icon">✔</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Unavailable</div>
            <div class="value" id="unavailCount">1</div>
          </div>
          <div class="icon">✖</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Categories</div>
            <div class="value" id="catCount">3</div>
          </div>
          <div class="icon">🏷</div>
        </div>
      </section>

      <!-- Menu items panel -->
      <section class="menu-panel">
        <div class="menu-panel-header">
          <div class="h-title">Menu Items</div>
          <button class="btn-add" type="button" id="btnAdd">
            <span>＋</span> Add Item
          </button>
        </div>

        <div class="menu-list" id="menuList">
          <!-- Item 1 -->
          <div class="menu-row" data-id="1">
            <div class="menu-left">
              <div class="menu-top">
                <div class="menu-name">UDSA ANGUS ROAST BEEF</div>
                <span class="chip price">₱134</span>
                <span class="chip">Main Courses</span>
              </div>

              <div class="desc">10-hours roast angus beef tenderloin (400g) with buttered veggies and potatoes <em>(for sharing)</em></div>

              <div class="avail-wrap">
                <label class="switch">
                  <input type="checkbox" checked class="availToggle">
                  <span class="slider"></span>
                </label>
                <div class="avail-text"><span class="avail-dot"></span><span class="availLabel">Available</span></div>
              </div>
            </div>

            <div class="actions">
              <button class="icon-btn edit" title="Edit">
                <!-- pencil -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
              </button>

              <button class="icon-btn delete" title="Delete">
                <!-- trash -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"/>
                  <path d="M8 6V4h8v2"/>
                  <path d="M19 6l-1 14H6L5 6"/>
                  <path d="M10 11v6"/>
                  <path d="M14 11v6"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="menu-row" data-id="2">
            <div class="menu-left">
              <div class="menu-top">
                <div class="menu-name">STEAK SALPICAO</div>
                <span class="chip price">₱88</span>
                <span class="chip">Main Courses</span>
              </div>

              <div class="desc">Seasoned tender chunks ribeye steak with stir fry garlic <em>(for sharing)</em></div>

              <div class="avail-wrap">
                <label class="switch">
                  <input type="checkbox" checked class="availToggle">
                  <span class="slider"></span>
                </label>
                <div class="avail-text"><span class="avail-dot"></span><span class="availLabel">Available</span></div>
              </div>
            </div>

            <div class="actions">
              <button class="icon-btn edit" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
              </button>

              <button class="icon-btn delete" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"/>
                  <path d="M8 6V4h8v2"/>
                  <path d="M19 6l-1 14H6L5 6"/>
                  <path d="M10 11v6"/>
                  <path d="M14 11v6"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="menu-row" data-id="3">
            <div class="menu-left">
              <div class="menu-top">
                <div class="menu-name">BEEFSILOG</div>
                <span class="chip price">₱288</span>
                <span class="chip">All-Day Breakfast Entrees</span>
              </div>

              <div class="desc">Tender beef samgyup with egg and fried rice <em>(rice with drinks included)</em></div>

              <div class="avail-wrap">
                <label class="switch">
                  <input type="checkbox" checked class="availToggle">
                  <span class="slider"></span>
                </label>
                <div class="avail-text"><span class="avail-dot"></span><span class="availLabel">Available</span></div>
              </div>
            </div>

            <div class="actions">
              <button class="icon-btn edit" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
              </button>

              <button class="icon-btn delete" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"/>
                  <path d="M8 6V4h8v2"/>
                  <path d="M19 6l-1 14H6L5 6"/>
                  <path d="M10 11v6"/>
                  <path d="M14 11v6"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Item 4 (Unavailable) -->
          <div class="menu-row" data-id="4">
            <div class="menu-left">
              <div class="menu-top">
                <div class="menu-name">CHEESYN CARBONARA</div>
                <span class="chip">Solo Size</span>
                <span class="chip price">₱134</span>
                <span class="chip">Main Courses</span>
              </div>

              <div class="desc">Cheesy carbonara pasta with creamy sauce <em>(solo)</em></div>

              <div class="avail-wrap">
                <label class="switch">
                  <input type="checkbox" class="availToggle">
                  <span class="slider"></span>
                </label>
                <div class="avail-text"><span class="avail-dot off"></span><span class="availLabel">Unavailable</span></div>
              </div>
            </div>

            <div class="actions">
              <button class="icon-btn edit" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
              </button>

              <button class="icon-btn delete" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"/>
                  <path d="M8 6V4h8v2"/>
                  <path d="M19 6l-1 14H6L5 6"/>
                  <path d="M10 11v6"/>
                  <path d="M14 11v6"/>
                </svg>
              </button>
            </div>
          </div>

        </div>
      </section>

    </main>
  </div>

  <script>
    // update toggle label + dot color
    document.querySelectorAll('.menu-row').forEach(row => {
      const toggle = row.querySelector('.availToggle');
      const dot = row.querySelector('.avail-dot');
      const label = row.querySelector('.availLabel');

      const sync = () => {
        if (toggle.checked) {
          dot.classList.remove('off');
          label.textContent = 'Available';
        } else {
          dot.classList.add('off');
          label.textContent = 'Unavailable';
        }
        updateStats();
      };

      toggle.addEventListener('change', sync);
      sync();
    });

    function updateStats(){
      const rows = document.querySelectorAll('.menu-row');
      const total = rows.length;
      let avail = 0;

      rows.forEach(r => {
        const t = r.querySelector('.availToggle');
        if (t && t.checked) avail++;
      });

      document.getElementById('menuCount').textContent = total;
      document.getElementById('availCount').textContent = avail;
      document.getElementById('unavailCount').textContent = total - avail;

      const cats = new Set();
      rows.forEach(r => {
        r.querySelectorAll('.chip').forEach(c => {
          const txt = c.textContent.trim();
          if (!txt.startsWith('₱') && txt !== 'Solo Size') cats.add(txt);
        });
      });
      document.getElementById('catCount').textContent = cats.size || 0;
    }

    // Add Item button (placeholder)
    document.getElementById('btnAdd')?.addEventListener('click', () => {
      alert('Add Item form/modal next (we can build this next).');
    });

    updateStats();
  </script>
</body>
</html>
