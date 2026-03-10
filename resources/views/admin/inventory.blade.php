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
      --sidebar:#e7e5e4;
      --text:#1f2937;
      --muted:#6b7280;
      --orange:#f59e0b;
      --orange-2:#ffb300;
      --orange-soft:#fff7ed;
      --card:#ffffff;
      --border:#e5e7eb;
      --shadow-soft:0 10px 30px rgba(0,0,0,.08);
      --shadow-card:0 8px 22px rgba(15,23,42,.06);
      --radius:18px;
      --good:#16a34a;
      --warn:#f59e0b;
      --bad:#ef4444;
      --over:#2563eb;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:linear-gradient(180deg,#fafafa 0%,#ffffff 100%);
      color:var(--text);
    }

    .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:240px 1fr;
    }

    .sidebar{
      background:var(--sidebar);
      padding:18px 14px;
      border-right:1px solid rgba(0,0,0,.04);
    }

    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      padding:6px 6px 16px;
    }

    .logo-box{
      width:118px;
      height:58px;
      background:#fff;
      border-radius:14px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow:0 4px 14px rgba(0,0,0,.08);
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
      color:#374151;
      margin:14px 6px 8px;
      text-transform:uppercase;
      letter-spacing:.4px;
      opacity:.9;
    }

    .nav{
      display:flex;
      flex-direction:column;
      gap:8px;
      padding:0 6px;
    }

    .nav a{
      text-decoration:none;
      font-size:14px;
      padding:11px 13px;
      border-radius:999px;
      color:#111827;
      display:flex;
      align-items:center;
      gap:8px;
      transition:.15s ease;
      background:rgba(255,255,255,.65);
      border:1px solid rgba(0,0,0,.04);
      font-weight:600;
    }

    .nav a:hover{
      background:rgba(255,184,30,.22);
      transform:translateY(-1px);
    }

    .nav a.active{
      background:var(--orange);
      color:#111;
      font-weight:800;
      box-shadow:0 6px 16px rgba(245,158,11,.28);
    }

    .content{
      padding:24px 22px;
      min-width:0;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      margin-bottom:16px;
    }

    .topbar .title{
      font-size:30px;
      font-weight:900;
      color:var(--orange);
      letter-spacing:.2px;
    }

    .logout-btn{
      padding:11px 18px;
      background:var(--orange);
      border:none;
      border-radius:14px;
      cursor:pointer;
      font-weight:900;
      font-size:14px;
      box-shadow:0 8px 18px rgba(245,158,11,.22);
    }

    .actions{
      display:flex;
      gap:10px;
      align-items:center;
      flex-wrap:wrap;
      margin:12px 0 16px;
      background:#fff;
      border:1px solid rgba(0,0,0,.05);
      border-radius:18px;
      padding:12px;
      box-shadow:var(--shadow-card);
    }

    .input, .select{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:11px 13px;
      border-radius:14px;
      font-size:13px;
      font-weight:700;
      outline:none;
      min-width:220px;
      color:#111827;
    }

    .input:focus,
    .select:focus,
    .field:focus,
    .mini-input:focus,
    .date-input:focus{
      border-color:rgba(245,158,11,.65);
      box-shadow:0 0 0 4px rgba(245,158,11,.12);
    }

    .select{ min-width:170px; }

    .btn{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:11px 15px;
      border-radius:14px;
      cursor:pointer;
      font-weight:900;
      font-size:13px;
      box-shadow:0 6px 14px rgba(0,0,0,.05);
      color:#111827;
    }

    .btn.primary{
      background:var(--orange);
      border-color:rgba(245,158,11,.45);
    }

    .btn:hover{ filter:brightness(.98); }

    .stats{
      display:grid;
      grid-template-columns:repeat(4, minmax(0,1fr));
      gap:14px;
      margin:12px 0 18px;
    }

    .stat{
      background:var(--card);
      border:1px solid rgba(245,158,11,.35);
      border-radius:18px;
      padding:16px;
      box-shadow:var(--shadow-card);
      display:flex;
      align-items:center;
      justify-content:space-between;
      min-height:92px;
      min-width:0;
    }

    .stat .label{
      font-size:12px;
      color:var(--muted);
      font-weight:900;
      margin-bottom:7px;
      letter-spacing:.2px;
    }

    .stat .value{
      font-size:25px;
      font-weight:900;
      color:#111827;
      line-height:1;
    }

    .dot{
      width:13px;
      height:13px;
      border-radius:50%;
      flex:0 0 auto;
    }

    .dot.good{ background:var(--good); box-shadow:0 0 0 8px rgba(22,163,74,.10); }
    .dot.warn{ background:var(--warn); box-shadow:0 0 0 8px rgba(245,158,11,.12); }
    .dot.bad{ background:var(--bad); box-shadow:0 0 0 8px rgba(239,68,68,.10); }
    .dot.over{ background:var(--over); box-shadow:0 0 0 8px rgba(37,99,235,.10); }

    .grid{
      display:grid;
      grid-template-columns:minmax(0,2.1fr) minmax(260px,.72fr);
      gap:14px;
      align-items:start;
    }

    .panel{
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:18px;
      box-shadow:var(--shadow-card);
      overflow:hidden;
      min-width:0;
    }

    .panel-head{
      padding:16px;
      border-bottom:1px solid rgba(0,0,0,.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      background:linear-gradient(180deg,rgba(245,158,11,.10),rgba(245,158,11,.05));
    }

    .panel-head h3{
      margin:0;
      font-size:15px;
      font-weight:900;
      color:#111827;
    }

    .panel-head p{
      margin:4px 0 0;
      font-size:12px;
      color:var(--muted);
      font-weight:700;
    }

    .panel-body{
      padding:14px;
      overflow-x:hidden;
    }

    table{
      width:100%;
      border-collapse:collapse;
      font-size:13px;
      table-layout:fixed;
    }

    th, td{
      padding:11px 8px;
      border-bottom:1px solid rgba(0,0,0,.06);
      text-align:left;
      vertical-align:middle;
    }

    th{
      color:var(--muted);
      font-size:12px;
      font-weight:900;
      background:#fff;
      white-space:normal;
      line-height:1.3;
    }

    td{
      overflow-wrap:break-word;
      word-break:break-word;
      line-height:1.35;
    }

    tbody tr:hover{
      background:#fffaf0;
    }

    .ingredient-name,
    .menu-title,
    .ingredient-cell strong{
      display:block;
      font-weight:800;
      line-height:1.35;
      word-break:break-word;
      overflow-wrap:break-word;
    }

    .ingredient-col{
      width:20%;
    }

    .unit-col{
      width:8%;
    }

    .stock-col,
    .reorder-col,
    .overstock-col{
      width:12%;
    }

    .status-col{
      width:12%;
    }

    .actions-col{
      width:24%;
    }

    .ingredient-cell{
      min-width:0;
    }

    .num-cell{
      white-space:normal;
      text-align:left;
    }

    .unit-cell{
      white-space:nowrap;
    }

    .status-cell{
      white-space:nowrap;
    }

    .badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:5px 10px;
      border-radius:999px;
      border:1px solid rgba(0,0,0,.08);
      font-size:10px;
      font-weight:900;
      background:#fff;
      white-space:nowrap;
      position:static;
      max-width:100%;
    }

    .badge.good{
      color:var(--good);
      border-color:rgba(22,163,74,.25);
      background:rgba(22,163,74,.04);
    }

    .badge.warn{
      color:var(--warn);
      border-color:rgba(245,158,11,.28);
      background:rgba(245,158,11,.05);
    }

    .badge.bad{
      color:var(--bad);
      border-color:rgba(239,68,68,.28);
      background:rgba(239,68,68,.04);
    }

    .badge.over{
      color:var(--over);
      border-color:rgba(37,99,235,.28);
      background:rgba(37,99,235,.04);
    }

    .col-action{
      width:auto;
      min-width:0;
    }

    .row-actions{
      display:flex;
      flex-direction:column;
      gap:6px;
      width:100%;
      max-width:145px;
      margin-left:auto;
    }

    .inline-stock-form{
      display:grid;
      grid-template-columns:1fr 66px;
      gap:6px;
      width:100%;
    }

    .mini-input{
      width:100%;
      min-width:0;
      padding:6px 6px;
      border-radius:10px;
      border:1px solid rgba(0,0,0,.12);
      font-size:11px;
      font-weight:800;
      text-align:center;
      outline:none;
      background:#fff;
    }

    .link-btn{
      width:100%;
      padding:6px 6px;
      border-radius:10px;
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      cursor:pointer;
      font-size:11px;
      font-weight:900;
      white-space:nowrap;
      color:#111827;
    }

    .edit-btn{
      width:100%;
      padding:7px 8px;
      border-radius:10px;
      border:1px solid rgba(245,158,11,.30);
      background:rgba(245,158,11,.12);
      cursor:pointer;
      font-size:11px;
      font-weight:900;
      text-align:center;
      color:#111827;
    }

    .movement{
      border:1px solid rgba(0,0,0,.06);
      border-radius:16px;
      padding:12px;
      display:flex;
      justify-content:space-between;
      gap:12px;
      margin-bottom:10px;
      background:linear-gradient(180deg,#fff,#fffdfa);
    }

    .movement .name{
      font-weight:900;
      font-size:13px;
      color:#111827;
      line-height:1.35;
      word-break:break-word;
    }

    .movement .meta{
      font-size:12px;
      color:var(--muted);
      font-weight:700;
      margin-top:4px;
      line-height:1.35;
      word-break:break-word;
    }

    .qty{
      font-weight:900;
      white-space:nowrap;
      font-size:13px;
      align-self:flex-start;
    }

    .qty.in{ color:var(--good); }
    .qty.out{ color:var(--bad); }

    .empty{
      color:var(--muted);
      font-weight:800;
      font-size:13px;
      text-align:center;
      padding:22px 0;
    }

    .menu-title{
      font-weight:900;
      color:#111827;
    }

    .menu-sub{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
      margin-top:4px;
      word-break:break-word;
    }

    .ing-list{
      margin:8px 0 0;
      padding-left:18px;
    }

    .ing-list li{
      margin:7px 0;
      line-height:1.5;
      word-break:break-word;
    }

    .pager-wrap{
      padding:12px 16px;
      border-top:1px solid rgba(0,0,0,.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
      background:#fff;
    }

    .pager-info{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
    }

    .pager{
      display:flex;
      align-items:center;
      gap:6px;
      flex-wrap:wrap;
    }

    .page-btn{
      min-width:36px;
      height:36px;
      padding:0 11px;
      border-radius:12px;
      border:1px solid rgba(0,0,0,.12);
      background:#fff;
      cursor:pointer;
      font-weight:900;
      font-size:12px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      text-decoration:none;
      color:#111;
    }

    .page-btn:hover{ filter:brightness(.98); }

    .page-btn.active{
      background:var(--orange);
      border-color:rgba(245,158,11,.45);
    }

    .page-btn.disabled{
      opacity:.45;
      pointer-events:none;
    }

    .mini-filter{
      display:flex;
      gap:8px;
      align-items:center;
      flex-wrap:wrap;
      margin-top:8px;
    }

    .date-input{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:10px 11px;
      border-radius:12px;
      font-size:12px;
      font-weight:800;
      outline:none;
      max-width:100%;
    }

    .tiny-btn{
      border:1px solid rgba(0,0,0,.10);
      background:#fff;
      padding:10px 12px;
      border-radius:12px;
      cursor:pointer;
      font-weight:900;
      font-size:12px;
      box-shadow:0 6px 14px rgba(0,0,0,.05);
      text-decoration:none;
      color:#111;
      display:inline-flex;
      align-items:center;
      justify-content:center;
    }

    .tiny-btn.primary{ background:var(--orange); }

    .modal-backdrop{
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.45);
      display:none;
      align-items:center;
      justify-content:center;
      padding:18px;
      z-index:9999;
      backdrop-filter:blur(2px);
    }

    .modal{
      width:560px;
      max-width:100%;
      background:#fff;
      border-radius:22px;
      border:1px solid rgba(0,0,0,.10);
      box-shadow:0 30px 70px rgba(0,0,0,.22);
      overflow:hidden;
    }

    .modal-head{
      padding:16px;
      border-bottom:1px solid rgba(0,0,0,.06);
      display:flex;
      justify-content:space-between;
      align-items:center;
      background:linear-gradient(180deg,rgba(245,158,11,.12),rgba(245,158,11,.05));
      font-weight:900;
    }

    .modal-body{ padding:16px; }

    .form-grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:12px;
    }

    .form-grid .full{ grid-column:1 / -1; }

    label{
      display:block;
      font-size:12px;
      font-weight:900;
      color:var(--muted);
      margin-bottom:6px;
    }

    .field{
      width:100%;
      padding:11px 12px;
      border-radius:14px;
      border:1px solid rgba(0,0,0,.12);
      font-size:13px;
      font-weight:700;
      outline:none;
      background:#fff;
    }

    .modal-foot{
      padding:16px;
      border-top:1px solid rgba(0,0,0,.06);
      display:flex;
      justify-content:flex-end;
      gap:10px;
    }

    @media (max-width:1280px){
      .grid{
        grid-template-columns:minmax(0,1.8fr) minmax(240px,.7fr);
      }
    }

    @media (max-width:1200px){
      .grid{ grid-template-columns:1fr; }
      .panel-body{ overflow-x:auto; }
      table{ min-width:760px; }
    }

    @media (max-width:1100px){
      .stats{ grid-template-columns:repeat(2, minmax(0,1fr)); }
    }

    @media (max-width:820px){
      .content{ padding:18px 14px; }
      .actions{ padding:10px; }
      .input, .select{ min-width:100%; }
      .stats{ grid-template-columns:1fr; }
      .form-grid{ grid-template-columns:1fr; }

      table{
        font-size:12px;
      }

      th, td{
        padding:10px 8px;
      }

      .row-actions{
        max-width:135px;
      }

      .link-btn,
      .edit-btn{
        font-size:11px;
      }

      .panel-body{
        padding:12px;
      }
    }

    @media (max-width:720px){
      .shell{ grid-template-columns:1fr; }
      .sidebar{ display:none; }
      .pager-wrap{ justify-content:center; }
      .pager-info{ width:100%; text-align:center; }
      .topbar{ align-items:flex-start; }
      .topbar .title{ font-size:26px; }
    }
  </style>
