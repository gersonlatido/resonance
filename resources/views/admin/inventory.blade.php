<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Inventory Management</title>

  <style>
    :root{
      --panel:#ffffff;
      --sidebar:#a9a9a9;
      --text:#222;
      --muted:#6b7280;
      --orange:#f59e0b;
      --orange-2:#ffb300;
      --card:#fff;
      --border:#e5e7eb;
      --shadow-soft:0 6px 14px rgba(0,0,0,.10);
      --radius:14px;
      --good:#16a34a;
      --warn:#f59e0b;
      --bad:#ef4444;
    }
    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:#ffffff;
      color:var(--text);
    }
    .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:230px 1fr;
    }
    .sidebar{
      background:#e4e3e3;
      padding:18px 14px;
    }
    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      padding:6px 6px 14px;
    }
    .logo-box{
      width:110px;height:54px;
      background:#fff;border-radius:8px;
      display:flex;align-items:center;justify-content:center;
      overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{ width:100%;height:100%;object-fit:contain;padding:6px; }
    .side-section-title{
      font-size:11px;font-weight:700;color:#1f2937;
      margin:14px 6px 8px;text-transform:uppercase;opacity:.85;
    }
    .nav{ display:flex;flex-direction:column;gap:8px;padding:0 6px; }
    .nav a{
      text-decoration:none;font-size:13px;
      padding:9px 10px;border-radius:18px;color:#111;
      display:flex;align-items:center;gap:8px;transition:.15s ease;
      background:rgba(255,255,255,.55);
      border:1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background:rgba(255,184,30,.25); }
    .nav a.active{
      background:var(--orange);color:#111;font-weight:700;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }

    .content{ padding:22px 24px; }
    .topbar{
      display:flex;align-items:center;justify-content:space-between;
      margin-bottom:14px;
    }
    .topbar .title{
      font-size:26px;font-weight:800;color:var(--orange);
      letter-spacing:.2px;
    }
    .logout-btn{
      padding:10px 18px;background:var(--orange);
      border:none;border-radius:12px;cursor:pointer;font-weight:800;
      box-shadow:0 4px 10px rgba(0,0,0,.12);
    }

    /* Header actions */
    .actions{
      display:flex;gap:10px;align-items:center;flex-wrap:wrap;
      margin:10px 0 14px;
    }
    .input, .select{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:10px 12px;
      border-radius:14px;
      font-size:13px;
      font-weight:700;
      outline:none;
      min-width:220px;
    }
    .select{ min-width:160px; }
    .btn{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:10px 14px;
      border-radius:14px;
      cursor:pointer;
      font-weight:900;
      font-size:13px;
      box-shadow:0 6px 14px rgba(0,0,0,.06);
    }
    .btn.primary{ background:var(--orange); }
    .btn:hover{ filter:brightness(.98); }

    /* Stats */
    .stats{
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:14px;
      margin:12px 0 16px;
    }
    .stat{
      background:var(--card);
      border:1px solid #f59e0b;
      border-radius:14px;
      padding:14px;
      box-shadow:var(--shadow-soft);
      display:flex;align-items:center;justify-content:space-between;
      min-height:80px;
    }
    .stat .label{ font-size:12px;color:var(--muted);font-weight:900;margin-bottom:6px; }
    .stat .value{ font-size:22px;font-weight:900;color:#111; }
    .dot{ width:12px;height:12px;border-radius:50%; }
    .dot.good{ background:var(--good); box-shadow:0 0 0 6px rgba(22,163,74,.10); }
    .dot.warn{ background:var(--warn); box-shadow:0 0 0 6px rgba(245,158,11,.12); }
    .dot.bad{ background:var(--bad); box-shadow:0 0 0 6px rgba(239,68,68,.10); }

    .grid{
      display:grid;
      grid-template-columns:1.6fr .9fr;
      gap:14px;
    }
    .panel{
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:14px;
      box-shadow:var(--shadow-soft);
      overflow:hidden;
    }
    .panel-head{
      padding:14px;
      border-bottom:1px solid rgba(0,0,0,.06);
      display:flex;align-items:center;justify-content:space-between;gap:10px;
      background:rgba(245,158,11,.08);
    }
    .panel-head h3{ margin:0;font-size:14px;font-weight:900; }
    .panel-head p{ margin:4px 0 0;font-size:12px;color:var(--muted);font-weight:700; }
    .panel-body{ padding:14px; }

    table{ width:100%;border-collapse:collapse;font-size:13px; }
    th, td{ padding:10px 8px;border-bottom:1px solid rgba(0,0,0,.06);text-align:left; vertical-align:top; }
    th{ color:var(--muted);font-size:12px;font-weight:900; }
    .badge{
      display:inline-flex;align-items:center;
      padding:4px 10px;border-radius:999px;
      border:1px solid rgba(0,0,0,.08);
      font-size:12px;font-weight:900;background:#fff;
      white-space:nowrap;
    }
    .badge.good{ color:var(--good);border-color:rgba(22,163,74,.25); }
    .badge.warn{ color:var(--warn);border-color:rgba(245,158,11,.28); }
    .badge.bad{ color:var(--bad);border-color:rgba(239,68,68,.28); }

    .row-actions{
      display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;
    }
    .mini-input{
      width:90px;padding:7px 9px;border-radius:12px;
      border:1px solid rgba(0,0,0,.12);
      font-size:12px;font-weight:800;outline:none;
    }
    .link-btn{
      padding:7px 10px;border-radius:12px;
      border:1px solid rgba(0,0,0,.10);
      background:#fff;cursor:pointer;
      font-size:12px;font-weight:900;
    }

    .movement{
      border:1px solid rgba(0,0,0,.06);
      border-radius:14px;
      padding:10px;
      display:flex;justify-content:space-between;gap:10px;
      margin-bottom:10px;
    }
    .movement .name{ font-weight:900;font-size:13px; }
    .movement .meta{ font-size:12px;color:var(--muted);font-weight:700;margin-top:3px; }
    .qty{ font-weight:900;white-space:nowrap;font-size:13px; }
    .qty.in{ color:var(--good); }
    .qty.out{ color:var(--bad); }
    .empty{ color:var(--muted);font-weight:800;font-size:13px;text-align:center;padding:18px 0; }

    /* Menu result cards inside table */
    .menu-title{ font-weight:900; }
    .menu-sub{ font-size:12px; color:var(--muted); font-weight:800; margin-top:4px; }
    .ing-list{ margin:8px 0 0; padding-left:18px; }
    .ing-list li{ margin:6px 0; }

    /* Modal */
    .modal-backdrop{
      position:fixed;inset:0;background:rgba(0,0,0,.45);
      display:none;align-items:center;justify-content:center;padding:18px;z-index:9999;
    }
    .modal{
      width:520px;max-width:100%;background:#fff;border-radius:20px;
      border:1px solid rgba(0,0,0,.10);
      box-shadow:0 25px 60px rgba(0,0,0,.25);overflow:hidden;
    }
    .modal-head{
      padding:14px;border-bottom:1px solid rgba(0,0,0,.06);
      display:flex;justify-content:space-between;align-items:center;
      background:rgba(245,158,11,.10);
    }
    .modal-body{ padding:14px; }
    .form-grid{ display:grid;grid-template-columns:1fr 1fr;gap:10px; }
    .form-grid .full{ grid-column:1 / -1; }
    label{ display:block;font-size:12px;font-weight:900;color:var(--muted);margin-bottom:6px; }
    .field{
      width:100%;padding:10px 12px;border-radius:14px;
      border:1px solid rgba(0,0,0,.12);
      font-size:13px;font-weight:700;outline:none;
    }
    .modal-foot{
      padding:14px;border-top:1px solid rgba(0,0,0,.06);
      display:flex;justify-content:flex-end;gap:10px;
    }

    @media (max-width:1100px){
      .grid{ grid-template-columns:1fr; }
      .stats{ grid-template-columns:repeat(2, minmax(0,1fr)); }
    }
    @media (max-width:720px){
      .shell{ grid-template-columns:1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns:1fr; }
    }
  </style>
</head>

<body>
@php
  $mode = $mode ?? 'ingredient';
  $q = $q ?? '';
@endphp

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
      <a href="{{ route('admin.dashboard') ?? '#' }}">Order Management</a>
      <a href="{{ route('admin.table-management') ?? '#' }}">Table Management</a>
     <a href="{{ route('admin.daily-sales-report') }}"
   class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">
  Daily Sales Report
</a>
    </nav>

    <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
    <nav class="nav">
      <a href="{{ route('admin.menu-management') ?? '#' }}">Menu Management</a>
      <a href="{{ route('admin.feedbacks') ?? '#' }}">Feedback Management</a>
      <a href="{{ route('admin.inventory') }}" class="active">Inventory Management</a>
   <a href="{{ route('admin.sales-stock-reports') }}"
   class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">
  Sales and Stock Reports
</a>
    </nav>
  </aside>

  <!-- Content -->
  <main class="content">
    <div class="topbar">
      <div class="title">Inventory Management</div>
      <form id="logout-form" method="POST" action="{{ route('admin.logout') ?? '#' }}">
        @csrf
        <button class="logout-btn" type="submit">Log Out</button>
      </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
      <div style="margin:10px 0; padding:10px 12px; border-radius:14px; background:rgba(22,163,74,.12); border:1px solid rgba(22,163,74,.25); font-weight:800;">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="margin:10px 0; padding:10px 12px; border-radius:14px; background:rgba(239,68,68,.10); border:1px solid rgba(239,68,68,.25); font-weight:800;">
        {{ session('error') }}
      </div>
    @endif
    @if($errors->any())
      <div style="margin:10px 0; padding:10px 12px; border-radius:14px; background:rgba(245,158,11,.10); border:1px solid rgba(245,158,11,.25); font-weight:800;">
        <div style="margin-bottom:6px;">Fix these:</div>
        <ul style="margin:0 0 0 18px;">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <!-- ✅ Server-side search form -->
    <form class="actions" method="GET" action="{{ route('admin.inventory') }}">
      <input id="searchInput" name="q" class="input" type="text"
             placeholder="{{ $mode === 'menu' ? 'Search menu item…' : 'Search ingredient…' }}"
             value="{{ $q }}" />

      <select id="modeSelect" name="mode" class="select">
        <option value="ingredient" {{ $mode === 'ingredient' ? 'selected' : '' }}>Ingredient</option>
        <option value="menu" {{ $mode === 'menu' ? 'selected' : '' }}>Menu Item (show ingredients)</option>
      </select>

      @if($mode === 'ingredient')
        <select id="statusFilter" class="select">
          <option value="all">All Status</option>
          <option value="good">Healthy</option>
          <option value="warn">Low Stock</option>
          <option value="bad">Out of Stock</option>
        </select>

        <button class="btn primary" type="button" id="openModalBtn">+ Add Ingredient</button>
      @endif

      <button class="btn primary" type="submit">Search</button>
    </form>

    <section class="stats">
      <div class="stat">
        <div>
          <div class="label">Total Ingredients</div>
          <div class="value">{{ $total }}</div>
        </div>
        <div class="dot good"></div>
      </div>
      <div class="stat">
        <div>
          <div class="label">Low Stock</div>
          <div class="value">{{ $lowStock }}</div>
        </div>
        <div class="dot warn"></div>
      </div>
      <div class="stat">
        <div>
          <div class="label">Out of Stock</div>
          <div class="value">{{ $outOfStock }}</div>
        </div>
        <div class="dot bad"></div>
      </div>
    </section>

    <section class="grid">
      <!-- Left -->
      <div class="panel">
        <div class="panel-head">
          <div>
            <h3>{{ $mode === 'menu' ? 'Menu Item Results' : 'Low Stock Alerts' }}</h3>
            <p>{{ $mode === 'menu' ? 'Search menu items and view ingredient requirements' : 'Ingredients to restock soon' }}</p>
          </div>
        </div>

        <div class="panel-body">
          @if($mode === 'menu')
            <table>
              <thead>
              <tr>
                <th style="width:28%;">Menu Item</th>
                <th style="width:58%;">Ingredients (per 1 order)</th>
                <th style="width:14%;">Status</th>
              </tr>
              </thead>
              <tbody>
              @forelse($menuItems as $item)
                @php
                  $recipes = $item->recipes ?? collect();
                  $menuOk = true;

                  foreach ($recipes as $r) {
                    $ing = $r->ingredient ?? null;
                    if (!$ing) { $menuOk = false; break; }

                    $need = (float) ($r->qty_needed ?? 0);
                    $stock = (float) ($ing->stock_qty ?? 0);
                    $reorder = (float) ($ing->reorder_level ?? 0);

                    if ($stock <= 0 || $stock <= $reorder || ($need > 0 && $stock < $need)) {
                      $menuOk = false;
                      break;
                    }
                  }
                @endphp

                <tr>
                  <td>
                    <div class="menu-title">{{ $item->name }}</div>
                    <div class="menu-sub">ID: {{ $item->menu_id }}</div>
                  </td>

                  <td>
                    @if($recipes->count())
                      <ul class="ing-list">
                        @foreach($recipes as $r)
                          @php
                            $ing = $r->ingredient ?? null;
                            $ingName = $ing->name ?? 'Unknown';
                            $need = (float) ($r->qty_needed ?? 0);
                            $unit = $r->unit ?? ($ing->unit ?? '');
                            $stock = (float) ($ing->stock_qty ?? 0);
                            $reorder = (float) ($ing->reorder_level ?? 0);
                            $insufficient = ($stock <= 0) || ($stock <= $reorder) || ($need > 0 && $stock < $need);
                            $canMake = $need > 0 ? (int) floor($stock / $need) : 0;
                          @endphp

                          <li>
                            <strong>{{ $ingName }}</strong>
                            — needs {{ number_format($need,2) }} {{ $unit }}
                            <span style="color:var(--muted); font-weight:800;">
                              | stock: {{ number_format($stock,2) }} {{ $ing->unit ?? '' }}
                              | reorder: {{ number_format($reorder,2) }}
                              | can make: {{ $canMake }}
                            </span>
                            @if($insufficient)
                              <span class="badge bad" style="margin-left:8px;">INSUFFICIENT</span>
                            @else
                              <span class="badge good" style="margin-left:8px;">OK</span>
                            @endif
                          </li>
                        @endforeach
                      </ul>
                    @else
                      <div class="empty" style="padding:10px 0;">No recipe found for this item.</div>
                    @endif
                  </td>

                  <td>
                    @if($menuOk)
                      <span class="badge good">AVAILABLE</span>
                    @else
                      <span class="badge bad">UNAVAILABLE</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="3" class="empty">No menu items found.</td></tr>
              @endforelse
              </tbody>
            </table>

          @else
            <!-- ✅ Original low stock alerts table -->
            <table>
              <thead>
              <tr>
                <th>Ingredient</th>
                <th>Stock</th>
                <th>Reorder</th>
                <th>Status</th>
                <th style="text-align:right;">Restock</th>
              </tr>
              </thead>
              <tbody id="lowStockBody">
              @forelse($lowItems as $i)
                @php
                  $status = ($i->stock_qty ?? 0) <= 0 ? 'bad' : ((($i->stock_qty ?? 0) <= ($i->reorder_level ?? 0)) ? 'warn' : 'good');
                @endphp
                <tr data-name="{{ strtolower($i->name) }}" data-status="{{ $status }}">
                  <td><strong>{{ $i->name }}</strong></td>
                  <td>{{ number_format($i->stock_qty,2) }} {{ $i->unit }}</td>
                  <td>{{ number_format($i->reorder_level,2) }} {{ $i->unit }}</td>
                  <td>
                    @if($status==='bad') <span class="badge bad">Out</span> @endif
                    @if($status==='warn') <span class="badge warn">Low</span> @endif
                    @if($status==='good') <span class="badge good">Healthy</span> @endif
                  </td>
                  <td style="text-align:right;">
                    <form method="POST" action="{{ route('admin.inventory.stockin', $i->id) }}" style="display:flex; gap:6px; justify-content:flex-end;">
                      @csrf
                      <input class="mini-input" name="qty" type="number" step="0.01" placeholder="+qty" required />
                      <button class="link-btn" type="submit">Restock</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="empty">No low stock items 🎉</td></tr>
              @endforelse
              </tbody>
            </table>
          @endif
        </div>

        @if($mode === 'ingredient')
          <div class="panel-head" style="border-top:1px solid rgba(0,0,0,.06);">
            <div>
              <h3>All Ingredients</h3>
              <p>Manage stock levels</p>
            </div>
          </div>

          <div class="panel-body">
            <table>
              <thead>
              <tr>
                <th>Ingredient</th>
                <th>Unit</th>
                <th>Stock</th>
                <th>Reorder</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
              </tr>
              </thead>
              <tbody id="allBody">
              @forelse($ingredients as $i)
                @php
                  $status = ($i->stock_qty ?? 0) <= 0 ? 'bad' : ((($i->stock_qty ?? 0) <= ($i->reorder_level ?? 0)) ? 'warn' : 'good');
                @endphp
                <tr data-name="{{ strtolower($i->name) }}" data-status="{{ $status }}">
                  <td><strong>{{ $i->name }}</strong></td>
                  <td>{{ $i->unit }}</td>
                  <td>{{ number_format($i->stock_qty,2) }}</td>
                  <td>{{ number_format($i->reorder_level,2) }}</td>
                  <td>
                    @if($status==='bad') <span class="badge bad">Out</span> @endif
                    @if($status==='warn') <span class="badge warn">Low</span> @endif
                    @if($status==='good') <span class="badge good">Healthy</span> @endif
                  </td>
                  <td style="text-align:right;">
                    <div class="row-actions">
                      <form method="POST" action="{{ route('admin.inventory.stockin', $i->id) }}" style="display:flex; gap:6px; align-items:center;">
                        @csrf
                        <input class="mini-input" name="qty" type="number" step="0.01" placeholder="+qty" required />
                        <button class="link-btn" type="submit">+ Stock</button>
                      </form>

                      <form method="POST" action="{{ route('admin.inventory.stockout', $i->id) }}" style="display:flex; gap:6px; align-items:center;">
                        @csrf
                        <input class="mini-input" name="qty" type="number" step="0.01" placeholder="-qty" required />
                        <button class="link-btn" type="submit">- Stock</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="empty">No ingredients yet.</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
        @endif
      </div>

      <!-- Right -->
      <aside class="panel">
        <div class="panel-head">
          <div>
            <h3>Recent Stock Movements</h3>
            <p>Automatic logs</p>
          </div>
        </div>

        <div class="panel-body">
          @forelse($recentMovements as $m)
            @php $type = $m->type ?? 'adjust'; @endphp
            <div class="movement">
              <div>
                <div class="name">{{ $m->ingredient->name ?? 'Ingredient' }}</div>
                <div class="meta">{{ strtoupper($type) }} • {{ $m->reason ?? '' }} • {{ optional($m->created_at)->format('M d, Y h:i A') }}</div>
              </div>
              <div class="qty {{ $type === 'in' ? 'in' : 'out' }}">
                {{ $type === 'in' ? '+' : '-' }}{{ number_format($m->qty ?? 0,2) }}
              </div>
            </div>
          @empty
            <div class="empty">No movements yet.</div>
          @endforelse
        </div>
      </aside>
    </section>
  </main>
</div>

@if($mode === 'ingredient')
  <!-- Modal -->
  <div class="modal-backdrop" id="modalBackdrop">
    <div class="modal">
      <div class="modal-head">
        <strong>Add Ingredient</strong>
        <button class="btn" id="closeModalBtn" type="button">Close</button>
      </div>

      <div class="modal-body">
        <form method="POST" action="{{ route('admin.inventory.ingredients.store') }}">
          @csrf
          <div class="form-grid">
            <div class="full">
              <label>Ingredient Name</label>
              <input class="field" name="name" type="text" placeholder="e.g., Garlic" required />
            </div>
            <div>
              <label>Unit</label>
              <select class="field" name="unit" required>
                <option value="g">g</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
              </select>
            </div>
            <div>
              <label>Starting Stock</label>
              <input class="field" name="stock_qty" type="number" step="0.01" required />
            </div>
            <div>
              <label>Reorder Level</label>
              <input class="field" name="reorder_level" type="number" step="0.01" required />
            </div>
          </div>

          <div class="modal-foot">
            <button class="btn" type="button" id="closeModalBtn2">Cancel</button>
            <button class="btn primary" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

<script>
  // modal (only exists in ingredient mode)
  const modal = document.getElementById("modalBackdrop");
  const openBtn = document.getElementById("openModalBtn");
  const closeBtn = document.getElementById("closeModalBtn");
  const closeBtn2 = document.getElementById("closeModalBtn2");

  if (modal && openBtn && closeBtn && closeBtn2) {
    openBtn.addEventListener("click", () => modal.style.display = "flex");
    closeBtn.addEventListener("click", () => modal.style.display = "none");
    closeBtn2.addEventListener("click", () => modal.style.display = "none");
    modal.addEventListener("click", (e) => { if(e.target === modal) modal.style.display = "none"; });
  }

  // Front-end filter only for ingredient mode tables
  const statusFilter = document.getElementById('statusFilter');
  const searchInput = document.getElementById('searchInput');

  function applyFilters(){
    if (!statusFilter) return; // menu mode -> no filter
    const q = (searchInput.value || '').trim().toLowerCase();
    const f = statusFilter.value;
    const rows = document.querySelectorAll('#allBody tr, #lowStockBody tr');
    rows.forEach(row=>{
      const name = row.getAttribute('data-name') || '';
      const status = row.getAttribute('data-status') || 'good';
      const okSearch = name.includes(q);
      const okFilter = (f==='all') ? true : (status===f);
      row.style.display = (okSearch && okFilter) ? '' : 'none';
    });
  }
  if (statusFilter && searchInput) {
    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
  }
</script>
</body>
</html>