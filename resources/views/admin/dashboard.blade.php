<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Order Management</title>

  <style>
    :root{
      --panel:#ffffff;
      --sidebar:#e4e3e3;
      --text:#222;
      --muted:#6b7280;
      --orange:#f59e0b;
      --orange-2:#ffb300;
      --card:#fff;
      --border:#e5e7eb;
      --shadow-soft:0 10px 30px rgba(0,0,0,.08);
      --radius:16px;
    }
    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family:'Figtree', sans-serif;
      background:#fff;
      color:var(--text);
    }

    /* Layout */
    .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:240px 1fr;
    }

    /* Sidebar */
    .sidebar{
      background:var(--sidebar);
      padding:18px 14px;
      border-right:1px solid rgba(0,0,0,.06);
    }
    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding:6px 6px 14px;
    }
    .logo-box{
      width:120px;
      height:58px;
      background:#fff;
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{
      width:100%;
      height:100%;
      object-fit:contain;
      padding:6px;
    }

    .side-section-title{
      font-size:11px;
      font-weight:800;
      color:#1f2937;
      margin:14px 6px 8px;
      text-transform:uppercase;
      opacity:.85;
    }
    .nav{
      display:flex;
      flex-direction:column;
      gap:8px;
      padding:0 6px;
    }
    .nav a{
      text-decoration:none;
      font-size:13px;
      padding:10px 12px;
      border-radius:999px;
      color:#111;
      display:flex;
      align-items:center;
      gap:8px;
      transition:.15s ease;
      background:rgba(255,255,255,.55);
      border:1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background:rgba(255,184,30,.25); }
    .nav a.active{
      background:var(--orange);
      color:#111;
      font-weight:800;
      border-color:rgba(0,0,0,.06);
      box-shadow:0 6px 14px rgba(0,0,0,.12);
    }

    /* Main */
    .content{
      padding:22px 24px;
      background:#fff;
    }

    .header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      margin-bottom:16px;
    }
    .header-left .title{
      font-size:26px;
      font-weight:900;
      color:var(--orange);
      letter-spacing:.2px;
      margin:0;
    }
    .header-left .subtitle{
      margin-top:6px;
      font-size:13px;
      color:var(--muted);
      font-weight:600;
    }
    .logout-btn{
      padding:10px 16px;
      background:var(--orange);
      border:none;
      border-radius:12px;
      cursor:pointer;
      font-weight:900;
      box-shadow:0 8px 20px rgba(0,0,0,.12);
    }
    .logout-btn:hover{ filter:brightness(.97); }

    /* Stats */
    .stats{
      display:grid;
      grid-template-columns:repeat(4, minmax(0, 1fr));
      gap:14px;
      margin-bottom:16px;
    }
    .stat{
      background:var(--card);
      border:1px solid rgba(245,158,11,.50);
      border-radius:var(--radius);
      padding:14px;
      box-shadow:var(--shadow-soft);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      min-height:86px;
    }
    .stat .label{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
      margin-bottom:6px;
    }
    .stat .value{
      font-size:22px;
      font-weight:900;
      color:#111;
      line-height:1;
    }
    .stat .icon{
      width:42px;
      height:42px;
      border-radius:14px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--border);
      background:#fff;
      user-select:none;
    }
    .icon svg{ width:22px; height:22px; }
    .icon.active{ color:var(--orange); }
    .icon.preparing{ color:#f59e0b; }
    .icon.serving{ color:#2563eb; border-color:rgba(37,99,235,.25); }
    .icon.served{ color:#16a34a; border-color:rgba(22,163,74,.25); }

    /* Board */
    .board{
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:var(--radius);
      box-shadow:var(--shadow-soft);
      overflow:hidden;
    }
    .board-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      padding:14px;
      border-bottom:1px solid rgba(0,0,0,.06);
      background:linear-gradient(0deg, rgba(245,158,11,.08), rgba(245,158,11,.08));
      font-weight:900;
    }
    .board-sub{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
    }

    .columns{
      padding:14px;
      display:grid;
      grid-template-columns:repeat(4, minmax(0, 1fr));
      gap:12px;
    }
    .col{
      border:1px solid rgba(0,0,0,.06);
      border-radius:14px;
      background:#fafafa;
      overflow:hidden;
      min-width:0;
    }
    .col-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:10px;
      background:#fff;
      border-bottom:1px solid rgba(0,0,0,.06);
    }
    .col-head strong{
      font-size:12px;
      letter-spacing:.2px;
    }
    .mini{
      font-size:11px;
      color:var(--muted);
      font-weight:900;
      background:rgba(245,158,11,.12);
      border:1px solid rgba(245,158,11,.20);
      padding:4px 8px;
      border-radius:999px;
    }
    .panel-body{
      height:520px;
      overflow:auto;
      padding:10px;
    }
    .empty{
      color:#6b7280;
      font-size:13px;
      text-align:center;
      margin-top:18px;
      font-weight:700;
    }

    .order-item{
      background:#fff;
      border:1px solid var(--border);
      border-radius:14px;
      padding:10px;
      margin-bottom:10px;
      box-shadow:0 2px 10px rgba(0,0,0,.06);
    }
    .order-top{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
      margin-bottom:8px;
      font-size:12.5px;
    }
    .order-top strong{ font-weight:900; }
    .muted{ color:var(--muted); font-weight:700; }

    .pill{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 8px;
      border-radius:999px;
      font-size:11px;
      font-weight:900;
      border:1px solid rgba(0,0,0,.08);
      background:#fff;
      color:#111;
      white-space:nowrap;
    }
    .pill .dot{
      width:8px; height:8px; border-radius:50%;
      background:var(--orange);
      display:inline-block;
    }

    .controls{
      display:flex;
      gap:8px;
      align-items:center;
      flex-wrap:wrap;
      margin:6px 0 6px;
    }
    .controls select{
      padding:7px 9px;
      border-radius:10px;
      border:1px solid rgba(0,0,0,.12);
      font-size:12px;
      font-weight:800;
      background:#fff;
      outline:none;
    }
    .save-msg{
      margin-left:auto;
      font-size:12px;
      color:#6b7280;
      font-weight:800;
    }

    .order-item ul{
      margin:8px 0 0 16px;
      padding:0;
      font-size:12.5px;
    }
    .order-item ul li{ margin-top:5px; }

    .total{
      margin-top:10px;
      display:flex;
      justify-content:space-between;
      font-weight:900;
      font-size:13px;
      color:var(--orange-2);
      border-top:1px dashed rgba(0,0,0,.12);
      padding-top:8px;
    }

    /* Responsive */
    @media (max-width: 1100px){
      .stats{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
      .columns{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
      .panel-body{ height:480px; }
    }
    @media (max-width: 720px){
      .shell{ grid-template-columns:1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns:1fr; }
      .columns{ grid-template-columns:1fr; }
      .panel-body{ height:auto; max-height:60vh; }
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
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Order Management</a>

        <a href="{{ route('admin.table-management') }}"
           class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">Table Management</a>

        <a href="{{ route('admin.daily-sales-report') }}"
           class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">Daily Sales Report</a>
      </nav>

      <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
      <nav class="nav">
        <a href="{{ route('admin.menu-management') }}"
           class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">Menu Management</a>

        <a href="{{ route('admin.feedbacks') }}"
           class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">Feedback Management</a>

        <a href="{{ route('admin.inventory') }}"
           class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">Inventory Management</a>

        <a href="{{ route('admin.sales-stock-reports') }}"
           class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">Sales and Stock Reports</a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="content">

      <!-- Header -->
      <div class="header">
        <div class="header-left">
          <h1 class="title">Order Management</h1>
          <div class="subtitle">Incoming paid orders appear in Active (Preparing + no ETA) → set ETA to move to Preparing → Serving → Served</div>
        </div>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      @php
        /**
         * ✅ NO CONTROLLER CHANGE
         * Show ONLY PAID orders in the admin board.
         * Your DB shows new paid orders come in as status='preparing'.
         * So "Active" = preparing with NO ETA yet (incoming/not acknowledged).
         */
        $paidOrdersAll = $orders->filter(fn($o) => (string)($o->payment_status ?? '') === 'paid');

        $activeOrders = $paidOrdersAll->filter(function($o){
          $s = strtolower(trim((string)($o->status ?? '')));
          return $s === '' || ($s === 'preparing' && empty($o->eta_minutes));
        })->sortBy('created_at');

        $preparingOrders = $paidOrdersAll->filter(function($o){
          $s = strtolower(trim((string)($o->status ?? '')));
          return $s === 'preparing' && !empty($o->eta_minutes);
        })->sortBy('created_at');

        $servingOrders = $paidOrdersAll
          ->filter(fn($o) => strtolower(trim((string)($o->status ?? ''))) === 'serving')
          ->sortBy('created_at');

        $servedOrders = $paidOrdersAll
          ->filter(fn($o) => strtolower(trim((string)($o->status ?? ''))) === 'served')
          ->sortBy('created_at');

        $totalBoard = $activeOrders->count() + $preparingOrders->count() + $servingOrders->count() + $servedOrders->count();
      @endphp

      <!-- Stats -->
      <section class="stats" id="orderStats" aria-label="Order stats">
        <div class="stat">
          <div>
            <div class="label">Active (Incoming)</div>
            <div class="value" id="activeCount">{{ $activeOrders->count() }}</div>
          </div>
          <div class="icon active" title="Active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 11a4 4 0 1 0-8 0"/><path d="M4 20a6 6 0 0 1 16 0"/>
            </svg>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Preparing</div>
            <div class="value" id="preparingCount">{{ $preparingOrders->count() }}</div>
          </div>
          <div class="icon preparing" title="Preparing">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/>
            </svg>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Serving</div>
            <div class="value" id="servingCount">{{ $servingOrders->count() }}</div>
          </div>
          <div class="icon serving" title="Serving">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 11h18"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Served</div>
            <div class="value" id="servedCount">{{ $servedOrders->count() }}</div>
          </div>
          <div class="icon served" title="Served">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6 9 17l-5-5"/>
            </svg>
          </div>
        </div>
      </section>

      <!-- Board -->
      <section class="board" id="ordersBoard">
        <div class="board-header">
          <div>All Tables Board (Paid Orders Only)</div>
          <div class="board-sub">{{ $totalBoard }} shown • auto refresh every 5s</div>
        </div>

        <div class="columns">

          <!-- ACTIVE -->
          <div class="col">
            <div class="col-head">
              <strong>Active</strong>
              <span class="mini">{{ $activeOrders->count() }}</span>
            </div>
            <div class="panel-body">
              @if($activeOrders->count() === 0)
                <div class="empty">No incoming orders.</div>
              @else
                @foreach($activeOrders as $order)
                  @php
                    $tableText = $order->table_label
                      ?? ($order->table_number ? ('Table ' . $order->table_number) : null)
                      ?? 'Table Unknown';
                  @endphp

                  <div class="order-item" data-status="{{ $order->status ?? '' }}">
                    <div class="order-top">
                      <div>
                        <strong>{{ $order->order_code }}</strong>
                        <div class="muted">
                          <span class="pill"><span class="dot"></span> {{ $tableText }}</span>
                        </div>
                      </div>
                      <span class="save-msg"></span>
                    </div>

                    <div class="controls">
                      <!-- Active is UI-only (status is actually preparing with no ETA) -->
                      <select onchange="updateStatus('{{ $order->order_code }}', this.value, this)">
                        <option value="preparing">Preparing</option>
                        <option value="serving">Serving</option>
                        <option value="served">Served</option>
                        <option value="cancelled">Cancelled</option>
                      </select>

                      <select onchange="updateEta('{{ $order->order_code }}', this.value, this)">
                        <option value="" selected>ETA --</option>
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

          <!-- PREPARING -->
          <div class="col">
            <div class="col-head">
              <strong>Preparing</strong>
              <span class="mini">{{ $preparingOrders->count() }}</span>
            </div>
            <div class="panel-body">
              @if($preparingOrders->count() === 0)
                <div class="empty">No preparing orders.</div>
              @else
                @foreach($preparingOrders as $order)
                  @php
                    $tableText = $order->table_label
                      ?? ($order->table_number ? ('Table ' . $order->table_number) : null)
                      ?? 'Table Unknown';
                  @endphp

                  <div class="order-item" data-status="{{ $order->status ?? '' }}">
                    <div class="order-top">
                      <div>
                        <strong>{{ $order->order_code }}</strong>
                        <div class="muted">
                          <span class="pill"><span class="dot"></span> {{ $tableText }}</span>
                        </div>
                      </div>
                      <span class="save-msg"></span>
                    </div>

                    <div class="controls">
                      <select onchange="updateStatus('{{ $order->order_code }}', this.value, this)">
                        <option value="preparing" selected>Preparing</option>
                        <option value="serving">Serving</option>
                        <option value="served">Served</option>
                        <option value="cancelled">Cancelled</option>
                      </select>

                      <select onchange="updateEta('{{ $order->order_code }}', this.value, this)">
                        <option value="">ETA --</option>
                        <option value="5"  {{ (string)$order->eta_minutes === "5" ? 'selected' : '' }}>5 min</option>
                        <option value="10" {{ (string)$order->eta_minutes === "10" ? 'selected' : '' }}>10 min</option>
                        <option value="15" {{ (string)$order->eta_minutes === "15" ? 'selected' : '' }}>15 min</option>
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

          <!-- SERVING -->
          <div class="col">
            <div class="col-head">
              <strong>Serving</strong>
              <span class="mini">{{ $servingOrders->count() }}</span>
            </div>
            <div class="panel-body">
              @if($servingOrders->count() === 0)
                <div class="empty">No serving orders.</div>
              @else
                @foreach($servingOrders as $order)
                  @php
                    $tableText = $order->table_label
                      ?? ($order->table_number ? ('Table ' . $order->table_number) : null)
                      ?? 'Table Unknown';
                  @endphp

                  <div class="order-item" data-status="{{ $order->status ?? '' }}">
                    <div class="order-top">
                      <div>
                        <strong>{{ $order->order_code }}</strong>
                        <div class="muted">
                          <span class="pill"><span class="dot"></span> {{ $tableText }}</span>
                        </div>
                      </div>
                      <span class="save-msg"></span>
                    </div>

                    <div class="controls">
                      <select onchange="updateStatus('{{ $order->order_code }}', this.value, this)">
                        <option value="serving" selected>Serving</option>
                        <option value="served">Served</option>
                        <option value="cancelled">Cancelled</option>
                      </select>

                      <select onchange="updateEta('{{ $order->order_code }}', this.value, this)">
                        <option value="">ETA --</option>
                        <option value="5"  {{ (string)$order->eta_minutes === "5" ? 'selected' : '' }}>5 min</option>
                        <option value="10" {{ (string)$order->eta_minutes === "10" ? 'selected' : '' }}>10 min</option>
                        <option value="15" {{ (string)$order->eta_minutes === "15" ? 'selected' : '' }}>15 min</option>
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

          <!-- SERVED -->
          <div class="col">
            <div class="col-head">
              <strong>Served</strong>
              <span class="mini">{{ $servedOrders->count() }}</span>
            </div>
            <div class="panel-body">
              @if($servedOrders->count() === 0)
                <div class="empty">No served orders.</div>
              @else
                @foreach($servedOrders as $order)
                  @php
                    $tableText = $order->table_label
                      ?? ($order->table_number ? ('Table ' . $order->table_number) : null)
                      ?? 'Table Unknown';
                  @endphp

                  <div class="order-item" data-status="{{ $order->status ?? '' }}">
                    <div class="order-top">
                      <div>
                        <strong>{{ $order->order_code }}</strong>
                        <div class="muted">
                          <span class="pill"><span class="dot"></span> {{ $tableText }}</span>
                        </div>
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
      </section>

    </main>
  </div>

  <script>
    // ========= CONFIG =========
    const AUTO_REFRESH = true;
    const REFRESH_MS = 5000; // 5 seconds
    // ==========================

    function toInt(x){ return parseInt((x || '0').toString().replace(/[^\d]/g,''), 10) || 0; }
    function incText(el, delta){ if (!el) return; el.textContent = String(toInt(el.textContent) + delta); }

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
        throw err;
      });
    }

    function getColByName(nameLower){
      const heads = Array.from(document.querySelectorAll('.col .col-head'));
      return heads.find(h => (h.querySelector('strong')?.textContent || '').trim().toLowerCase() === nameLower) || null;
    }
    function getPanelBodyByName(nameLower){
      const head = getColByName(nameLower);
      return head ? head.closest('.col')?.querySelector('.panel-body') : null;
    }
    function updateMini(nameLower, delta){
      const head = getColByName(nameLower);
      const mini = head ? head.querySelector('.mini') : null;
      if (mini) mini.textContent = String(toInt(mini.textContent) + delta);
    }
    function ensureEmpty(nameLower){
      const body = getPanelBodyByName(nameLower);
      if (!body) return;
      const hasOrder = body.querySelector('.order-item');
      const hasEmpty = body.querySelector('.empty');
      if (!hasOrder && !hasEmpty){
        const div = document.createElement('div');
        div.className = 'empty';
        div.textContent = `No ${nameLower} orders.`;
        body.appendChild(div);
      }
    }
    function removeEmptyIfAny(targetBody){
      const empty = targetBody.querySelector('.empty');
      if (empty) empty.remove();
    }

    function statusToColumn(status, eta){
      const s = (status || '').toLowerCase().trim();
      if (s === 'served') return 'served';
      if (s === 'serving') return 'serving';
      if (s === 'preparing'){
        // Incoming vs acknowledged
        return (eta == null || eta === '' || Number(eta) === 0) ? 'active' : 'preparing';
      }
      if (s === 'cancelled') return null;
      return 'active';
    }

    function updateTopStatsDelta(oldCol, newCol){
      const activeEl = document.getElementById('activeCount');
      const preparingEl = document.getElementById('preparingCount');
      const servingEl = document.getElementById('servingCount');
      const servedEl = document.getElementById('servedCount');

      const dec = (col) => {
        if (col === 'active') incText(activeEl, -1);
        if (col === 'preparing') incText(preparingEl, -1);
        if (col === 'serving') incText(servingEl, -1);
        if (col === 'served') incText(servedEl, -1);
      };
      const inc = (col) => {
        if (col === 'active') incText(activeEl, +1);
        if (col === 'preparing') incText(preparingEl, +1);
        if (col === 'serving') incText(servingEl, +1);
        if (col === 'served') incText(servedEl, +1);
      };

      if (oldCol) dec(oldCol);
      if (newCol) inc(newCol);
    }

    window.updateStatus = function(orderCode, newStatus, el){
      const card = el?.closest('.order-item');
      const msgEl = card ? card.querySelector('.save-msg') : null;
      const oldStatus = card?.getAttribute('data-status') || '';
      const etaSel = card ? card.querySelector('select[onchange^="updateEta"]') : null;
      const oldEta = etaSel ? etaSel.value : null;

      const oldCol = statusToColumn(oldStatus, oldEta);

      return saveOrderUpdate(orderCode, { status: newStatus }, msgEl).then(() => {
        if (!card) return;

        const etaSel2 = card.querySelector('select[onchange^="updateEta"]');
        const newEta = etaSel2 ? etaSel2.value : null;
        const newCol = statusToColumn(newStatus, newEta);

        updateTopStatsDelta(oldCol, newCol);

        if (oldCol) updateMini(oldCol, -1);

        if (newCol){
          updateMini(newCol, +1);
          const targetBody = getPanelBodyByName(newCol);
          if (targetBody){
            removeEmptyIfAny(targetBody);
            targetBody.prepend(card);
          }
        } else {
          card.remove();
        }

        if (oldCol) ensureEmpty(oldCol);

        card.setAttribute('data-status', newStatus);

        scheduleSoftRefresh(700);
      });
    }

    window.updateEta = function(orderCode, eta, el){
      const card = el?.closest('.order-item');
      const msgEl = card ? card.querySelector('.save-msg') : null;
      const status = card?.getAttribute('data-status') || '';
      const oldCol = statusToColumn(status, '');

      const etaVal = (eta === '' ? null : Number(eta));

      return saveOrderUpdate(orderCode, { eta_minutes: etaVal }, msgEl).then(() => {
        if (!card) return;

        const newCol = statusToColumn(status, etaVal);

        if (oldCol !== newCol){
          updateTopStatsDelta(oldCol, newCol);

          if (oldCol) updateMini(oldCol, -1);
          if (newCol) updateMini(newCol, +1);

          const targetBody = getPanelBodyByName(newCol);
          if (targetBody){
            removeEmptyIfAny(targetBody);
            targetBody.prepend(card);
          }
          if (oldCol) ensureEmpty(oldCol);
        }

        scheduleSoftRefresh(700);
      });
    }

    // ===== auto refresh board + stats (partial refresh) =====
    let refreshTimer = null;
    let softTimer = null;
    let isRefreshing = false;

    function scheduleSoftRefresh(delayMs){
      if (softTimer) clearTimeout(softTimer);
      softTimer = setTimeout(refreshBoardAndStats, delayMs);
    }

    function refreshBoardAndStats(){
      if (isRefreshing) return;
      const board = document.getElementById('ordersBoard');
      const stats = document.getElementById('orderStats');
      if (!board && !stats) return;

      isRefreshing = true;

      fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;

          const newBoard = temp.querySelector('#ordersBoard');
          const newStats = temp.querySelector('#orderStats');

          if (board && newBoard) board.replaceWith(newBoard);
          if (stats && newStats) stats.replaceWith(newStats);
        })
        .catch(() => {})
        .finally(() => { isRefreshing = false; });
    }

    function startAutoRefresh(){
      if (!AUTO_REFRESH) return;
      if (refreshTimer) clearInterval(refreshTimer);
      refreshTimer = setInterval(refreshBoardAndStats, REFRESH_MS);
    }

    document.addEventListener('DOMContentLoaded', () => {
      startAutoRefresh();
    });
  </script>
</body>
</html>