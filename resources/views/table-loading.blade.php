<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Loading…</title>

  <style>
    :root{
      --bg1:#ffffff;
      --bg2:#f7f7fb;
      --accent:#f59e0b;
      --text:#111827;
      --muted:#6b7280;
    }
    body{
      margin:0;
      min-height:100vh;
      display:grid;
      place-items:center;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
      background:
        radial-gradient(1200px 520px at 50% 18%, rgba(245,158,11,.20), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
      overflow:hidden;
    }
    .card{
      width:min(88vw, 380px);
      padding:22px 18px 18px;
      border-radius:22px;
      background:rgba(255,255,255,.78);
      border:1px solid rgba(0,0,0,.06);
      box-shadow:0 18px 55px rgba(0,0,0,.12);
      backdrop-filter: blur(10px);
      text-align:center;
    }
    .logo{
      width:78px;height:78px;margin:0 auto 10px;
      border-radius:18px;
      display:grid;place-items:center;
      background:rgba(245,158,11,.12);
      border:1px solid rgba(245,158,11,.25);
    }
    .logo img{width:58px;height:58px;object-fit:contain}
    .title{font-weight:900;font-size:18px;color:var(--text);letter-spacing:.2px}
    .sub{margin-top:6px;font-size:13px;color:var(--muted);line-height:1.45}

    .pill{
      display:inline-flex;
      gap:8px;
      align-items:center;
      margin-top:12px;
      padding:8px 12px;
      border-radius:999px;
      background:rgba(17,24,39,.05);
      border:1px solid rgba(17,24,39,.06);
      font-weight:800;
      font-size:12px;
      color:rgba(17,24,39,.78);
    }
    .dot{
      width:8px;height:8px;border-radius:999px;background:rgba(245,158,11,.95);
      box-shadow:0 0 0 6px rgba(245,158,11,.14);
      animation:pulse 1.2s infinite ease-in-out;
    }
    @keyframes pulse{0%,100%{transform:scale(1);opacity:.75}50%{transform:scale(1.25);opacity:1}}

    .dots{
      display:flex;
      justify-content:center;
      gap:8px;
      margin-top:14px;
    }
    .dots span{
      width:9px;height:9px;border-radius:999px;
      background:rgba(17,24,39,.22);
      animation:bounce 1s infinite ease-in-out;
    }
    .dots span:nth-child(2){animation-delay:.15s}
    .dots span:nth-child(3){animation-delay:.30s}
    @keyframes bounce{0%,100%{transform:translateY(0);opacity:.35}50%{transform:translateY(-7px);opacity:1}}
  </style>
</head>
<body>

  <div class="card">
    <div class="logo">
      <img src="{{ asset('/images/logo-image.png') }}" alt="Logo">
    </div>

    <div class="title">Preparing your table…</div>
    <div class="sub">Please wait while we set up your order experience.</div>

    <div class="pill">
      <span class="dot"></span>
      <span>Table {{ $table }}</span>
    </div>

    <div class="dots" aria-label="Loading">
      <span></span><span></span><span></span>
    </div>
  </div>

  <script>
    (function () {
      const MIN_MS = 4000; //  at least 4 seconds
      const redirectUrl = @json($redirectUrl);

      const start = Date.now();
      window.addEventListener('load', () => {
        const elapsed = Date.now() - start;
        const remaining = Math.max(0, MIN_MS - elapsed);
        setTimeout(() => window.location.href = redirectUrl, remaining);
      });
    })();
  </script>

</body>
</html>