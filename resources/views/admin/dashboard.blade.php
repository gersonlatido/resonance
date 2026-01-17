<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
      font-family: Arial, sans-serif;
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
      background: var(--sidebar);
      padding: 18px 14px;
      position: relative;
    }

    .sidebar .brand{
      display:flex;
      align-items:center;
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
    .nav a:hover{ background: rgba(255,255,255,.25); }

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
      border: 1px solid var(--border);
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

    .order-item .total{
      margin-top: 8px;
      display:flex;
      justify-content:space-between;
      font-weight: 800;
      font-size: 13px;
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
          <!-- <img src="/images/silog-cafe-logo.png" alt="Silog Cafe Logo" /> -->
          <div class="logo-fallback">99<br/>Silog Cafe</div>
        </div>
      </div>

      <div class="side-section-title">Cashier Transaction</div>
      <nav class="nav">
        <a href="#" class="active"><span class="dot-icon"></span>Order Management</a>
        <a href="#"><span class="dot-icon"></span>Table Management</a>
        <a href="#"><span class="dot-icon"></span>Daily Sales Report</a>
      </nav>

      <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
      <nav class="nav">
        <a href="#"><span class="dot-icon"></span>Menu Management</a>
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
          <div class="icon active" title="Active">👥</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Pending Orders</div>
            <div class="value" id="pendingCount">0</div>
          </div>
          <div class="icon pending" title="Pending">🕒</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Cancelled Orders</div>
            <div class="value" id="cancelledCount">0</div>
          </div>
          <div class="icon cancelled" title="Cancelled">✖</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Order Served</div>
            <div class="value" id="servedCount">0</div>
          </div>
          <div class="icon served" title="Served">≡</div>
        </div>
      </section>

      <!-- Panels -->
      <section class="board" aria-label="Orders board">
        <div class="order-panel">
          <span class="chip" id="leftChip">Table 1</span>
          <div class="panel-body" id="activePanel"></div>
        </div>

        <div class="order-panel">
          <span class="chip" id="rightChip">Table 1</span>
          <div class="panel-body" id="pendingPanel"></div>
        </div>
      </section>
    </main>
  </div>

  <script>
    function money(n) {
      const num = Number(n || 0);
      return num.toFixed(2);
    }

    function calculateTotalPrice(items) {
      return items.reduce((sum, it) => {
        const price = Number(it.price || 0);
        const qty = Number(it.qty || 1);
        return sum + price * qty;
      }, 0);
    }

    function groupCartIntoOneOrder(cart) {
      const table = "Table 1";
      const orderId = "ORD001";
      const total = calculateTotalPrice(cart);

      return {
        id: orderId,
        table,
        items: cart.map(it => ({
          name: it.name || "Item",
          qty: Number(it.qty || 1),
          price: Number(it.price || 0)
        })),
        total
      };
    }

    function renderOrderCard(order) {
      const div = document.createElement("div");
      div.className = "order-item";

      const header = document.createElement("div");
      header.className = "row";
      header.innerHTML = `
        <div><strong>${order.id}</strong> <span class="muted">(${order.table})</span></div>
        <div class="muted">${order.items.length} item(s)</div>
      `;
      div.appendChild(header);

      const ul = document.createElement("ul");
      order.items.forEach(it => {
        const li = document.createElement("li");
        li.textContent = `${it.name} × ${it.qty} — ${money(it.price * it.qty)}`;
        ul.appendChild(li);
      });
      div.appendChild(ul);

      const totalRow = document.createElement("div");
      totalRow.className = "total";
      totalRow.innerHTML = `<span>Total</span><span>${money(order.total)}</span>`;
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

    // Fetch cart from localStorage (same as your old code)
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Active order from cart
    const activeOrder = groupCartIntoOneOrder(cart);

    // Pending orders (replace with real backend data later)
    const pendingOrders = [];

    // Counts
    document.getElementById("activeCount").textContent = cart.length ? 1 : 0;
    document.getElementById("pendingCount").textContent = pendingOrders.length;
    document.getElementById("cancelledCount").textContent = 0;
    document.getElementById("servedCount").textContent = 0;

    // Render panels
    const activePanel = document.getElementById("activePanel");
    const pendingPanel = document.getElementById("pendingPanel");

    if (!cart.length) {
      setEmpty(activePanel, "No active orders.");
    } else {
      activePanel.innerHTML = "";
      activePanel.appendChild(renderOrderCard(activeOrder));
    }

    if (!pendingOrders.length) {
      setEmpty(pendingPanel, "No pending orders.");
    } else {
      pendingPanel.innerHTML = "";
      pendingOrders.forEach(o => pendingPanel.appendChild(renderOrderCard(o)));
    }
  </script>
</body>
</html>
