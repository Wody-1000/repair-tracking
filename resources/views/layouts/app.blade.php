<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Repair Tracking') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Mono:wght@500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Palette: near-black base, charcoal surfaces, one muted gold accent.
           Variable names are unchanged so other views that use them keep working. */
        :root {
            --bg:#0b0c0e; --card:#131518; --ink:#f2f3f5; --muted:#8b919a; --line:#24272c;
            --brand:#c8a24a; --gold-hi:#dcb961; --brand-soft:rgba(200,162,74,.12); --led:var(--brand);
            --ok:#4cc38a; --warn:#e0a63a; --bad:#e5645f; --r-lg:8px; --r-md:6px;
        }
        * { box-sizing:border-box; }
        body { margin:0; font:15px/1.6 "IBM Plex Sans", system-ui, "Segoe UI", sans-serif; background:var(--bg); color:var(--ink); min-height:100vh; }
        h1, h2, .brand, nav a { font-family:"Chakra Petch", "IBM Plex Sans", sans-serif; }
        .mono { font-family:"IBM Plex Mono", ui-monospace, Consolas, monospace; letter-spacing:.03em; }
        main a:not(.btn) { color:var(--gold-hi); }

        /* Header */
        header { background:var(--bg); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:20; }
        .bar { max-width:1100px; margin:auto; padding:10px 20px; display:flex; gap:20px; align-items:center; flex-wrap:wrap; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; font-size:18px; color:#fff; text-decoration:none; }
        .mark { display:inline-grid; place-items:center; width:28px; height:28px; border:1.5px solid var(--brand); border-radius:5px; color:var(--gold-hi); font:600 11px "IBM Plex Mono", monospace; }
        nav { display:flex; gap:2px; flex-wrap:wrap; flex:1; }
        nav a { color:var(--muted); text-decoration:none; font-size:14px; font-weight:500; padding:6px 12px; border-radius:4px; }
        nav a:hover { color:#fff; }
        nav a.on { color:#fff; box-shadow:inset 0 -2px 0 var(--brand); border-radius:4px 4px 0 0; }
        .bar > a:not(.brand) { color:var(--gold-hi); text-decoration:none; font-size:14px; font-weight:600; padding:5px 14px; border:1px solid rgba(200,162,74,.45); border-radius:var(--r-md); }
        .bar > a:not(.brand):hover { background:var(--brand-soft); }
        header .btn.sec { background:rgba(255,255,255,.08); color:#fff; }

        /* Hero */
        .hero { padding:64px 20px 32px; }
        .hero-in { max-width:1100px; margin:auto; display:grid; gap:56px; grid-template-columns:1.1fr .9fr; align-items:center; }
        .hero h1 { font-size:clamp(38px, 5.2vw, 56px); line-height:1.05; letter-spacing:-.02em; margin:0 0 16px; color:#fff; }
        .hero p { font-size:17px; color:var(--muted); margin:0; max-width:44ch; }

        /* Claim slip card (also used for the ticket result) */
        .chip { margin:0 0 24px; border:1px solid var(--line); border-radius:var(--r-lg); background:var(--card); }
        .slip-top, .slip-head { padding:16px 22px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:baseline; gap:12px; flex-wrap:wrap; }
        .slip-top strong { font:600 17px "Chakra Petch", sans-serif; }
        .slip-top span { color:var(--muted); font:13px "IBM Plex Mono", monospace; }
        .chip .slip-label { margin:16px 22px 0; }
        .chip form { padding:8px 22px 22px; }
        .chip input { font-size:16px; }
        .chip .actions input { flex:1 1 180px; }
        .slip-head h2 { font-size:28px; margin:0; font-weight:600; color:var(--gold-hi); }
        .slip-body { padding:22px; }
        .slip-body .device { font-size:17px; font-weight:500; margin:0 0 20px; }
        .badge.big { display:inline-flex; align-items:center; gap:8px; font-size:14px; padding:4px 14px; }
        .badge.big::before { content:""; width:8px; height:8px; border-radius:50%; background:currentColor; }

        /* Information and parts sections */
        .facts { display:grid; gap:28px 32px; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); margin-top:40px; }
        .facts div { border-top:1px solid var(--line); padding-top:16px; }
        .facts strong { display:block; margin-bottom:4px; font:600 16px "Chakra Petch", sans-serif; color:#fff; }
        .stock-title { margin:56px 0 0; }
        .parts { margin-top:16px; }

        /* Page */
        main { max-width:1100px; margin:30px auto 60px; padding:0 20px; }
        h1 { font-size:32px; line-height:1.1; font-weight:700; margin:0 0 10px; color:#fff; }
        h2 { font-size:20px; font-weight:600; margin:0 0 10px; color:#fff; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:var(--r-lg); padding:24px; margin-bottom:20px; overflow-x:auto; }
        .grid { display:grid; gap:20px; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); }

        /* Tables */
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:11px 12px; border-bottom:1px solid var(--line); vertical-align:top; }
        th { font-size:13px; font-weight:600; color:var(--muted); white-space:nowrap; }
        tbody tr:hover td { background:rgba(255,255,255,.03); }
        .num { text-align:right; font-family:"IBM Plex Mono", monospace; }

        /* Forms */
        label { display:block; font-weight:500; margin:14px 0 5px; color:#cbd0d6; }
        input, select, textarea { width:100%; padding:10px 13px; border:1px solid #2e3238; border-radius:var(--r-md); font:inherit; background:#0e0f12; color:var(--ink); }
        select option { background:#131518; color:var(--ink); }
        input::placeholder, textarea::placeholder { color:#5f666f; }
        input:focus, select:focus, textarea:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 3px rgba(200,162,74,.18); }
        input[type=checkbox] { width:auto; accent-color:var(--brand); }
        textarea { min-height:100px; }
        .row { display:grid; gap:16px; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); }
        .err-text { color:var(--bad); font-size:13px; margin-top:4px; }

        /* Buttons */
        .btn { display:inline-block; background:var(--brand); color:#15110a; border:0; border-radius:var(--r-md); padding:10px 22px; font:inherit; font-weight:700; cursor:pointer; text-decoration:none; }
        .btn:hover { background:var(--gold-hi); }
        .btn.sec { background:rgba(255,255,255,.08); color:var(--ink); } .btn.sec:hover { background:rgba(255,255,255,.15); }
        .btn.red { background:var(--bad); color:#250707; } .btn.red:hover { background:#ee8480; }
        .btn.sm { padding:5px 11px; font-size:13px; }
        .btn:focus-visible, nav a:focus-visible, .bar > a:focus-visible { outline:2px solid var(--gold-hi); outline-offset:2px; }

        /* Messages and badges */
        .flash { padding:12px 16px; border-radius:var(--r-md); margin-bottom:16px; border-left:3px solid transparent; }
        .flash.ok { background:rgba(76,195,138,.1); color:#8fdcb6; border-left-color:var(--ok); }
        .flash.err { background:rgba(229,100,95,.1); color:#f0a29e; border-left-color:var(--bad); }
        .badge { display:inline-block; padding:2px 10px; border-radius:99px; font-size:12px; font-weight:600; background:var(--brand-soft); color:var(--gold-hi); }
        .badge.low { background:rgba(229,100,95,.14); color:#f0a29e; }
        header .badge { background:rgba(255,255,255,.08); color:#d8dde3; }
        .muted { color:var(--muted); }
        .actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        form.inline { display:inline; }
        .pager { margin-top:12px; } .pager nav { display:block; } .pager svg { width:16px; height:16px; }

        /* Timeline */
        .tl { list-style:none; padding:0; margin:0; }
        .tl li { position:relative; padding:2px 0 18px 22px; border-left:1px solid var(--line); margin-left:5px; }
        .tl li:last-child { padding-bottom:0; }
        .tl li::before { content:""; position:absolute; left:-5px; top:8px; width:9px; height:9px; border-radius:50%; background:var(--bg); border:2px solid var(--brand); }

        /* Tablet */
        @media (max-width:900px) {
            .hero-in { gap:40px; }
        }
        /* Mobile */
        @media (max-width:800px) {
            .bar { gap:10px 14px; padding:10px 16px; }
            .brand { margin-right:auto; }
            .bar nav { order:3; flex:1 0 100%; flex-wrap:nowrap; overflow-x:auto; scrollbar-width:none; }
            .bar nav::-webkit-scrollbar { display:none; }
            .bar nav a { white-space:nowrap; }
            .hero { padding:36px 20px 20px; }
            .hero-in { grid-template-columns:1fr; gap:32px; }
            .facts { margin-top:32px; }
            .stock-title { margin-top:44px; }
        }
        @media (max-width:480px) {
            .slip-top, .slip-head, .slip-body { padding-left:16px; padding-right:16px; }
            .chip .slip-label { margin-left:16px; margin-right:16px; }
            .chip form { padding:8px 16px 18px; }
        }
    </style>
</head>
<body>
<header>
    <div class="bar">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('status') }}"><span class="mark">RT</span> Repair Tracking</a>
        <nav>
            <a href="{{ route('status') }}" class="{{ request()->routeIs('status') ? 'on' : '' }}">Check status</a>
            <a href="{{ route('parts.available') }}" class="{{ request()->routeIs('parts.available') ? 'on' : '' }}">Available parts</a>
            @auth
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">Dashboard</a>
                <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*') ? 'on' : '' }}">Tickets</a>
                <a href="{{ route('parts.index') }}" class="{{ request()->routeIs('parts.*') ? 'on' : '' }}">Parts</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'on' : '' }}">Reports</a>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'on' : '' }}">Users</a>
                @endif
            @endauth
        </nav>
        @auth
            <span class="muted">{{ auth()->user()->name }} <span class="badge">{{ str_replace('_', ' ', auth()->user()->role) }}</span></span>
            <form method="POST" action="{{ route('logout') }}" class="inline">@csrf <button class="btn sec sm">Log out</button></form>
        @else
            <a href="{{ route('login') }}">Staff login</a>
        @endauth
    </div>
</header>
@yield('hero')
<main>
    @if (session('success')) <div class="flash ok">{{ session('success') }}</div> @endif
    @if ($errors->any())
        <div class="flash err">
            @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>