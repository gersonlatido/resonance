<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Options</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body>
  <div id="qrSplash" class="qr-splash">
    <div class="qr-splash-card">
      <img class="qr-splash-logo" src="{{ asset('images/logo.png') }}" alt="99 Silog Cafe">
      <div class="qr-splash-title">Welcome to 99 Silog Café</div>
      <div class="qr-splash-sub">Preparing your table options…</div>
      <div class="qr-splash-dots" aria-hidden="true">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>

  <div class="to-wrap">
    <div class="to-card">
      <div class="to-header">
        <p class="to-title">Table {{ $scanned }} Options</p>
        <p class="to-sub">This table supports sharing. Choose Solo or Shared.</p>

        <div class="to-chips">
          @foreach($group as $n)
            @php $a = (bool)($availability[$n] ?? true); @endphp
            <span class="to-chip">
              <span class="to-dot {{ $a ? '' : 'busy' }}"></span>
              Table {{ $n }}: <b>{{ $a ? 'Available' : 'Unavailable' }}</b>
            </span>
          @endforeach
        </div>

        @if(session('error'))
          <div style="margin-top:10px; font-size:12px; font-weight:900; color:#ef4444;">
            {{ session('error') }}
          </div>
        @endif
      </div>

      <div class="to-body">
        @php $scanAvail = (bool)($availability[$scanned] ?? true); @endphp

        <div class="to-section">
          <h3>
            Solo
            <span class="to-badge">Recommended</span>
          </h3>

          <p class="to-help">Solo allowed only if the scanned table is available.</p>

          <form method="POST" action="{{ route('table.select') }}">
            @csrf
            <input type="hidden" name="scanned" value="{{ $scanned }}">
            <input type="hidden" name="mode" value="solo">

            <button
              class="to-btn primary"
              type="submit"
              {{ $scanAvail ? '' : 'disabled' }}
              style="{{ $scanAvail ? '' : 'opacity:.55; cursor:not-allowed; box-shadow:none;' }}"
            >
              Solo Table {{ $scanned }}
            </button>
          </form>
        </div>

        <div class="to-section">
          <h3>
            Shared
            <span class="to-badge">Group Tables</span>
          </h3>

          <p class="to-help">Pick one or more available tables in your group to share with.</p>

          <form method="POST" action="{{ route('table.select') }}">
            @csrf
            <input type="hidden" name="scanned" value="{{ $scanned }}">
            <input type="hidden" name="mode" value="shared">

            <div class="to-list">
              @php $hasPartners = false; @endphp

              @foreach($group as $n)
                @continue($n === $scanned)
                @php $a = (bool)($availability[$n] ?? true); if($a) $hasPartners = true; @endphp

                <div class="to-check" style="{{ $a ? '' : 'opacity:.55;' }}">
                  <input
                    type="checkbox"
                    name="partners[]"
                    value="{{ $n }}"
                    id="tb{{ $n }}"
                    {{ $a ? '' : 'disabled' }}
                  >
                  <label for="tb{{ $n }}">Table {{ $n }}</label>
                  <span class="to-muted">{{ $a ? 'Available' : 'Unavailable' }}</span>
                </div>
              @endforeach

              @if(!$hasPartners)
                <div style="margin-top:6px; font-size:12px; color:#6b7280; font-weight:800;">
                  No other available tables to share with right now.
                </div>
              @endif
            </div>

            <button class="to-btn dark" type="submit" style="margin-top: 12px;">
              Continue Shared
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