</head>

<body>
@php
  $mode = $mode ?? 'ingredient';
  $q = $q ?? '';
  $status = $status ?? '';
  $movementDate = $movementDate ?? '';
@endphp

<div class="shell">
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
        Sales Report
      </a>
    </nav>

    <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
    <nav class="nav">
      <a href="{{ route('admin.menu-management') ?? '#' }}">Menu Management</a>
      <a href="{{ route('admin.feedbacks') ?? '#' }}">Feedback Management</a>
      <a href="{{ route('admin.inventory') }}" class="active">Inventory Management</a>
      <a href="{{ route('admin.sales-stock-reports') }}"
         class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">
        Stock Reports
      </a>
    </nav>
  </aside>

  <main class="content">
    <div class="topbar">
      <div class="title">Inventory Management</div>
      <form id="logout-form" method="POST" action="{{ route('admin.logout') ?? '#' }}">
        @csrf
        <button class="logout-btn" type="submit">Log Out</button>
      </form>
    </div>

    @if(session('success'))
      <div style="margin:10px 0; padding:12px 14px; border-radius:16px; background:rgba(22,163,74,.12); border:1px solid rgba(22,163,74,.25); font-weight:800;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="margin:10px 0; padding:12px 14px; border-radius:16px; background:rgba(239,68,68,.10); border:1px solid rgba(239,68,68,.25); font-weight:800;">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div style="margin:10px 0; padding:12px 14px; border-radius:16px; background:rgba(245,158,11,.10); border:1px solid rgba(245,158,11,.25); font-weight:800;">
        <div style="margin-bottom:6px;">Fix these:</div>
        <ul style="margin:0 0 0 18px;">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form class="actions" method="GET" action="{{ route('admin.inventory') }}">
      <input id="searchInput" name="q" class="input" type="text"
             placeholder="{{ $mode === 'menu' ? 'Search menu item…' : 'Search ingredient…' }}"
             value="{{ $q }}" />

      <select id="modeSelect" name="mode" class="select">
        <option value="ingredient" {{ $mode === 'ingredient' ? 'selected' : '' }}>Ingredient</option>
        <option value="menu" {{ $mode === 'menu' ? 'selected' : '' }}>Menu Item (show ingredients)</option>
      </select>

      <input type="hidden" name="movement_date" value="{{ $movementDate }}">

      @if($mode === 'ingredient')
        <select id="statusFilter" name="status" class="select">
          <option value="" {{ $status === '' ? 'selected' : '' }}>All Status</option>
          <option value="healthy" {{ $status === 'healthy' ? 'selected' : '' }}>Healthy</option>
          <option value="low" {{ $status === 'low' ? 'selected' : '' }}>Low Stock</option>
          <option value="out" {{ $status === 'out' ? 'selected' : '' }}>Out of Stock</option>
          <option value="overstock" {{ $status === 'overstock' ? 'selected' : '' }}>Overstock</option>
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

      <div class="stat">
        <div>
          <div class="label">Overstock</div>
          <div class="value">{{ $overstock ?? 0 }}</div>
        </div>
        <div class="dot over"></div>
      </div>
    </section>

    <section class="grid">
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
  $menuLowStock = false;
  $menuCanMake = null;
  $canMakeList = [];

  foreach ($recipes as $r) {
    $ing = $r->ingredient ?? null;

    if (!$ing) {
      $menuOk = false;
      $canMakeList[] = 0;
      continue;
    }

    $need = (float) ($r->qty_needed ?? 0);
    $stock = (float) ($ing->stock_qty ?? 0);
    $reorder = (float) ($ing->reorder_level ?? 0);

    if ($need > 0) {
      $canMakeList[] = (int) floor($stock / $need);
    }

    if ($stock <= 0 || ($need > 0 && $stock < $need)) {
      $menuOk = false;
    }

    if ($stock > 0 && $stock <= $reorder) {
      $menuLowStock = true;
    }
  }

  $menuCanMake = count($canMakeList) ? min($canMakeList) : 0;
