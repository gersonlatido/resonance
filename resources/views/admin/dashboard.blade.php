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
      --sidebar: #e4e3e3;
      --text: #222;
      --muted: #6b7280;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --card: #fff;
      --border: #e5e7eb;
      --shadow-soft: 0 10px 30px rgba(0,0,0,.08);
      --radius: 16px;
    }

    * { box-sizing: border-box; }

    body{
      margin:0;
      font-family: 'Figtree', sans-serif;
      background: #ffffff;
      color: var(--text);
    }

    /* Layout */
    .shell{
      width: 100%;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 240px 1fr;
    }

    /* Sidebar */
    .sidebar{
      background: var(--sidebar);
      padding: 18px 14px;
      border-right: 1px solid rgba(0,0,0,.06);
    }

    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 6px 6px 14px 6px;
    }

    .logo-box{
      width: 120px;
      height: 58px;
      background: #fff;
      border-radius: 10px;
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
      font-weight: 800;
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
      padding: 10px 12px;
      border-radius: 999px;
      color:#111;
      display:flex;
      align-items:center;
      gap: 8px;
      transition: .15s ease;
      background: rgba(255,255,255,.55);
      border: 1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background: rgba(255, 184, 30, 0.25); }

    .nav a.active{
      background: var(--orange);
      color:#111;
      font-weight: 800;
      border-color: rgba(0,0,0,.06);
      box-shadow: 0 6px 14px rgba(0,0,0,.12);
    }

    /* Main content */
    .content{
      padding: 22px 24px;
      background: #ffffff;
    }

    .header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap: 16px;
      margin-bottom: 16px;
    }

    .header-left .title{
      font-size: 26px;
      font-weight: 900;
      color: var(--orange);
      letter-spacing: .2px;
      margin: 0;
    }
    .header-left .subtitle{
      margin-top: 6px;
      font-size: 13px;
      color: var(--muted);
      font-weight: 600;
    }

    .logout-btn{
      padding: 10px 16px;
      background: var(--orange);
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 900;
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
    }
    .logout-btn:hover{ filter: brightness(.97); }

    /* Stats */
    .stats{
      display:grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 16px;
    }

    .stat{
      background: var(--card);
      border: 1px solid rgba(245,158,11,.50);
      border-radius: var(--radius);
      padding: 14px;
      box-shadow: var(--shadow-soft);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      min-height: 86px;
    }

    .stat .label{
      font-size: 12px;
      color: var(--muted);
      font-weight: 800;
      margin-bottom: 6px;
    }
    .stat .value{
      font-size: 22px;
      font-weight: 900;
      color:#111;
      line-height: 1;
    }

    .stat .icon{
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display:flex;
      align-items:center;
      justify-content:center;
      border: 1px solid var(--border);
      background: #fff;
      user-select:none;
    }
    .icon svg{ width: 22px; height: 22px; }

    .icon.active { color: var(--orange); }
    .icon.pending { color: #f59e0b; }
    .icon.cancelled { color: #ef4444; border-color: rgba(239,68,68,.35); }
    .icon.served { color: #16a34a; border-color: rgba(22,163,74,.25); }

    /* Tables grid */
    .tables-grid{
      display:grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .table-card{
      background: #fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
      overflow: hidden;
    }

    .table-card-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      padding: 14px 14px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      background: linear-gradient(0deg, rgba(245,158,11,.08), rgba(245,158,11,.08));
    }

    .table-title{
      display:flex;
      align-items:center;
      gap: 10px;
      font-weight: 900;
      color:#111;
    }

    .table-badge{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #fff;
      border: 1px solid rgba(0,0,0,.08);
      font-size: 12px;
      font-weight: 900;
      color: #111;
    }
    .dot{
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: var(--orange);
      display:inline-block;
    }

    .table-card-body{
      padding: 14px;
    }

    .columns{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .col{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 14px;
      background: #fafafa;
      overflow: hidden;
    }

    .col-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 10px 10px;
      background: #fff;
      border-bottom: 1px solid rgba(0,0,0,.06);
    }

    .col-head strong{
      font-size: 12px;
      letter-spacing:.2px;
    }

    .col-head .mini{
      font-size: 11px;
      color: var(--muted);
      font-weight: 800;
    }

    .panel-body{
      height: 320px;
      overflow:auto;
      padding: 10px;
    }

    .empty{
      color: #6b7280;
      font-size: 13px;
      text-align:center;
      margin-top: 18px;
      font-weight: 700;
    }

    .order-item{
      background:#fff;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 10px 10px;
      margin-bottom: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }

    .order-top{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      margin-bottom: 8px;
      font-size: 12.5px;
    }
    .order-top strong{ font-weight: 900; }
    .muted{ color: var(--muted); font-weight: 700; }

    .controls{
      display:flex;
      gap: 8px;
      align-items:center;
      flex-wrap:wrap;
      margin: 6px 0 6px;
    }
    .controls select{
      padding: 7px 9px;
      border-radius: 10px;
      border: 1px solid rgba(0,0,0,.12);
      font-size: 12px;
      font-weight: 800;
      background:#fff;
      outline:none;
    }

    .save-msg{
      margin-left:auto;
      font-size:12px;
      color:#6b7280;
      font-weight: 800;
    }

    .order-item ul{
      margin: 8px 0 0 16px;
      padding: 0;
      font-size: 12.5px;
    }
    .order-item ul li{ margin-top: 5px; }

    .total{
      margin-top: 10px;
      display:flex;
      justify-content:space-between;
      font-weight: 900;
      font-size: 13px;
      color: var(--orange-2);
      border-top: 1px dashed rgba(0,0,0,.12);
      padding-top: 8px;
    }

    /* Responsive */
    @media (max-width: 1100px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .tables-grid{ grid-template-columns: 1fr; }
    }
    @media (max-width: 720px){
      .shell{ grid-template-columns: 1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns: 1fr; }
      .columns{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="shell">

    <!-- Sidebar -->
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

        <a href="#">
          Daily Sales Report
        </a>
      </nav>

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

        <a href="#">Inventory Management</a>
        <a href="#">Sales and Stock Reports</a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="content">

      <!-- Header -->
      <div class="header">
        <div class="header-left">
          <h1 class="title">Order Management</h1>
          <div class="subtitle">Manage orders per table (Preparing → Serving → Served / Cancelled)</div>
        </div>

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
          <div class="icon active" title="Active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 11a4 4 0 1 0-8 0"/>
              <path d="M4 20a6 6 0 0 1 16 0"/>
            </svg>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Pending Orders</div>
            <div class="value" id="pendingCount">0</div>
          </div>
          <div class="icon pending" title="Pending">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
          <div class="icon cancelled" title="Cancelled">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
          <div class="icon served" title="Served">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6 9 17l-5-5"/>
            </svg>
          </div>
        </div>
      </section>

      @php
        $grouped = $orders->groupBy('table_number');
      @endphp

      <!-- Tables Grid -->
      <section class="tables-grid">

        @foreach($grouped as $tableNumber => $tableOrders)
          @php
            $inProgress = $tableOrders->whereIn('status', ['preparing','serving']);
            $done = $tableOrders->whereIn('status', ['served','cancelled']);
          @endphp

          <article class="table-card">
            <div class="table-card-header">
              <div class="table-title">
                <span class="table-badge"><span class="dot"></span> Table {{ $tableNumber }}</span>
                <span class="muted">{{ $inProgress->count() }} active • {{ $done->count() }} done</span>
              </div>
            </div>

            <div class="table-card-body">
              <div class="columns">

                <!-- In Progress -->
                <div class="col">
                  <div class="col-head">
                    <strong>In Progress</strong>
                    <span class="mini">Preparing / Serving</span>
                  </div>

                  <div class="panel-body">
                    @if($inProgress->count() === 0)
                      <div class="empty">No preparing/serving orders.</div>
                    @else
                      @foreach($inProgress as $order)
                        <div class="order-item">
                          <div class="order-top">
                            <div>
                              <strong>{{ $order->order_code }}</strong>
                              <span class="muted">• Table {{ $order->table_number }}</span>
                            </div>
                            <span class="save-msg"></span>
                          </div>

                          <div class="controls">
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
                          </div>

                          <ul>
                            @foreach($order->items as $item)
                              <li>{{ $item->name }} × {{ $item->qty }}</li>
                            @endforeach
                          </ul>

                          <div class="total">
                            <span>Total</span>
                            <span>₱{{ number_format($order->total,2) }}</span>
                          </div>
                        </div>
                      @endforeach
                    @endif
                  </div>
                </div>

                <!-- Done -->
                <div class="col">
                  <div class="col-head">
                    <strong>Done</strong>
                    <span class="mini">Served / Cancelled</span>
                  </div>

                  <div class="panel-body">
                    @if($done->count() === 0)
                      <div class="empty">No served/cancelled orders.</div>
                    @else
                      @foreach($done as $order)
                        <div class="order-item">
                          <div class="order-top">
                            <div>
                              <strong>{{ $order->order_code }}</strong>
                              <span class="muted">• {{ ucfirst($order->status) }}</span>
                            </div>
                          </div>

                          <ul>
                            @foreach($order->items as $item)
                              <li>{{ $item->name }} × {{ $item->qty }}</li>
                            @endforeach
                          </ul>

                          <div class="total">
                            <span>Total</span>
                            <span>₱{{ number_format($order->total,2) }}</span>
                          </div>
                        </div>
                      @endforeach
                    @endif
                  </div>
                </div>

              </div>
            </div>
          </article>

        @endforeach
      </section>

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
  </script>
</body>
</html>
