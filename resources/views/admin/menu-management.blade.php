<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu Management</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

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

    /* ===== Modal ===== */
    .modal-backdrop{
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.35);
      display:none;
      align-items:center;
      justify-content:center;
      padding: 16px;
      z-index: 9999;
    }
    .modal{
      width: 520px;
      max-width: 100%;
      background: #fff;
      border-radius: 14px;
      border: 2px solid rgba(245,158,11,.35);
      box-shadow: 0 10px 30px rgba(0,0,0,.2);
      overflow:hidden;
    }
    .modal-head{
      background:#f1f1f1;
      padding: 12px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      font-weight:900;
    }
    .modal-body{
      padding: 14px;
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    .modal-body .full{ grid-column: 1 / -1; }
    .field label{
      display:block;
      font-size: 11px;
      font-weight: 800;
      color:#111;
      margin-bottom: 6px;
    }
    .field input, .field textarea{
      width: 100%;
      padding: 10px 10px;
      border: 1px solid rgba(0,0,0,.14);
      border-radius: 10px;
      outline:none;
      font-size: 13px;
    }
    .field textarea{ min-height: 86px; resize: vertical; }
    .modal-foot{
      padding: 12px 14px;
      display:flex;
      justify-content:flex-end;
      gap: 10px;
      border-top: 1px solid rgba(0,0,0,.08);
    }
    .btn{
      border:none;
      border-radius: 10px;
      padding: 10px 14px;
      font-weight: 900;
      cursor:pointer;
    }
    .btn.secondary{
      background: #e5e7eb;
    }
    .btn.primary{
      background: var(--orange);
    }

    .notice{
      font-size: 12px;
      color: #b91c1c;
      margin-top: 8px;
      display:none;
    }

    @media (max-width: 980px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px){
      .shell{ grid-template-columns: 1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns: 1fr; }
      .modal-body{ grid-template-columns: 1fr; }
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
    <a href="{{ route('admin.feedbacks') }}"
           class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">
          Feedback Management
        </a>

        <a href="#"><span class="dot-icon"></span>Inventory Management</a>
        <a href="#"><span class="dot-icon"></span>Sales and Stock Reports</a>
      </nav>
    </aside>

    <main class="content">
      <div class="topbar">
        <div class="title">Menu Management</div>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <section class="stats">
        <div class="stat">
          <div>
            <div class="label">Menu Items</div>
            <div class="value" id="menuCount">0</div>
          </div>
          <div class="icon">🍽</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Categories</div>
            <div class="value" id="catCount">0</div>
          </div>
          <div class="icon">🏷</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Total Value</div>
            <div class="value" id="totalValue">0</div>
          </div>
          <div class="icon">₱</div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Last Refresh</div>
            <div class="value" id="lastRefresh">—</div>
          </div>
          <div class="icon">⟳</div>
        </div>
      </section>

      <section class="menu-panel">
        <div class="menu-panel-header">
          <div class="h-title">Menu Items</div>
          <button class="btn-add" type="button" id="btnAdd">
            <span>＋</span> Add Item
          </button>
        </div>

        <div class="menu-list" id="menuList">
          <!-- loaded by JS -->
        </div>

        <div class="notice" id="notice"></div>
      </section>

    </main>
  </div>

  <!-- ===== Modal ===== -->
  <div class="modal-backdrop" id="modalBackdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="modal-head">
        <div id="modalTitle">Add Item</div>
        <button class="icon-btn" type="button" id="btnCloseModal" title="Close">✕</button>
      </div>

      <div class="modal-body">
        <div class="field full">
          <label>Menu ID (string, unique)</label>
          <input type="text" id="f_menu_id" placeholder="ex: MENU002" />
        </div>

        <div class="field full">
          <label>Name</label>
          <input type="text" id="f_name" placeholder="ex: BEEFSILOG" />
        </div>

        <div class="field">
          <label>Price</label>
          <input type="number" step="0.01" id="f_price" placeholder="ex: 288" />
        </div>

        <div class="field">
          <label>Category</label>
          <input type="text" id="f_category" placeholder="ex: All-Day Breakfast" />
        </div>

        <div class="field full">
          <label>Image (filename or url)</label>
          <input type="text" id="f_image" placeholder="ex: beefsilog.png" />
        </div>

        <div class="field full">
          <label>Description</label>
          <textarea id="f_description" placeholder="Write description..."></textarea>
        </div>
      </div>

      <div class="modal-foot">
        <button class="btn secondary" type="button" id="btnCancel">Cancel</button>
        <button class="btn primary" type="button" id="btnSave">Save</button>
      </div>
    </div>
  </div>

  <script>
    const API_BASE = '/api/menu'; // your API

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const menuList = document.getElementById('menuList');
    const notice = document.getElementById('notice');

    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalTitle = document.getElementById('modalTitle');
    const btnAdd = document.getElementById('btnAdd');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancel = document.getElementById('btnCancel');
    const btnSave = document.getElementById('btnSave');

    const f_menu_id = document.getElementById('f_menu_id');
    const f_name = document.getElementById('f_name');
    const f_price = document.getElementById('f_price');
    const f_category = document.getElementById('f_category');
    const f_image = document.getElementById('f_image');
    const f_description = document.getElementById('f_description');

    let mode = 'add'; // add | edit
    let editingId = null;

    function showNotice(msg){
      notice.style.display = 'block';
      notice.textContent = msg;
    }
    function hideNotice(){
      notice.style.display = 'none';
      notice.textContent = '';
    }

    function openModalAdd(){
      mode = 'add';
      editingId = null;
      modalTitle.textContent = 'Add Item';
      btnSave.textContent = 'Save';

      f_menu_id.disabled = false;
      f_menu_id.value = '';
      f_name.value = '';
      f_price.value = '';
      f_category.value = '';
      f_image.value = '';
      f_description.value = '';

      modalBackdrop.style.display = 'flex';
      modalBackdrop.setAttribute('aria-hidden', 'false');
      hideNotice();
    }

    function openModalEdit(item){
      mode = 'edit';
      editingId = item.menu_id;
      modalTitle.textContent = 'Edit Item';
      btnSave.textContent = 'Update';

      f_menu_id.value = item.menu_id || '';
      f_menu_id.disabled = true; // do not change id on edit

      f_name.value = item.name || '';
      f_price.value = item.price ?? '';
      f_category.value = item.category || '';
      f_image.value = item.image || '';
      f_description.value = item.description || '';

      modalBackdrop.style.display = 'flex';
      modalBackdrop.setAttribute('aria-hidden', 'false');
      hideNotice();
    }

    function closeModal(){
      modalBackdrop.style.display = 'none';
      modalBackdrop.setAttribute('aria-hidden', 'true');
    }

    btnAdd.addEventListener('click', openModalAdd);
    btnCloseModal.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', (e) => {
      if (e.target === modalBackdrop) closeModal();
    });

    function money(n){
      const num = Number(n || 0);
      return num.toFixed(2);
    }

    function escapeHtml(str){
      return String(str ?? '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    function renderRow(item){
      // IMPORTANT: uses menu_id not id
      const menu_id = item.menu_id ?? item.menuId ?? item.id;

      const div = document.createElement('div');
      div.className = 'menu-row';
      div.dataset.menuId = menu_id;

      const name = escapeHtml(item.name || '');
      const category = escapeHtml(item.category || '');
      const description = escapeHtml(item.description || '');
      const price = money(item.price);

      div.innerHTML = `
        <div class="menu-left">
          <div class="menu-top">
            <div class="menu-name">${name}</div>
            <span class="chip price">₱${price}</span>
            ${category ? `<span class="chip">${category}</span>` : ``}
            <span class="chip">ID: ${escapeHtml(menu_id)}</span>
          </div>

          <div class="desc">${description}</div>
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
      `;

      div.querySelector('.edit').addEventListener('click', () => openModalEdit({
        menu_id: menu_id,
        name: item.name,
        price: item.price,
        category: item.category,
        image: item.image,
        description: item.description,
      }));

      div.querySelector('.delete').addEventListener('click', async () => {
        if (!confirm(`Delete item ${menu_id}?`)) return;
        await deleteItem(menu_id);
      });

      return div;
    }

    function updateStats(items){
      document.getElementById('menuCount').textContent = items.length;

      const cats = new Set();
      let total = 0;

      items.forEach(it => {
        if (it.category) cats.add(it.category);
        total += Number(it.price || 0);
      });

      document.getElementById('catCount').textContent = cats.size;
      document.getElementById('totalValue').textContent = money(total);

      const d = new Date();
      document.getElementById('lastRefresh').textContent = d.toLocaleTimeString();
    }

    async function apiFetch(url, options = {}){
      const headers = options.headers || {};
      headers['Accept'] = 'application/json';
      headers['Content-Type'] = 'application/json';
      headers['X-CSRF-TOKEN'] = csrfToken;

      return fetch(url, {
        ...options,
        headers
      });
    }

    async function loadMenu(){
      hideNotice();
      menuList.innerHTML = '<div style="padding:10px;color:#6b7280;">Loading...</div>';

      try{
        const res = await fetch(API_BASE, { headers: { 'Accept':'application/json' }});
        if (!res.ok){
          throw new Error(`GET ${API_BASE} failed (${res.status})`);
        }
        const items = await res.json();

        menuList.innerHTML = '';
        if (!Array.isArray(items) || items.length === 0){
          menuList.innerHTML = '<div style="padding:10px;color:#6b7280;">No menu items found.</div>';
          updateStats([]);
          return;
        }

        items.forEach(item => menuList.appendChild(renderRow(item)));
        updateStats(items);

      } catch(err){
        menuList.innerHTML = '';
        showNotice('Error loading menu: ' + err.message);
      }
    }

    async function createItem(payload){
      hideNotice();

      const res = await apiFetch(API_BASE, {
        method: 'POST',
        body: JSON.stringify(payload),
      });

      if (!res.ok){
        let msg = `Create failed (${res.status})`;
        try{
          const data = await res.json();
          msg = data.message || JSON.stringify(data);
        }catch(e){}
        throw new Error(msg);
      }
    }

    async function updateItem(menuId, payload){
      hideNotice();

      const res = await apiFetch(`${API_BASE}/${encodeURIComponent(menuId)}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
      });

      if (!res.ok){
        let msg = `Update failed (${res.status})`;
        try{
          const data = await res.json();
          msg = data.message || JSON.stringify(data);
        }catch(e){}
        throw new Error(msg);
      }
    }

    async function deleteItem(menuId){
      hideNotice();

      const res = await apiFetch(`${API_BASE}/${encodeURIComponent(menuId)}`, {
        method: 'DELETE'
      });

      if (!res.ok){
        let msg = `Delete failed (${res.status})`;
        try{
          const data = await res.json();
          msg = data.message || JSON.stringify(data);
        }catch(e){}
        showNotice(msg);
        return;
      }

      await loadMenu();
    }

    function getFormPayload(){
      return {
        menu_id: f_menu_id.value.trim(),
        name: f_name.value.trim(),
        price: Number(f_price.value || 0),
        category: f_category.value.trim(),
        image: f_image.value.trim(),
        description: f_description.value.trim(),
      };
    }

    btnSave.addEventListener('click', async () => {
      try{
        const payload = getFormPayload();

        // basic validation
        if (!payload.menu_id) return showNotice('Menu ID is required.');
        if (!payload.name) return showNotice('Name is required.');
        if (!payload.category) return showNotice('Category is required.');

        if (mode === 'add'){
          await createItem(payload);
        } else {
          // do not send menu_id update when editing
          const { menu_id, ...rest } = payload;
          await updateItem(editingId, rest);
        }

        closeModal();
        await loadMenu();

      } catch(err){
        showNotice(err.message);
      }
    });

    // initial load
    loadMenu();
  </script>
</body>
</html>
