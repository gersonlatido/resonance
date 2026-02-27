<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Options</title>
  <style>
    body{font-family:Arial;background:#f6f6f6;margin:0;padding:24px;}
    .card{max-width:520px;margin:0 auto;background:#fff;border-radius:14px;padding:18px;box-shadow:0 10px 25px rgba(0,0,0,.08);}
    h2{margin:0 0 6px;}
    .row{margin-top:14px;padding:14px;border:1px solid #eee;border-radius:12px;}
    .btn{padding:10px 14px;border:none;border-radius:10px;cursor:pointer;font-weight:800;}
    .btn.primary{background:#f59e0b;}
    .btn.dark{background:#111;color:#fff;}
    .tag{display:inline-block;padding:6px 10px;border-radius:999px;background:#f2f2f2;margin:4px 6px 0 0;font-weight:700;font-size:12px;}
    .ok{color:#16a34a;}
    .bad{color:#ef4444;}
    .list{margin-top:10px}
    .list label{display:flex;align-items:center;gap:10px;margin:8px 0;}
    .error{color:#ef4444;font-weight:800;margin-top:10px;}
  </style>
</head>
<body>
  <div class="card">
    <h2>Table {{ $scanned }} Options</h2>
    <div style="color:#666;font-size:13px;">This table supports sharing. Choose Solo or Shared.</div>

    <div style="margin-top:10px;">
      @foreach($group as $n)
        @php $a = (bool)($availability[$n] ?? true); @endphp
        <span class="tag">Table {{ $n }}:
          <span class="{{ $a ? 'ok' : 'bad' }}">{{ $a ? 'Available' : 'Unavailable' }}</span>
        </span>
      @endforeach
    </div>

    @if(session('error'))
      <div class="error">{{ session('error') }}</div>
    @endif

    <!-- SOLO -->
    <div class="row">
      <h3>Solo</h3>
      <div style="color:#666;font-size:13px;">Solo allowed only if scanned table is available.</div>

      @php $scanAvail = (bool)($availability[$scanned] ?? true); @endphp

      <form method="POST" action="{{ route('table.select') }}">
        @csrf
        <input type="hidden" name="scanned" value="{{ $scanned }}">
        <input type="hidden" name="mode" value="solo">
        <button class="btn primary" type="submit" {{ $scanAvail ? '' : 'disabled' }}>
          Solo Table {{ $scanned }}
        </button>
      </form>
    </div>

    <!-- SHARED -->
    <div class="row">
      <h3>Shared</h3>
      <div style="color:#666;font-size:13px;">Pick one or more available tables in your group to share with.</div>

      <form method="POST" action="{{ route('table.select') }}">
        @csrf
        <input type="hidden" name="scanned" value="{{ $scanned }}">
        <input type="hidden" name="mode" value="shared">

        <div class="list">
          @foreach($group as $n)
            @continue($n === $scanned)
            @php $a = (bool)($availability[$n] ?? true); @endphp

            @if($a)
              <label>
                <input type="checkbox" name="partners[]" value="{{ $n }}">
                Table {{ $n }} (Available)
              </label>
            @endif
          @endforeach
        </div>

        <button class="btn dark" type="submit">Continue Shared</button>
      </form>
    </div>
  </div>
</body>
</html>