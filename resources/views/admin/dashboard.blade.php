<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Order Management</title>

  <style>
    :root{
      --panel: #ffffff;
      --text: #222;
      --muted: #6b7280;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --card: #fff;
      --border: #e5e7eb;
      --shadow-soft: 0 6px 14px rgba(0,0,0,.10);
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
      display: grid;
      grid-template-columns: 230px 1fr;
    }

    .sidebar{
      background: #e4e3e3;
      padding: 18px 14px;
    }

    .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding-bottom: 14px;
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
      transition: .15s ease;
    }
    .nav a:hover{ background: rgba(255, 184, 30, 0.25); }
    .nav a.active{
      background: var(--orange);
      color:#111;
      font-weight: 700;
      box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }

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
    }
    .stat .value{
      font-size: 18px;
      font-weight: 800;
      color:#111;
    }

    /* ✅ 4 panels */
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
    .order-item ul li{ margin-top: 5px }

    .order-item .total{
      margin-top: 8px;
      display:flex;
      justify-content:space-between;
      font-weight: 800;
      font-size: 13px;
      color: #ffb300
    }

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
          Order Management
        </a>

        <a href="{{ route('admin.table-management') }}"
           class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">
          Table Management
        </a>

        <a href="#">Daily Sales Report</a>
      </nav>
        <div class="side-section-title" style="margin-top:18px;">
  Admin Management
</div>

<nav class="nav">
  <a href="javascript:void(0)" onclick="return false;">
    Menu Management
  </a>

  <a href="javascript:void(0)" onclick="return false;">
    Inventory Management
  </a>

  <a href="javascript:void(0)" onclick="return false;">
    Sales and Stock Reports
  </a>
</nav>

    </aside>

    <main class="content">
      <div class="topbar">
        <div class="title">Order Management</div>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <section class="stats">
        <div class="stat"><div><div class="label">Active Orders</div><div class="value" id="activeCount">0</div></div></div>
        <div class="stat"><div><div class="label">Pending Orders</div><div class="value" id="pendingCount">0</div></div></div>
        <div class="stat"><div><div class="label">Cancelled Orders</div><div class="value" id="cancelledCount">0</div></div></div>
        <div class="stat"><div><div class="label">Order Served</div><div class="value" id="servedCount">0</div></div></div>
      </section>

      <!-- ✅ 4 Panels -->
      <section class="board">
        <div class="order-panel">
          <span class="chip">ACTIVE (PAID)</span>
          <div class="panel-body" id="activePanel"></div>
        </div>

        <div class="order-panel">
          <span class="chip">PENDING</span>
          <div class="panel-body" id="pendingPanel"></div>
        </div>

        <div class="order-panel">
          <span class="chip">CANCELLED</span>
          <div class="panel-body" id="cancelledPanel"></div>
        </div>

        <div class="order-panel">
          <span class="chip">SERVED</span>
          <div class="panel-body" id="servedPanel"></div>
        </div>
      </section>
    </main>
  </div>

  <script>
    function money(n) {
      const num = Number(n || 0);
      return num.toFixed(2);
    }

    function normalizeItem(it) {
      const quantity  = Number(it.quantity ?? it.qty ?? 1);
      const unitPrice = Number(it.unit_price ?? it.price ?? 0);
      const subtotal  = Number(it.subtotal ?? it.line_total ?? (unitPrice * quantity));
      return { quantity, unitPrice, subtotal };
    }

    function renderOrderCard(order) {
      const div = document.createElement("div");
      div.className = "order-item";

      const header = document.createElement("div");
      header.className = "row";
      header.innerHTML = `
        <div><strong>${order.order_code}</strong> <span class="muted">(${order.status})</span></div>
        <div class="muted">${(order.items || []).length} item(s)</div>
      `;
      div.appendChild(header);

      const ul = document.createElement("ul");
      (order.items || []).forEach(it => {
        const { quantity, unitPrice, subtotal } = normalizeItem(it);
        const li = document.createElement("li");
        li.textContent = `${it.name} × ${quantity} — ${money(unitPrice)} each — ${money(subtotal)}`;
        ul.appendChild(li);
      });
      div.appendChild(ul);

      const computedTotal = (order.items || []).reduce((sum, it) => {
        const { subtotal } = normalizeItem(it);
        return sum + Number(subtotal || 0);
      }, 0);

      const totalRow = document.createElement("div");
      totalRow.className = "total";
      totalRow.innerHTML = `<span>Total</span><span>${money(order.total ?? computedTotal)}</span>`;
      div.appendChild(totalRow);

      return div;
    }

    function setEmpty(panelEl, message) {
      panelEl.innerHTML = "";
      const p = document.createElement("div");
      p.className = "empty";
      p.textContent = message;
      panelEl.appendChild(p);
    }

    async function loadOrders() {
      const res = await fetch('/admin/orders/json', { headers: { 'Accept': 'application/json' } });
      const orders = await res.json();
      if (!res.ok) return;

      const activeOrders    = orders.filter(o => o.status === 'paid');
      const pendingOrders   = orders.filter(o => o.status === 'pending');
      const cancelledOrders = orders.filter(o => o.status === 'cancelled');
      const servedOrders    = orders.filter(o => o.status === 'served');

      document.getElementById("activeCount").textContent = activeOrders.length;
      document.getElementById("pendingCount").textContent = pendingOrders.length;
      document.getElementById("cancelledCount").textContent = cancelledOrders.length;
      document.getElementById("servedCount").textContent = servedOrders.length;

      const activePanel = document.getElementById("activePanel");
      const pendingPanel = document.getElementById("pendingPanel");
      const cancelledPanel = document.getElementById("cancelledPanel");
      const servedPanel = document.getElementById("servedPanel");

      activePanel.innerHTML = "";
      pendingPanel.innerHTML = "";
      cancelledPanel.innerHTML = "";
      servedPanel.innerHTML = "";

      if (!activeOrders.length) setEmpty(activePanel, "No active orders.");
      else activeOrders.forEach(o => activePanel.appendChild(renderOrderCard(o)));

      if (!pendingOrders.length) setEmpty(pendingPanel, "No pending orders.");
      else pendingOrders.forEach(o => pendingPanel.appendChild(renderOrderCard(o)));

      if (!cancelledOrders.length) setEmpty(cancelledPanel, "No cancelled orders.");
      else cancelledOrders.forEach(o => cancelledPanel.appendChild(renderOrderCard(o)));

      if (!servedOrders.length) setEmpty(servedPanel, "No served orders.");
      else servedOrders.forEach(o => servedPanel.appendChild(renderOrderCard(o)));
    }

    // ✅ avoid duplicate timers if page reloads
    let ordersTimer = null;
    function startPolling() {
      if (ordersTimer) clearInterval(ordersTimer);
      loadOrders();
      ordersTimer = setInterval(loadOrders, 3000);
    }
    startPolling();
  </script>
</body>
</html>
