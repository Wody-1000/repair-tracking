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
        :root {
            --bg:#0a0d12; --card:rgba(18,24,32,.9); --ink:#e9eef5; --muted:#8a98a9; --line:rgba(224,174,82,.2);
            --brand:#e0ae52; --gold-hi:#f3c872; --brand-soft:rgba(224,174,82,.14); --led:#2de2c8;
            --ok:#3df08f; --warn:#ffb02e; --bad:#ff5c5c; --r-lg:8px; --r-md:6px;
        }
        * { box-sizing:border-box; }
        body { margin:0; font:15px/1.6 "IBM Plex Sans", system-ui, "Segoe UI", sans-serif; background:var(--bg); color:var(--ink); min-height:100vh; }

        /* Background: faint circuit traces, static */
        body::before { content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
            background:
                radial-gradient(50% 40% at 82% 0%, rgba(224,174,82,.09), transparent 70%),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240' fill='none' stroke='rgb(224,174,82)' stroke-opacity='.1' stroke-width='1.5'%3E%3Cpath d='M0 40h70l20 20h60M0 120h40l24-24h90l20 20h66M60 240v-60l20-20h40M180 0v50l-20 20h-30M240 190h-50l-20 20v30'/%3E%3Ccircle cx='70' cy='40' r='3'/%3E%3Ccircle cx='150' cy='60' r='3'/%3E%3Ccircle cx='40' cy='120' r='3'/%3E%3Ccircle cx='226' cy='116' r='3'/%3E%3Ccircle cx='120' cy='160' r='3'/%3E%3Ccircle cx='170' cy='210' r='3'/%3E%3C/svg%3E"); }

        h1, h2, .brand, nav a { font-family:"Chakra Petch", "IBM Plex Sans", sans-serif; }
        .mono { font-family:"IBM Plex Mono", ui-monospace, Consolas, monospace; letter-spacing:.03em; }
        main a:not(.btn) { color:var(--gold-hi); }

        /* Header */
        header { background:rgba(10,13,18,.9); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:20; }
        .bar { max-width:1100px; margin:auto; padding:12px 20px; display:flex; gap:22px; align-items:center; flex-wrap:wrap; }
        .brand { display:flex; align-items:center; gap:14px; font-weight:700; font-size:19px; color:#fff; text-decoration:none; }
        .mark { position:relative; display:inline-grid; place-items:center; width:28px; height:28px; margin:0 8px; border:2px solid var(--brand); border-radius:4px; background:#1a1408; color:var(--gold-hi); font:600 11px "IBM Plex Mono", monospace; }
        .mark::before, .mark::after { content:""; position:absolute; top:3px; bottom:3px; width:6px; background:repeating-linear-gradient(var(--brand) 0 2px, transparent 2px 5px); }
        .mark::before { left:-10px; } .mark::after { right:-10px; }
        nav { display:flex; gap:4px; flex-wrap:wrap; flex:1; }
        nav a { color:var(--muted); text-decoration:none; font-weight:500; padding:6px 13px; border-radius:var(--r-md); }
        nav a:hover { color:#fff; background:rgba(255,255,255,.07); }
        nav a.on { color:var(--gold-hi); background:var(--brand-soft); box-shadow:inset 0 0 0 1px rgba(224,174,82,.45); }
        .bar > a:not(.brand) { color:var(--gold-hi); text-decoration:none; font-weight:600; padding:6px 15px; border:1px solid rgba(224,174,82,.5); border-radius:var(--r-md); }
        .bar > a:not(.brand):hover { background:var(--brand-soft); }
        header .btn.sec { background:rgba(255,255,255,.09); color:#fff; }

        /* Hero */
        .hero { padding:70px 20px 40px; }
        .hero-in { max-width:1100px; margin:auto; display:grid; gap:48px; grid-template-columns:1.05fr .95fr; align-items:center; }
        .hero h1 { font-size:68px; line-height:1; letter-spacing:-.02em; margin:0 0 18px; color:#fff; }
        .hero p { font-size:18px; color:var(--muted); margin:0; max-width:44ch; }
        .board { position:relative; padding:0 48px; }
        .tr { position:absolute; top:0; height:100%; width:48px; overflow:visible; }
        .tr.l { left:0; } .tr.r { right:0; transform:scaleX(-1); }
        .tr path { fill:none; stroke:var(--brand); stroke-width:1.5; vector-effect:non-scaling-stroke; stroke-dasharray:5 9; animation:flow 2.4s linear infinite; }
        .tr path.pad { stroke-dasharray:none; animation:none; stroke-width:8; stroke-linecap:round; }
        @keyframes flow { to { stroke-dashoffset:-28; } }

        /* Chip package: the signature element */
        .chip { position:relative; margin:14px 0 24px; border-radius:8px; border:1px solid var(--line); background:linear-gradient(180deg, rgba(30,38,50,.95), rgba(14,19,27,.95)); box-shadow:0 20px 50px rgba(0,0,0,.45); }
        .chip::before, .chip::after { content:""; position:absolute; left:28px; right:28px; height:10px; background:repeating-linear-gradient(90deg, var(--brand) 0 9px, transparent 9px 21px); opacity:.85; }
        .chip::before { top:-10px; border-radius:2px 2px 0 0; } .chip::after { bottom:-10px; border-radius:0 0 2px 2px; }
        .slip-top, .slip-head { position:relative; padding:18px 22px 18px 38px; border-bottom:1px dashed rgba(224,174,82,.35); display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; }
        .slip-top::before, .slip-head::before { content:""; position:absolute; left:14px; top:50%; width:10px; height:10px; margin-top:-5px; border-radius:50%; background:#0a0d12; box-shadow:inset 0 0 0 2px rgba(224,174,82,.6); }
        .slip-top strong { font-family:"Chakra Petch",sans-serif; font-size:18px; }
        .slip-top span { color:var(--muted); font-size:13px; font-family:"IBM Plex Mono",monospace; }
        .chip form { padding:22px; }
        .chip input { font-size:17px; }
        .slip-head h2 { font-size:30px; margin:0; font-weight:600; color:var(--gold-hi); }
        .slip-body { padding:22px; }
        .slip-body .device { font-size:17px; font-weight:500; margin:0 0 20px; }
        .badge.big { display:inline-flex; align-items:center; gap:8px; font-size:14px; padding:5px 14px; }
        .badge.big::before { content:""; width:9px; height:9px; border-radius:50%; background:var(--ok); box-shadow:0 0 8px var(--ok); }
        .facts { display:grid; gap:30px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); margin-top:34px; }
        .facts div { border-top:2px solid var(--brand); padding-top:14px; }
        .facts strong { display:block; margin-bottom:4px; font-family:"Chakra Petch",sans-serif; font-size:17px; }

        /* Page */
        main { max-width:1100px; margin:30px auto 60px; padding:0 20px; }
        h1 { font-size:32px; line-height:1.1; font-weight:700; margin:0 0 10px; color:#fff; }
        h2 { font-size:20px; font-weight:600; margin:0 0 10px; color:#fff; }
        .card { background:var(--card); border:1px solid var(--line); border-top-color:rgba(224,174,82,.5); border-radius:var(--r-lg); padding:26px 24px 24px; margin-bottom:20px; box-shadow:0 12px 32px rgba(0,0,0,.35); overflow-x:auto; }
        .grid { display:grid; gap:20px; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); }

        /* Tables */
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:11px 12px; border-bottom:1px solid rgba(224,174,82,.12); vertical-align:top; }
        th { font-size:13px; font-weight:600; color:var(--gold-hi); background:rgba(224,174,82,.07); white-space:nowrap; }
        tbody tr:hover td { background:rgba(224,174,82,.06); }
        .num { text-align:right; font-family:"IBM Plex Mono",monospace; }

        /* Forms */
        label { display:block; font-weight:500; margin:14px 0 5px; color:#cbd5e1; }
        input, select, textarea { width:100%; padding:11px 13px; border:1px solid rgba(224,174,82,.3); border-radius:var(--r-md); font:inherit; background:rgba(5,8,12,.7); color:var(--ink); }
        select option { background:#121820; color:var(--ink); }
        input::placeholder, textarea::placeholder { color:#5d6b7c; }
        input:focus, select:focus, textarea:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 3px rgba(224,174,82,.22); }
        input[type=checkbox] { width:auto; accent-color:var(--brand); }
        textarea { min-height:100px; }
        .row { display:grid; gap:16px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
        .err-text { color:var(--bad); font-size:13px; margin-top:4px; }

        /* Buttons */
        .btn { display:inline-block; background:var(--brand); color:#1a1204; border:0; border-radius:var(--r-md); padding:11px 22px; font:inherit; font-weight:700; cursor:pointer; text-decoration:none; }
        .btn:hover { background:var(--gold-hi); }
        .btn.sec { background:rgba(255,255,255,.09); color:var(--ink); } .btn.sec:hover { background:rgba(255,255,255,.16); }
        .btn.red { background:var(--bad); color:#250707; } .btn.red:hover { background:#ff8585; }
        .btn.sm { padding:5px 11px; font-size:13px; }
        .btn:focus-visible, nav a:focus-visible, .bar > a:focus-visible { outline:3px solid var(--led); outline-offset:2px; }

        /* Messages and badges */
        .flash { padding:12px 16px; border-radius:var(--r-md); margin-bottom:16px; border-left:4px solid transparent; }
        .flash.ok { background:rgba(61,240,143,.11); color:#8ff5bd; border-left-color:var(--ok); }
        .flash.err { background:rgba(255,92,92,.12); color:#ffa0a0; border-left-color:var(--bad); }
        .badge { display:inline-block; padding:2px 10px; border-radius:99px; font-size:12px; font-weight:600; background:var(--brand-soft); color:var(--gold-hi); box-shadow:inset 0 0 0 1px rgba(224,174,82,.35); }
        .badge.low { background:rgba(255,92,92,.14); color:#ff9f9f; box-shadow:inset 0 0 0 1px rgba(255,92,92,.4); }
        header .badge { background:rgba(255,255,255,.1); color:#d8e0ea; box-shadow:none; }
        .muted { color:var(--muted); }
        .actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        form.inline { display:inline; }
        .pager { margin-top:12px; } .pager nav { display:block; } .pager svg { width:16px; height:16px; }

        /* Timeline: a trace with vias */
        .tl { list-style:none; padding:0; margin:0; }
        .tl li { position:relative; padding:6px 0 16px 28px; border-left:2px solid rgba(224,174,82,.5); margin-left:7px; }
        .tl li::before { content:""; position:absolute; left:-9px; top:9px; width:16px; height:16px; border-radius:50%; background:#0a0d12; border:4px solid var(--brand); }

        @media (max-width:800px) {
            .hero { padding:40px 20px 24px; }
            .hero-in { grid-template-columns:1fr; gap:34px; }
            .hero h1 { font-size:44px; }
            .board { padding:0; } .tr { display:none; }
        }
        @media (prefers-reduced-motion:reduce) { .tr path { animation:none; } }
    </style>
</head>
<body>
<header>
    <div class="bar">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('status') }}"><span class="mark">RT</span> Repair Tracking</a>
        <nav>
            <a href="{{ route('status') }}" class="{{ request()->routeIs('status') ? 'on' : '' }}">Check status</a>
            @auth
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">Dashboard</a>
                <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*') ? 'on' : '' }}">Tickets</a>
                <a href="{{ route('parts.index') }}" class="{{ request()->routeIs('parts.*') ? 'on' : '' }}">Parts</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'on' : '' }}">Reports</a>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'on' : '' }}">Users</a>
                    <a href="{{ route('admin.database') }}" class="{{ request()->routeIs('admin.database') ? 'on' : '' }}">Database</a>
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