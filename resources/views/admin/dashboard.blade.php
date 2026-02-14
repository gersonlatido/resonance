<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Admin Order Management</title>
  

  <style>
    :root{
      --panel: #ffffff;         /* main white panel */
      --sidebar: #a9a9a9;       /* left gray sidebar */
      --text: #222;
      --muted: #6b7280;
      --orange: #f59e0b;        /* main accent */
      --orange-2: #ffb300;      /* chip */
      --card: #fff;
      --border: #e5e7eb;
      --shadow-soft: 0 6px 14px rgba(0,0,0,.10);
      --radius: 14px;
    }

    * { box-sizing: border-box; }

    /* ✅ Outer background removed */
    body{
      margin:0;
      font-family: 'Figtree', sans-serif;
      background: #ffffff;
      color: var(--text);
    }

    /* ✅ Fullscreen layout (no outer margin/shadow) */
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

    /* ====== Orders board ====== */
    .board{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-top: 6px;
    }

    .order-panel{
      border-radius: 12px;
      border: 2px solid rgba(245,158,11,.35);
      box-shadow: var(--shadow-soft);
      background: #fff;
      padding: 14px 14px 12px;
      min-height: 360px;
      position: relative;
    }

    .chip{
      display:inline-block;
      background: var(--orange-2);
      color:#111;
      padding: 6px 12px;
      border-radius: 999px;
      font-weight: 800;
      font-size: 12px;
      box-shadow: 0 2px 7px rgba(0,0,0,.10);
    }

    .panel-body{
      margin-top: 10px;
      background: #f5f5f5;
      border-radius: 10px;
      border: 1px solid rgba(0,0,0,.06);
      height: 300px;
      overflow:auto;
      padding: 10px;
    }

    .empty{
      color: #6b7280;
      font-size: 13px;
      text-align:center;
      margin-top: 28px;
    }

    .order-item{
      background:#fff;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 10px 10px;
      margin-bottom: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .order-item .row{
      display:flex;
      justify-content:space-between;
      gap: 10px;
      font-size: 12.5px;
    }
    .order-item .row strong{ font-weight: 800; }
    .order-item .muted{ color: var(--muted); }

    .order-item ul{
      margin: 8px 0 0 16px;
      padding: 0;
      font-size: 12.5px;
    }
       .order-item ul li{
      margin-top: 5px
    
    }

    .order-item .total{
      margin-top: 8px;
      display:flex;
      justify-content:space-between;
      font-weight: 800;
      font-size: 13px;
      color: #ffb300
    }

    /* Responsive */
    @media (max-width: 980px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .board{ grid-template-columns: 1fr; }
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
          <!-- Replace with real logo if you have it -->
           <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
          {{-- <div class="logo-fallback">99<br/>Silog Cafe</div> --}}
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

  <a href="#"
     {{-- class="{{ request()->routeIs('admin.daily-sales') ? 'active' : '' }}" --}}>
    <span class="dot-icon"></span>Daily Sales Report
  </a>
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
        <div class="title">Order Management</div>

        <!-- Laravel logout form -->
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
            <div class="value" id="activeCount">0</div>
          </div>
          <div class="icon active" title="Active"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M16 11a4 4 0 1 0-8 0"/>
  <path d="M4 20a6 6 0 0 1 16 0"/>
  <path d="M20 20v0"/>
</svg>
</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Pending Orders</div>
            <div class="value" id="pendingCount">0</div>
          </div>
          <div class="icon pending" title="Pending"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <circle cx="12" cy="12" r="9"/>
  <path d="M12 7v6l4 2"/>
</svg>
</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Cancelled Orders</div>
            <div class="value" id="cancelledCount">0</div>
          </div>
          <div class="icon cancelled" title="Cancelled"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <circle cx="12" cy="12" r="9"/>
  <path d="M9 9l6 6"/>
  <path d="M15 9l-6 6"/>
</svg>
</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Order Served</div>
            <div class="value" id="servedCount">0</div>
          </div>
          <div class="icon served" title="Served"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M6 7h14"/>
  <path d="M6 12h14"/>
  <path d="M6 17h14"/>
  <path d="M4 7h.01"/>
  <path d="M4 12h.01"/>
  <path d="M4 17h.01"/>
</svg>
</div>
        </div>
      </section>

      <!-- Panels -->
  @php
    $grouped = $orders->groupBy('table_number');
@endphp

@foreach($grouped as $tableNumber => $tableOrders)
    <section class="board">
        <div class="order-panel">
            <span class="chip">
                Table {{ $tableNumber }} (Preparing/Serving)
            </span>

            <div class="panel-body">
              <span class="save-msg" style="margin-left:10px; font-size:12px; color:#6b7280;"></span>

                @foreach($tableOrders->whereIn('status', ['preparing','serving']) as $order)
                    <div class="order-item">
                        <strong>{{ $order->order_code }}</strong>

                    <select onchange="updateStatus('{{ $order->order_code }}', this.value, this)">

                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="serving" {{ $order->status == 'serving' ? 'selected' : '' }}>Serving</option>
                            <option value="served">Served</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                       <select onchange="updateEta('{{ $order->order_code }}', this.value, this)">


                            <option value="">ETA --</option>
                            <option value="5">5 min</option>
                            <option value="10">10 min</option>
                            <option value="15">15 min</option>
                        </select>

                        <ul>
                            @foreach($order->items as $item)
                                <li>{{ $item->name }} × {{ $item->qty }}</li>
                            @endforeach
                        </ul>

                        <div class="total">
                            Total ₱{{ number_format($order->total,2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="order-panel">
            <span class="chip">
                Table {{ $tableNumber }} (Served/Cancelled)
            </span>

            <div class="panel-body">
                @foreach($tableOrders->whereIn('status', ['served','cancelled']) as $order)
                    <div class="order-item">
                        {{ $order->order_code }} - {{ ucfirst($order->status) }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endforeach

    </main>
  </div>

<script>
 function saveOrderUpdate(orderCode, payload, msgEl) {
  return fetch('/admin/api/orders/' + encodeURIComponent(orderCode), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(payload)
  })
  .then(async (res) => {
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Update failed');
    return data;
  })
  .then(() => {
    if (msgEl) {
      msgEl.textContent = 'Saved ✅';
      setTimeout(() => msgEl.textContent = '', 1200);
    }
  })
  .catch(err => {
    if (msgEl) msgEl.textContent = 'Error ❌';
    alert(err.message);
  });
}

function updateStatus(orderCode, status, el) {
  // el is the select element (optional)
  const card = el?.closest('.order-item');
  const msgEl = card ? card.querySelector('.save-msg') : null;

  return saveOrderUpdate(orderCode, { status }, msgEl);
}

function updateEta(orderCode, eta, el) {
  const card = el?.closest('.order-item');
  const msgEl = card ? card.querySelector('.save-msg') : null;

  const etaVal = (eta === '' ? null : Number(eta));
  return saveOrderUpdate(orderCode, { eta_minutes: etaVal }, msgEl);
}
  function money(n) {
    return Number(n || 0).toFixed(2);
  }

  function buildEtaOptions(selected) {
    const options = [null, 5,10,15,20,25,30,35,40,45,50,55,60];
    return options.map(v => {
      const label = v === null ? 'ETA: --' : `ETA: ${v} min`;
      const sel = String(v) === String(selected) ? 'selected' : '';
      return `<option value="${v === null ? '' : v}" ${sel}>${label}</option>`;
    }).join('');
  }

  function buildStatusOptions(selected) {
    const statuses = [
      {v:'preparing', label:'Preparing'},
      {v:'serving',   label:'Serving'},
      {v:'served',    label:'Served'},
      {v:'cancelled', label:'Cancelled'},
    ];
    return statuses.map(s => {
      const sel = s.v === selected ? 'selected' : '';
      return `<option value="${s.v}" ${sel}>${s.label}</option>`;
    }).join('');
  }

  function renderOrderCard(order) {
    const div = document.createElement("div");
    div.className = "order-item";

    const itemsCount = Array.isArray(order.items) ? order.items.length : 0;

    div.innerHTML = `
      <div class="row">
        <div>
          <strong>${order.order_code}</strong>
          <span class="muted">(Table ${order.table_number})</span>
        </div>
        <div class="muted">${itemsCount} item(s)</div>
      </div>

      <div style="display:flex; gap:8px; margin-top:8px; align-items:center;">
        <select class="statusSelect" data-code="${order.order_code}" style="padding:6px 8px; border-radius:8px; border:1px solid #e5e7eb;">
          ${buildStatusOptions(order.status)}
        </select>

        <select class="etaSelect" data-code="${order.order_code}" style="padding:6px 8px; border-radius:8px; border:1px solid #e5e7eb;">
          ${buildEtaOptions(order.eta_minutes)}
        </select>

        <span class="muted" style="margin-left:auto;" id="saveMsg-${order.order_code}"></span>
      </div>

      <ul>
        ${(order.items || []).map(it => `
          <li>${it.name} × ${it.qty} — ${money(Number(it.price)*Number(it.qty))}</li>
        `).join('')}
      </ul>

      <div class="total">
        <span>Total</span>
        <span>₱${money(order.total)}</span>
      </div>
    `;

    return div;
  }

  function setEmpty(panelEl, message) {
    panelEl.innerHTML = "";
    const p = document.createElement("div");
    p.className = "empty";
    p.textContent = message;
    panelEl.appendChild(p);
  }

  // ✅ CSRF for PUT (web.php)
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  async function updateOrder(orderCode, payload) {
    const res = await fetch(`/admin/api/orders/${encodeURIComponent(orderCode)}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data?.message || 'Update failed');
    return data.order;
  }

  async function loadOrders(tableNumber) {
    const activePanel = document.getElementById("activePanel");
    const pendingPanel = document.getElementById("pendingPanel");

    // You can keep these chips or update them
    document.getElementById("leftChip").textContent = `Table ${tableNumber} (Preparing/Serving)`;
    document.getElementById("rightChip").textContent = `Table ${tableNumber} (Served/Cancelled)`;

    const res = await fetch(`/admin/api/orders?table=${tableNumber}`, { headers: { 'Accept':'application/json' }});
    const orders = await res.json();

    // Split panels
    const left = orders.filter(o => o.status === 'preparing' || o.status === 'serving');
    const right = orders.filter(o => o.status === 'served' || o.status === 'cancelled');

    // Stats
    const activeCount = left.length;
    const pendingCount = orders.filter(o => o.status === 'preparing').length;
    const cancelledCount = orders.filter(o => o.status === 'cancelled').length;
    const servedCount = orders.filter(o => o.status === 'served').length;

    document.getElementById("activeCount").textContent = activeCount;
    document.getElementById("pendingCount").textContent = pendingCount;
    document.getElementById("cancelledCount").textContent = cancelledCount;
    document.getElementById("servedCount").textContent = servedCount;

    // Render left
    if (!left.length) setEmpty(activePanel, "No preparing/serving orders.");
    else {
      activePanel.innerHTML = "";
      left.forEach(o => activePanel.appendChild(renderOrderCard(o)));
    }

    // Render right
    if (!right.length) setEmpty(pendingPanel, "No served/cancelled orders.");
    else {
      pendingPanel.innerHTML = "";
      right.forEach(o => pendingPanel.appendChild(renderOrderCard(o)));
    }

    // Attach dropdown events
    document.querySelectorAll('.statusSelect, .etaSelect').forEach(el => {
      el.addEventListener('change', async () => {
        const code = el.dataset.code;
        const card = el.closest('.order-item');
        const status = card.querySelector('.statusSelect').value;
        const etaRaw = card.querySelector('.etaSelect').value;
        const eta = etaRaw === '' ? null : Number(etaRaw);

        const msg = document.getElementById(`saveMsg-${code}`);
        msg.textContent = 'Saving...';

        try {
          await updateOrder(code, { status, eta_minutes: eta });
          msg.textContent = 'Saved ✅';
          setTimeout(() => msg.textContent = '', 1200);

          // reload to move between panels if status changed
          await loadOrders(tableNumber);

        } catch (e) {
          msg.textContent = 'Error ❌';
          alert(e.message);
        }
      });
    });
  }

  // ✅ For now: choose which table to view
  // You can later add a dropdown "Select Table"
  loadOrders(1);
</script>

</body>
</html>
