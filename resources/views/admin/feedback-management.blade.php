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

/* gray room */
.room{
  background: #f1f1f1;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.06);
  padding: 18px;
  position: relative;
  min-height: 330px;
  overflow: hidden;
}

/* yellow dividers (walls) */
.divider{
  position:absolute;
  width: 6px;
  background: var(--orange);
  border-radius: 6px;
  opacity: .95;
}
.divider.top-mid{ left: 36%; top: 54px; height: 80px; }
.divider.bottom-mid{ left: 36%; top: 210px; height: 140px; }

/* table cards */
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
}

.tcard.unavailable{
  border-color: #ef4444;
}

/* shapes */
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

/* responsive room: scroll if screen is small */
@media (max-width: 980px){
  .room{ overflow:auto; }
  .room-inner{ position: relative; width: 900px; height: 360px; }
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

    <a href="{{ route('admin.feedbacks') }}"
           class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">
          Feedback Management
        </a>

        <a href="#"><span class="dot-icon"></span>Inventory Management</a>
        <a href="#"><span class="dot-icon"></span>Sales and Stock Reports</a>
      </nav>
    </aside>

    <!-- ===== Main content ===== -->
    <main class="content">
      <div class="topbar">
       <div class="title">Feedback Management</div>



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

    
<!-- ===== Feedback Management Layout ===== -->
<section class="tables-panel" aria-label="Feedback management">
  <div class="tables-title">Feedbacks</div>

  {{-- feedback list container (same gray background like your screenshot) --}}
  <div class="room" style="padding: 14px; overflow:auto;">

    {{-- ✅ LOOP: from controller $feedbacks --}}
    @forelse ($feedbacks as $fb)
      <div style="
        background:#fff;
        border: 1.5px solid rgba(245,158,11,.55);
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,.10);
        padding: 14px 16px;
        margin-bottom: 14px;
      ">

        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
          <div>
            <div style="font-weight:900; font-size:14px; margin-bottom:6px;">
              {{ $fb->customer_name ?? 'Customer' }}
            </div>

            <div style="display:flex; gap:18px; flex-wrap:wrap; font-size:12px; opacity:.85; margin-bottom:8px;">
              <div><b>ID:</b> {{ $fb->id }}</div>
              <div><b>{{ \Carbon\Carbon::parse($fb->created_at)->format('Y-m-d') }}</b></div>
            </div>

            {{-- Stars --}}
            @php $rating = (int) ($fb->rating ?? 0); @endphp
            <div style="display:flex; align-items:center; gap:10px; margin: 6px 0 10px;">
              <div style="display:flex; gap:6px;">
                @for($i=1; $i<=5; $i++)
                  @if($i <= $rating)
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#f59e0b">
                      <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24 14.81 8.63 12 2 9.19 8.63 2 9.24 7.46 13.97 5.82 21 12 17.27Z"/>
                    </svg>
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="1.6">
                      <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24 14.81 8.63 12 2 9.19 8.63 2 9.24 7.46 13.97 5.82 21 12 17.27Z"/>
                    </svg>
                  @endif
                @endfor
              </div>

              <div style="font-weight:900; min-width:40px;">
                {{ number_format((float)($fb->rating ?? 0), 1) }}
              </div>
            </div>
          </div>

          {{-- Badges + button --}}
          <div style="display:flex; align-items:flex-start; gap:10px; flex-wrap:wrap; margin-left:auto;">
            <span class="chip" style="background:#fff; border:1px solid rgba(245,158,11,.6);">
              TABLE# {{ $fb->table_number ?? '-' }}
            </span>

            @if(($fb->is_reviewed ?? 0) == 1)
              <span class="chip">Reviewed</span>
            @else
              <span class="chip" style="background:#fff; border:1px solid rgba(245,158,11,.6);">
                Pending
              </span>
            @endif

            {{-- Mark reviewed --}}
            @if(($fb->is_reviewed ?? 0) == 0)
              <form method="POST" action="{{ route('admin.feedback.review', $fb->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="logout-btn" style="border-radius:999px; padding:8px 14px;">
                  Mark Reviewed
                </button>
              </form>
            @else
              <button type="button" class="logout-btn" style="border-radius:999px; padding:8px 14px; opacity:.6; cursor:not-allowed;" disabled>
                Mark Reviewed
              </button>
            @endif
          </div>
        </div>

        {{-- Comment box --}}
        <div style="
          margin-top: 10px;
          border: 1px solid rgba(245,158,11,.45);
          border-radius: 14px;
          padding: 12px 14px;
          background: #fff;
          box-shadow: 0 3px 10px rgba(0,0,0,.08);
          font-size: 13px;
        ">
          “{{ $fb->comment ?? 'No comment provided.' }}”
        </div>

      </div>
    @empty
      <div class="empty">No feedbacks yet.</div>
    @endforelse

  </div>
</section>



     
    </main>
  </div>

  <script>
 
// Toggle table status on click (front-end only for now)
document.querySelectorAll('.tcard').forEach(card => {
  card.addEventListener('click', () => {
    const statusEl = card.querySelector('.tstatus');
    const isUnavailable = card.classList.toggle('unavailable');
    statusEl.textContent = isUnavailable ? 'Unavailable' : 'Available';

    // Later: send to backend using fetch() to save in DB
    // console.log('Table', card.dataset.table, 'status:', statusEl.textContent);
  });
});

   </script>
</body>
</html>
