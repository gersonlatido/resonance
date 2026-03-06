<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Unavailable</title>

    <style>
        :root{
            --orange:#f59e0b;
            --text:#222;
            --muted:#6b7280;
            --danger:#ef4444;
            --card:#ffffff;
            --bg:#f8fafc;
            --shadow:0 14px 35px rgba(0,0,0,.10);
            --radius:22px;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
            background:linear-gradient(180deg, #fff8eb 0%, #f8fafc 100%);
            font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color:var(--text);
        }

        .card{
            width:min(100%, 460px);
            background:var(--card);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:28px 24px;
            text-align:center;
            border:1px solid rgba(0,0,0,.06);
        }

        .logo-box{
            width:118px;
            height:62px;
            margin:0 auto 18px;
            background:#fff;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 6px 20px rgba(0,0,0,.08);
            overflow:hidden;
        }

        .logo-box img{
            width:100%;
            height:100%;
            object-fit:contain;
            padding:6px;
        }

        .icon{
            width:74px;
            height:74px;
            margin:0 auto 16px;
            border-radius:999px;
            background:rgba(239,68,68,.10);
            color:var(--danger);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:34px;
            font-weight:900;
            border:1px solid rgba(239,68,68,.18);
        }

        h1{
            margin:0 0 10px;
            font-size:28px;
            line-height:1.1;
            font-weight:900;
        }

        .table-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            background:rgba(245,158,11,.12);
            color:#111;
            border:1px solid rgba(245,158,11,.25);
            font-weight:900;
            margin-bottom:16px;
        }

        p{
            margin:0;
            font-size:15px;
            line-height:1.6;
            color:var(--muted);
            font-weight:700;
        }

        .actions{
            margin-top:22px;
            display:flex;
            flex-direction:column;
            gap:10px;
        }

        .btn{
            text-decoration:none;
            padding:13px 18px;
            border-radius:14px;
            font-weight:900;
            font-size:15px;
            border:1px solid rgba(0,0,0,.10);
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }

        .btn.primary{
            background:var(--orange);
            color:#111;
        }

        .btn.secondary{
            background:#fff;
            color:#111;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-box">
            <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo">
        </div>

        <div class="icon">!</div>

        <h1>Sorry, this table is unavailable</h1>

        <div class="table-badge">Table {{ $table }}</div>

        <p>
            This table is currently unavailable or already in use.
            Please select another table or ask the staff for assistance.
        </p>

        {{-- <div class="actions">
            <a href="{{ url('/') }}" class="btn primary">Go to Home</a>
            <a href="javascript:history.back()" class="btn secondary">Go Back</a>
        </div> --}}
    </div>
</body>
</html>