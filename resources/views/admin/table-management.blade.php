@php
  $tables = $tables ?? collect(); // ✅ prevent undefined variable crash
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Table Management</title>

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

    /* ====== Sidebar ====== */
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

    .logo-fallback{
      font-weight: 800;
      color:#111;
      font-size: 14px;
      line-height: 1.1;
      text-align:center;
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
      display:inline-block;
      position: relative;
      display: none;
    }

    .dot-icon::after{
      content:"";
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: rgba(255,255,255,.75);
      position:absolute;
      top:50%;
      left:50%;
      transform: translate(-50%, -50%);
    }

    /* ====== Content area ====== */
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
      font-size: 26px;
      font-weight: 800;
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

    /* ====== Stat cards row ====== */
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
      font-weight: 900;
      border: 1px solid var(--border);
      background: #fff;
      user-select:none;
    }

    .icon.active { color: var(--orange); }
    .icon.pending { color: #f59e0b; }
    .icon.cancelled { color: #ef4444; border-color: rgba(239,68,68,.4); }
    .icon.served { color: #f59e0b; }

    /* ===== Table Management Room Layout ===== */
    .tables-panel{
      border-radius: 12px;
      border: 2px solid rgba(245,158,11,.35);
      box-shadow: var(--shadow-soft);
      background: #fff;
      padding: 14px 14px 12px;
      margin-top: 6px;
    }

    .tables-title{
      font-size: 18px;
      font-weight: 800;
      margin: 4px 0 10px;
      color:#111;
    }

    .room{
      background: #f1f1f1;
      border-radius: 12px;
      border: 1px solid rgba(0,0,0,.06);
      padding: 18px;
      position: relative;
      min-height: 330px;
      overflow: hidden;
    }

    .divider{
      position:absolute;
      width: 6px;
      background: var(--orange);
      border-radius: 6px;
      opacity: .95;
    }

    .divider.top-mid{ left: 36%; top: 54px; height: 80px; }
    .divider.bottom-mid{ left: 36%; top: 210px; height: 140px; }

    .tcard{
      position:absolute;
      background: #fff;
      border: 2px solid var(--orange);
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,.12);
      padding: 10px 10px 8px;
      min-width: 92px;
      min-height: 62px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      gap: 6px;
      cursor: pointer;
      user-select:none;
      transition: transform .15s ease, box-shadow .15s ease;
    }

    .tcard:hover{
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0,0,0,.14);
    }

    .tcard .tno{
      position:absolute;
      top: 6px;
      left: 8px;
      font-size: 12px;
      color:#111;
      opacity: .75;
      font-weight: 800;
    }

    .tcard .tstatus{
      font-size: 15px;
      font-weight: 500;
      color:#111;
      text-align: center;
    }

    .tcard.unavailable{
      border-color: #ef4444;
    }

    .shape-rect{ border-radius: 12px; }
    .shape-wide{ border-radius: 12px; min-width: 140px; }
    .shape-round{
      width: 86px; height: 86px; min-width: 86px; min-height: 86px;
      border-radius: 999px;
      padding-top: 18px;
    }
    .shape-oval{
      width: 96px; height: 132px; min-width: 96px; min-height: 132px;
      border-radius: 999px;
      padding-top: 18px;
    }

    @media (max-width: 980px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .room{ overflow:auto; }
      .room-inner{ position: relative; width: 900px; height: 360px; }
    }

    @media (max-width: 640px){
      .shell{ grid-template-columns: 1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns: 1fr; }
    }

    /* ===== Modal ===== */
    .confirm-overlay{
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 16px;
    }

    .confirm-overlay.show{
      display: flex;
    }

    .confirm-modal{
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 16px;
      padding: 22px 20px 18px;
      box-shadow: 0 18px 40px rgba(0,0,0,.22);
      animation: popIn .18s ease;
    }

    @keyframes popIn{
      from{
        opacity: 0;
        transform: scale(.96);
      }
      to{
        opacity: 1;
        transform: scale(1);
      }
    }

    .confirm-title{
      font-size: 20px;
      font-weight: 800;
      color: #111;
      margin-bottom: 10px;
    }

    .confirm-text{
      font-size: 15px;
      color: #374151;
      line-height: 1.5;
      margin-bottom: 18px;
    }

    .confirm-actions{
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .btn-cancel,
    .btn-confirm{
      border: none;
      border-radius: 10px;
      padding: 10px 16px;
      font-weight: 700;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-cancel{
      background: #e5e7eb;
      color: #111827;
    }

    .btn-confirm{
      background: var(--orange);
      color: #111;
      box-shadow: 0 4px 10px rgba(0,0,0,.10);
    }

    .btn-cancel:hover,
    .btn-confirm:hover{
      filter: brightness(.97);
    }
  </style>
</head>

<body>
  @php
    $tableIsAvailable = function(int $n) use ($tables) {
        return isset($tables[$n]) ? (bool) $tables[$n]->is_available : true;
    };

    $userPosition = strtolower(auth()->user()->position ?? '');
    $isAdmin = $userPosition === 'admin';
    $isCashier = $userPosition === 'cashier';
  @endphp

  <div class="shell">
    <!-- ===== Sidebar ===== -->
    <aside class="sidebar">
      <div class="brand">
        <div class="logo-box">
          <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
        </div>
      </div>

      @if($isAdmin || $isCashier)
        <div class="side-section-title">Cashier Transaction</div>
        <nav class="nav">
   <a href="{{ route('admin.dashboard.analytics') }}"   class="{{ request()->routeIs('admin.dashboard.analytics') ? 'active' : '' }}">Dashboard</a>

          <a href="{{ route('admin.dashboard') }}"
             class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Order Management
          </a>

          <a href="{{ route('admin.table-management') }}"
             class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">
            Table Management
          </a>

          <a href="{{ route('admin.daily-sales-report') }}"
             class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">
            Sales Report
          </a>
        </nav>
      @endif

      @if($isAdmin)
        <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
        <nav class="nav">
          <a href="{{ route('admin.menu-management') }}"
             class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">
            Menu Management
          </a>

          <a href="{{ route('admin.feedbacks') }}"
             class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">
            Feedback Management
          </a>

          <a href="{{ route('admin.inventory') }}"
             class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
            Inventory Management
          </a>

          <a href="{{ route('admin.sales-stock-reports') }}"
             class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">
            Stock Reports
          </a>

          <a href="{{ route('admin.users.index') }}"
             class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            User Management
          </a>
        </nav>
      @endif
    </aside>

    <!-- ===== Main content ===== -->
    <main class="content">
      <div class="topbar">
        <div class="title">Table Management</div>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <!-- Stats -->
      <section class="stats" aria-label="Order stats">
        <div class="stat">
          <div>
            <div class="label">Active Orders</div>
            <div class="value">{{ $activeCount ?? 0 }}</div>
          </div>
          <div class="icon active">A</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Pending Orders</div>
            <div class="value">{{ $pendingCount ?? 0 }}</div>
          </div>
          <div class="icon pending">P</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Cancelled Orders</div>
            <div class="value">{{ $cancelledCount ?? 0 }}</div>
          </div>
          <div class="icon cancelled">C</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Order Served</div>
            <div class="value">{{ $servedCount ?? 0 }}</div>
          </div>
          <div class="icon served">S</div>
        </div>
      </section>

      <!-- ===== Table Management Layout ===== -->
      <section class="tables-panel" aria-label="Table management">
        <div class="tables-title">Tables</div>

        <div class="room">
          <div class="divider top-mid"></div>
          <div class="divider bottom-mid"></div>

          <div class="room-inner" style="position:relative; width:100%; height:330px;">

            <!-- Left column: 9,8,7 -->
            <div class="tcard shape-rect {{ $tableIsAvailable(9) ? '' : 'unavailable' }}" data-table="9" style="left: 18px; top: 22px;">
              <div class="tno">9</div>
              <div class="tstatus">{{ $tableIsAvailable(9) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <div class="tcard shape-rect {{ $tableIsAvailable(8) ? '' : 'unavailable' }}" data-table="8" style="left: 18px; top: 110px;">
              <div class="tno">8</div>
              <div class="tstatus">{{ $tableIsAvailable(8) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <div class="tcard shape-rect {{ $tableIsAvailable(7) ? '' : 'unavailable' }}" data-table="7" style="left: 18px; top: 198px;">
              <div class="tno">7</div>
              <div class="tstatus">{{ $tableIsAvailable(7) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Top left: 10 -->
            <div class="tcard shape-rect {{ $tableIsAvailable(10) ? '' : 'unavailable' }}" data-table="10" style="left: 165px; top: 22px;">
              <div class="tno">10</div>
              <div class="tstatus">{{ $tableIsAvailable(10) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Center top: 4 and 3 -->
            <div class="tcard shape-rect {{ $tableIsAvailable(4) ? '' : 'unavailable' }}" data-table="4" style="left: 380px; top: 22px;">
              <div class="tno">4</div>
              <div class="tstatus">{{ $tableIsAvailable(4) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <div class="tcard shape-rect {{ $tableIsAvailable(3) ? '' : 'unavailable' }}" data-table="3" style="left: 495px; top: 22px;">
              <div class="tno">3</div>
              <div class="tstatus">{{ $tableIsAvailable(3) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Bottom left: 6 (round) -->
            <div class="tcard shape-round {{ $tableIsAvailable(6) ? '' : 'unavailable' }}" data-table="6" style="left: 200px; top: 240px;">
              <div class="tno">6</div>
              <div class="tstatus">{{ $tableIsAvailable(6) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Bottom center: 5 (oval) -->
            <div class="tcard shape-oval {{ $tableIsAvailable(5) ? '' : 'unavailable' }}" data-table="5" style="left: 420px; top: 196px;">
              <div class="tno">5</div>
              <div class="tstatus">{{ $tableIsAvailable(5) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Right: 2 (wide) -->
            <div class="tcard shape-wide {{ $tableIsAvailable(2) ? '' : 'unavailable' }}" data-table="2" style="right: 18px; top: 70px;">
              <div class="tno">2</div>
              <div class="tstatus">{{ $tableIsAvailable(2) ? 'Available' : 'Unavailable' }}</div>
            </div>

            <!-- Right: 1 (wide) -->
            <div class="tcard shape-wide {{ $tableIsAvailable(1) ? '' : 'unavailable' }}" data-table="1" style="right: 18px; top: 210px;">
              <div class="tno">1</div>
              <div class="tstatus">{{ $tableIsAvailable(1) ? 'Available' : 'Unavailable' }}</div>
            </div>

          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ===== Confirmation Modal ===== -->
  <div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-modal">
      <div class="confirm-title">Confirm Table Status</div>
      <div class="confirm-text" id="confirmText">
        Do you confirm to set Table #1 to Available?
      </div>
      <div class="confirm-actions">
        <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
        <button type="button" class="btn-confirm" id="confirmBtn">Confirm</button>
      </div>
    </div>
  </div>

<script>
const csrf = '{{ csrf_token() }}';
const canToggleTables = @json($isAdmin || $isCashier);

const confirmOverlay = document.getElementById('confirmOverlay');
const confirmText = document.getElementById('confirmText');
const confirmBtn = document.getElementById('confirmBtn');
const cancelBtn = document.getElementById('cancelBtn');

let selectedCard = null;
let autoRefreshTimer = null;

/* ===== OPEN CONFIRM MODAL ===== */
function openConfirmModal(card) {
  if (!canToggleTables) return;

  selectedCard = card;

  const tableNo = card.dataset.table;
  const isUnavailable = card.classList.contains('unavailable');
  const nextStatus = isUnavailable ? 'Available' : 'Unavailable';

  confirmText.textContent = `Do you confirm to set Table #${tableNo} to ${nextStatus}?`;
  confirmOverlay.classList.add('show');
}

/* ===== CLOSE MODAL ===== */
function closeConfirmModal() {
  confirmOverlay.classList.remove('show');
  selectedCard = null;
}

/* ===== TOGGLE TABLE ===== */
async function toggleTable(card) {
  if (!canToggleTables) return;

  const tableNo = card.dataset.table;

  try {
    const res = await fetch(`/admin/tables/${tableNo}/toggle`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!res.ok) {
      alert('Failed to update table');
      return;
    }

    window.location.reload();

  } catch (error) {
    console.error(error);
    alert('Network error');
  }
}

/* ===== CLICK TABLE ===== */
document.querySelectorAll('.tcard').forEach(card => {
  card.addEventListener('click', () => {
    openConfirmModal(card);
  });

if (!canToggleTables) {
  card.style.cursor = 'default';
}
});

/* ===== CONFIRM BUTTON ===== */
confirmBtn.addEventListener('click', async () => {
  if (!selectedCard) return;

  const card = selectedCard;
  closeConfirmModal();
  await toggleTable(card);
});

/* ===== CANCEL BUTTON ===== */
cancelBtn.addEventListener('click', closeConfirmModal);

/* ===== CLICK OUTSIDE MODAL ===== */
confirmOverlay.addEventListener('click', (e) => {
  if (e.target === confirmOverlay) {
    closeConfirmModal();
  }
});

/* ===== ESC KEY CLOSE ===== */
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && confirmOverlay.classList.contains('show')) {
    closeConfirmModal();
  }
});

/* ===== AUTO REFRESH ===== */
function startAutoRefresh() {
  if (autoRefreshTimer) clearInterval(autoRefreshTimer);

  autoRefreshTimer = setInterval(() => {
    const modalOpen = confirmOverlay.classList.contains('show');
    const pageHidden = document.hidden;

    if (!modalOpen && !pageHidden) {
      window.location.reload();
    }
  }, 3000);
}

startAutoRefresh();
</script>
</body>
</html>