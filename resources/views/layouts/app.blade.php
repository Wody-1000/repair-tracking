<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Repair Tracking') · {{ config('app.name') }}</title>
    <style>
        :root { --bg:#f5f6f8; --card:#fff; --ink:#1d2330; --muted:#6a7385; --line:#e1e5ec; --brand:#2455d6; --ok:#17803d; --warn:#b45309; --bad:#b42318; }
        * { box-sizing: border-box; }
        body { margin:0; font:15px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background:var(--bg); color:var(--ink); }
        header { background:#fff; border-bottom:1px solid var(--line); }
        .bar { max-width:1100px; margin:auto; padding:12px 20px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
        .brand { font-weight:700; color:var(--ink); text-decoration:none; }
        nav { display:flex; gap:14px; flex-wrap:wrap; flex:1; }
        nav a { color:var(--muted); text-decoration:none; }
        nav a:hover, nav a.on { color:var(--brand); }
        main { max-width:1100px; margin:24px auto; padding:0 20px; }
        h1 { font-size:22px; margin:0 0 16px; }
        h2 { font-size:17px; margin:0 0 10px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:10px; padding:18px; margin-bottom:18px; }
        .grid { display:grid; gap:18px; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:8px 10px; border-bottom:1px solid var(--line); vertical-align:top; }
        th { font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); }
        label { display:block; font-weight:600; margin:12px 0 4px; }
        input, select, textarea { width:100%; padding:8px 10px; border:1px solid #cbd2dd; border-radius:7px; font:inherit; background:#fff; }
        textarea { min-height:90px; }
        .row { display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
        .btn { display:inline-block; background:var(--brand); color:#fff; border:0; border-radius:7px; padding:8px 16px; font:inherit; cursor:pointer; text-decoration:none; }
        .btn.sec { background:#e8ecf3; color:var(--ink); }
        .btn.red { background:var(--bad); }
        .btn.sm { padding:4px 10px; font-size:13px; }
        .flash { padding:10px 14px; border-radius:8px; margin-bottom:16px; }
        .flash.ok { background:#e6f4ec; color:var(--ok); }
        .flash.err { background:#fdeceb; color:var(--bad); }
        .badge { display:inline-block; padding:2px 9px; border-radius:99px; font-size:12px; background:#e8ecf3; }
        .badge.low { background:#fff1dc; color:var(--warn); font-weight:600; }
        .muted { color:var(--muted); }
        .actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        form.inline { display:inline; }
        .pager { margin-top:12px; } .pager nav { display:block; } .pager svg { width:16px; height:16px; }
        .tl { list-style:none; padding:0; margin:0; } .tl li { padding:8px 0 8px 14px; border-left:3px solid var(--brand); margin-left:4px; }
        .num { text-align:right; }
        .err-text { color:var(--bad); font-size:13px; margin-top:4px; }
    </style>
</head>
<body>
<header>
    <div class="bar">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('status') }}">🔧 Repair Tracking</a>
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