@endphp

                <tr>
                  <td class="ingredient-cell">
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
  $canMake = $need > 0 ? (int) floor($stock / $need) : 0;

  $hardInsufficient = ($stock <= 0) || ($need > 0 && $stock < $need);
  $lowWarning = ($stock > 0 && $stock <= $reorder);
@endphp
                          @endphp

                          <li>
                            <strong>{{ $ingName }}</strong>
                            — needs {{ number_format($need,2) }} {{ $unit }}
                            <span style="color:var(--muted); font-weight:800;">
                              | stock: {{ number_format($stock,2) }} {{ $ing->unit ?? '' }}
                              | reorder: {{ number_format($reorder,2) }}
                              | can make: {{ $canMake }}
                            </span>
                           @if($hardInsufficient)
  <span class="badge bad" style="margin-left:8px;">INSUFFICIENT</span>
@elseif($lowWarning)
  <span class="badge warn" style="margin-left:8px;">LOW STOCK</span>
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

                 <td class="status-cell">
  @if($menuOk)
    @if($menuLowStock)
      <span class="badge warn">AVAILABLE • </br> LOW STOCK</span>
    @else
      <span class="badge good">AVAILABLE</span>
    @endif

    <div style="margin-top:6px; font-size:12px; font-weight:800; color:var(--muted);">
      Can make: {{ $menuCanMake }}
    </div>
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
            <table>
              <thead>
              <tr>
                <th class="ingredient-col">Ingredient</th>
                <th class="stock-col">Stock</th>
                <th class="reorder-col">Reorder</th>
                <th class="overstock-col">Overstock</th>
                <th class="status-col">Status</th>
                <th class="actions-col" style="text-align:right;">Restock</th>
              </tr>
              </thead>
              <tbody id="lowStockBody">
              @forelse($lowItems as $i)
                @php
                  $hasOverstockLevel = ($i->overstock_level ?? 0) > 0;
                  $isOverstock = $hasOverstockLevel && (($i->stock_qty ?? 0) > ($i->overstock_level ?? 0));
                  $statusKey = ($i->stock_qty ?? 0) <= 0 ? 'bad' : ((($i->stock_qty ?? 0) <= ($i->reorder_level ?? 0)) ? 'warn' : ($isOverstock ? 'over' : 'good'));
                @endphp
                <tr data-name="{{ strtolower($i->name) }}" data-status="{{ $statusKey }}">
                  <td class="ingredient-cell"><strong class="ingredient-name">{{ $i->name }}</strong></td>
                  <td class="num-cell">{{ number_format($i->stock_qty,2) }} {{ $i->unit }}</td>
                  <td class="num-cell">{{ number_format($i->reorder_level,2) }} {{ $i->unit }}</td>
                  <td class="num-cell">{{ number_format($i->overstock_level ?? 0,2) }} {{ $i->unit }}</td>
                  <td class="status-cell">
                    @if($statusKey === 'bad') <span class="badge bad">Out</span> @endif
                    @if($statusKey === 'warn') <span class="badge warn">Low</span> @endif
                    @if($statusKey === 'good') <span class="badge good">Healthy</span> @endif
                    @if($statusKey === 'over') <span class="badge over">Overstock</span> @endif
                  </td>
                  <td style="text-align:right;">
                    <form method="POST" action="{{ route('admin.inventory.stockin', $i->id) }}" class="inline-stock-form" style="max-width:138px; margin-left:auto;">
                      @csrf
                      <input class="mini-input" name="qty" type="number" step="0.01" placeholder="+qty" required />
                      <button class="link-btn" type="submit">Restock</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="empty">No low stock items 🎉</td></tr>
              @endforelse
              </tbody>
            </table>
          @endif
        </div>

        @if($mode === 'menu' && $menuItems instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div class="pager-wrap">
            <div class="pager-info">
              Showing {{ $menuItems->firstItem() ?? 0 }} - {{ $menuItems->lastItem() ?? 0 }} of {{ $menuItems->total() }} menu items
            </div>
            <div class="pager">
              <a class="page-btn {{ $menuItems->onFirstPage() ? 'disabled' : '' }}" href="{{ $menuItems->previousPageUrl() ?? '#' }}">Prev</a>
              @php $start = max(1, $menuItems->currentPage() - 2); $end = min($menuItems->lastPage(), $menuItems->currentPage() + 2); @endphp
              @if($start > 1)
                <a class="page-btn" href="{{ $menuItems->url(1) }}">1</a>
                @if($start > 2)<span class="page-btn disabled">…</span>@endif
              @endif
              @for($p=$start; $p<=$end; $p++)
                <a class="page-btn {{ $p === $menuItems->currentPage() ? 'active' : '' }}" href="{{ $menuItems->url($p) }}">{{ $p }}</a>
              @endfor
              @if($end < $menuItems->lastPage())
                @if($end < $menuItems->lastPage()-1)<span class="page-btn disabled">…</span>@endif
                <a class="page-btn" href="{{ $menuItems->url($menuItems->lastPage()) }}">{{ $menuItems->lastPage() }}</a>
              @endif
              <a class="page-btn {{ $menuItems->hasMorePages() ? '' : 'disabled' }}" href="{{ $menuItems->nextPageUrl() ?? '#' }}">Next</a>
            </div>
          </div>
        @endif

        @if($mode === 'ingredient' && $lowItems instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div class="pager-wrap">
            <div class="pager-info">
              Showing {{ $lowItems->firstItem() ?? 0 }} - {{ $lowItems->lastItem() ?? 0 }} of {{ $lowItems->total() }} low stock items
            </div>
            <div class="pager">
              <a class="page-btn {{ $lowItems->onFirstPage() ? 'disabled' : '' }}" href="{{ $lowItems->previousPageUrl() ?? '#' }}">Prev</a>
              @php $start = max(1, $lowItems->currentPage() - 2); $end = min($lowItems->lastPage(), $lowItems->currentPage() + 2); @endphp
              @if($start > 1)
                <a class="page-btn" href="{{ $lowItems->url(1) }}">1</a>
                @if($start > 2)<span class="page-btn disabled">…</span>@endif
              @endif
              @for($p=$start; $p<=$end; $p++)
                <a class="page-btn {{ $p === $lowItems->currentPage() ? 'active' : '' }}" href="{{ $lowItems->url($p) }}">{{ $p }}</a>
              @endfor
              @if($end < $lowItems->lastPage())
                @if($end < $lowItems->lastPage()-1)<span class="page-btn disabled">…</span>@endif
                <a class="page-btn" href="{{ $lowItems->url($lowItems->lastPage()) }}">{{ $lowItems->lastPage() }}</a>
              @endif
              <a class="page-btn {{ $lowItems->hasMorePages() ? '' : 'disabled' }}" href="{{ $lowItems->nextPageUrl() ?? '#' }}">Next</a>
            </div>
          </div>
        @endif

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
                <th class="ingredient-col">Ingredient</th>
                <th class="unit-col">Unit</th>
                <th class="stock-col">Stock</th>
                <th class="reorder-col">Reorder</th>
                <th class="overstock-col">Overstock</th>
                <th class="status-col">Status</th>
                <th class="actions-col" style="text-align:center;">Actions</th>
              </tr>
              </thead>
              <tbody id="allBody">
              @forelse($ingredients as $i)
                @php
                  $hasOverstockLevel = ($i->overstock_level ?? 0) > 0;
                  $isOverstock = $hasOverstockLevel && (($i->stock_qty ?? 0) > ($i->overstock_level ?? 0));
                  $statusKey = ($i->stock_qty ?? 0) <= 0 ? 'bad' : ((($i->stock_qty ?? 0) <= ($i->reorder_level ?? 0)) ? 'warn' : ($isOverstock ? 'over' : 'good'));
                @endphp
                <tr data-name="{{ strtolower($i->name) }}" data-status="{{ $statusKey }}">
                  <td class="ingredient-cell"><strong class="ingredient-name">{{ $i->name }}</strong></td>
                  <td class="unit-cell">{{ $i->unit }}</td>
                  <td class="num-cell">{{ number_format($i->stock_qty,2) }}</td>
                  <td class="num-cell">{{ number_format($i->reorder_level,2) }}</td>
                  <td class="num-cell">{{ number_format($i->overstock_level ?? 0,2) }}</td>
                  <td class="status-cell">
                    @if($statusKey === 'bad') <span class="badge bad">Out</span> @endif
                    @if($statusKey === 'warn') <span class="badge warn">Low</span> @endif
                    @if($statusKey === 'good') <span class="badge good">Healthy</span> @endif
                    @if($statusKey === 'over') <span class="badge over">Overstock</span> @endif
                  </td>
                  <td class="col-action" style="text-align:center;">
                    <div class="row-actions">
                      <button
                        type="button"
                        class="edit-btn editIngredientBtn"
                        data-id="{{ $i->id }}"
                        data-name="{{ $i->name }}"
                        data-unit="{{ $i->unit }}"
                        data-reorder="{{ $i->reorder_level }}"
                        data-overstock="{{ $i->overstock_level ?? 0 }}"
                      >
                        Edit Ingredient
                      </button>

                      <form method="POST" action="{{ route('admin.inventory.stockin', $i->id) }}" class="inline-stock-form">
                        @csrf
                        <input class="mini-input" name="qty" type="number" step="0.01" placeholder="+qty" required />
                        <button class="link-btn" type="submit">+ Stock</button>
                      </form>

                      <form method="POST" action="{{ route('admin.inventory.stockout', $i->id) }}" class="inline-stock-form">
                        @csrf
                        <input class="mini-input" name="qty" type="number" step="0.01" placeholder="-qty" required />
                        <button class="link-btn" type="submit">- Stock</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="7" class="empty">No ingredients yet.</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>

          @if($ingredients instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="pager-wrap">
              <div class="pager-info">
                Showing {{ $ingredients->firstItem() ?? 0 }} - {{ $ingredients->lastItem() ?? 0 }} of {{ $ingredients->total() }} ingredients
              </div>
              <div class="pager">
                <a class="page-btn {{ $ingredients->onFirstPage() ? 'disabled' : '' }}" href="{{ $ingredients->previousPageUrl() ?? '#' }}">Prev</a>
                @php $start = max(1, $ingredients->currentPage() - 2); $end = min($ingredients->lastPage(), $ingredients->currentPage() + 2); @endphp
                @if($start > 1)
                  <a class="page-btn" href="{{ $ingredients->url(1) }}">1</a>
                  @if($start > 2)<span class="page-btn disabled">…</span>@endif
                @endif
                @for($p=$start; $p<=$end; $p++)
                  <a class="page-btn {{ $p === $ingredients->currentPage() ? 'active' : '' }}" href="{{ $ingredients->url($p) }}">{{ $p }}</a>
                @endfor
                @if($end < $ingredients->lastPage())
                  @if($end < $ingredients->lastPage()-1)<span class="page-btn disabled">…</span>@endif
                  <a class="page-btn" href="{{ $ingredients->url($ingredients->lastPage()) }}">{{ $ingredients->lastPage() }}</a>
                @endif
                <a class="page-btn {{ $ingredients->hasMorePages() ? '' : 'disabled' }}" href="{{ $ingredients->nextPageUrl() ?? '#' }}">Next</a>
              </div>
            </div>
          @endif
        @endif
      </div>

      <aside class="panel">
        <div class="panel-head" style="flex-direction:column; align-items:flex-start;">
          <div style="display:flex; width:100%; align-items:center; justify-content:space-between; gap:10px;">
            <div>
              <h3>Recent Stock Movements</h3>
              <p>Automatic logs</p>
            </div>
          </div>

          <form class="mini-filter" method="GET" action="{{ route('admin.inventory') }}">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <input type="hidden" name="q" value="{{ $q }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="low_page" value="{{ request('low_page') }}">
            <input type="hidden" name="ing_page" value="{{ request('ing_page') }}">
            <input type="hidden" name="menu_page" value="{{ request('menu_page') }}">

            <input class="date-input" type="date" name="movement_date" value="{{ $movementDate }}">

            <button class="tiny-btn primary" type="submit">Filter</button>
            <a class="tiny-btn" href="{{ route('admin.inventory', ['mode' => $mode, 'q' => $q, 'status' => $status]) }}">Clear</a>
          </form>
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
            <div class="empty">
              @if($movementDate)
                No movements for {{ \Carbon\Carbon::parse($movementDate)->format('M d, Y') }}.
              @else
                No movements yet.
              @endif
            </div>
          @endforelse
        </div>

        @if($recentMovements instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div class="pager-wrap">
            <div class="pager-info">
              Showing {{ $recentMovements->firstItem() ?? 0 }} - {{ $recentMovements->lastItem() ?? 0 }} of {{ $recentMovements->total() }} movements
            </div>
            <div class="pager">
              <a class="page-btn {{ $recentMovements->onFirstPage() ? 'disabled' : '' }}" href="{{ $recentMovements->previousPageUrl() ?? '#' }}">Prev</a>
              @php $start = max(1, $recentMovements->currentPage() - 2); $end = min($recentMovements->lastPage(), $recentMovements->currentPage() + 2); @endphp
              @if($start > 1)
                <a class="page-btn" href="{{ $recentMovements->url(1) }}">1</a>
                @if($start > 2)<span class="page-btn disabled">…</span>@endif
              @endif
              @for($p=$start; $p<=$end; $p++)
                <a class="page-btn {{ $p === $recentMovements->currentPage() ? 'active' : '' }}" href="{{ $recentMovements->url($p) }}">{{ $p }}</a>
              @endfor
              @if($end < $recentMovements->lastPage())
                @if($end < $recentMovements->lastPage()-1)<span class="page-btn disabled">…</span>@endif
                <a class="page-btn" href="{{ $recentMovements->url($recentMovements->lastPage()) }}">{{ $recentMovements->lastPage() }}</a>
              @endif
              <a class="page-btn {{ $recentMovements->hasMorePages() ? '' : 'disabled' }}" href="{{ $recentMovements->nextPageUrl() ?? '#' }}">Next</a>
            </div>
          </div>
        @endif
      </aside>
    </section>
  </main>
</div>

@if($mode === 'ingredient')
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
              <input class="field" name="stock_qty" type="number" step="0.01" min="0" required />
            </div>
            <div>
              <label>Reorder Level</label>
              <input class="field" name="reorder_level" type="number" step="0.01" min="0" required />
            </div>
            <div class="full">
              <label>Overstock Level</label>
              <input class="field" name="overstock_level" type="number" step="0.01" min="0" required />
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

  <div class="modal-backdrop" id="editModalBackdrop">
    <div class="modal">
      <div class="modal-head">
        <strong>Edit Ingredient</strong>
        <button class="btn" id="closeEditModalBtn" type="button">Close</button>
      </div>

      <div class="modal-body">
        <form method="POST" id="editIngredientForm">
          @csrf
          @method('PUT')

          <div class="form-grid">
            <div class="full">
              <label>Ingredient Name</label>
              <input class="field" id="edit_name" name="name" type="text" required />
            </div>
            <div>
              <label>Unit</label>
              <select class="field" id="edit_unit" name="unit" required>
                <option value="g">g</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
              </select>
            </div>
            <div>
              <label>Reorder Level</label>
              <input class="field" id="edit_reorder_level" name="reorder_level" type="number" step="0.01" min="0" required />
            </div>
            <div class="full">
              <label>Overstock Level</label>
              <input class="field" id="edit_overstock_level" name="overstock_level" type="number" step="0.01" min="0" required />
            </div>
          </div>

          <div class="modal-foot">
            <button class="btn" type="button" id="closeEditModalBtn2">Cancel</button>
            <button class="btn primary" type="submit">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

<script>
  const modal = document.getElementById("modalBackdrop");
  const openBtn = document.getElementById("openModalBtn");
  const closeBtn = document.getElementById("closeModalBtn");
  const closeBtn2 = document.getElementById("closeModalBtn2");

  const editModal = document.getElementById("editModalBackdrop");
  const closeEditBtn = document.getElementById("closeEditModalBtn");
  const closeEditBtn2 = document.getElementById("closeEditModalBtn2");
  const editForm = document.getElementById("editIngredientForm");
  const modeSelect = document.getElementById("modeSelect");
  const statusFilter = document.getElementById("statusFilter");

  if (modal && openBtn && closeBtn && closeBtn2) {
    openBtn.addEventListener("click", () => modal.style.display = "flex");
    closeBtn.addEventListener("click", () => modal.style.display = "none");
    closeBtn2.addEventListener("click", () => modal.style.display = "none");
    modal.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });
  }

  if (editModal && closeEditBtn && closeEditBtn2) {
    closeEditBtn.addEventListener("click", () => editModal.style.display = "none");
    closeEditBtn2.addEventListener("click", () => editModal.style.display = "none");
    editModal.addEventListener("click", (e) => {
      if (e.target === editModal) editModal.style.display = "none";
    });
  }

  document.querySelectorAll(".editIngredientBtn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      const unit = this.dataset.unit;
      const reorder = this.dataset.reorder;
      const overstock = this.dataset.overstock;

      document.getElementById("edit_name").value = name;
      document.getElementById("edit_unit").value = unit;
      document.getElementById("edit_reorder_level").value = reorder;
      document.getElementById("edit_overstock_level").value = overstock;

      editForm.action = `/admin/inventory/ingredients/${id}`;
      editModal.style.display = "flex";
    });
  });

  if (modeSelect) {
    modeSelect.addEventListener("change", function () {
      this.form.submit();
    });
  }

  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      this.form.submit();
    });
  }
</script>
</body>
</html>