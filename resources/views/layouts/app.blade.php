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
            --bg:#0a0d12; --card:rgba(18,24,32,.84); --ink:#e9eef5; --muted:#8a98a9; --line:rgba(224,174,82,.2);
            --brand:#e0ae52; --gold-hi:#f3c872; --brand-soft:rgba(224,174,82,.14); --led:#2de2c8;
            --ok:#3df08f; --warn:#ffb02e; --bad:#ff5c5c; --r-lg:8px; --r-md:6px;
        }
        * { box-sizing:border-box; }
        body { margin:0; font:15px/1.6 "IBM Plex Sans", system-ui, "Segoe UI", sans-serif; background:var(--bg); color:var(--ink); min-height:100vh; }
        body::before { content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
            background:
                radial-gradient(50% 40% at 82% 0%, rgba(224,174,82,.11), transparent 70%),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240' fill='none' stroke='rgb(224,174,82)' stroke-opacity='.12' stroke-width='1.5'%3E%3Cpath d='M0 40h70l20 20h60M0 120h40l24-24h90l20 20h66M60 240v-60l20-20h40M180 0v50l-20 20h-30M240 190h-50l-20 20v30'/%3E%3Ccircle cx='70' cy='40' r='3'/%3E%3Ccircle cx='150' cy='60' r='3'/%3E%3Ccircle cx='40' cy='120' r='3'/%3E%3Ccircle cx='226' cy='116' r='3'/%3E%3Ccircle cx='120' cy='160' r='3'/%3E%3Ccircle cx='170' cy='210' r='3'/%3E%3C/svg%3E"); }
        h1, h2, .brand, nav a { font-family:"Chakra Petch", "IBM Plex Sans", sans-serif; }
        .mono { font-family:"IBM Plex Mono", ui-monospace, Consolas, monospace; letter-spacing:.03em; }
        main a:not(.btn) { color:var(--gold-hi); }

        /* Header */
        header { background:rgba(10,13,18,.82); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:20; }
        .bar { max-width:1100px; margin:auto; padding:12px 20px; display:flex; gap:22px; align-items:center; flex-wrap:wrap; }
        .brand { display:flex; align-items:center; gap:14px; font-weight:700; font-size:19px; color:#fff; text-decoration:none; }
        .mark { position:relative; display:inline-grid; place-items:center; width:28px; height:28px; margin:0 8px; border:2px solid var(--brand); border-radius:4px; background:#1a1408; color:var(--gold-hi); font:600 11px "IBM Plex Mono", monospace; }
        .mark::before, .mark::after { content:""; position:absolute; top:3px; bottom:3px; width:6px; background:repeating-linear-gradient(var(--brand) 0 2px, transparent 2px 5px); }
        .mark::before { left:-10px; } .mark::after { right:-10px; }
        nav { display:flex; gap:4px; flex-wrap:wrap; flex:1; }
        nav a { color:var(--muted); text-decoration:none; font-weight:500; padding:6px 13px; border-radius:var(--r-md); transition:background .15s,color .15s; }
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
        .chip { position:relative; margin:14px 0 24px; border-radius:8px; border:1px solid var(--line); background:linear-gradient(180deg, rgba(30,38,50,.95), rgba(14,19,27,.95)); box-shadow:0 24px 60px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.06); }
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
        .badge.big::before { content:""; width:9px; height:9px; border-radius:50%; background:var(--ok); box-shadow:0 0 10px var(--ok); animation:led 2s ease-in-out infinite; }
        @keyframes led { 50% { opacity:.35; } }
        .facts { display:grid; gap:30px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); margin-top:34px; }
        .facts div { border-top:2px solid var(--brand); padding-top:14px; }
        .facts strong { display:block; margin-bottom:4px; font-family:"Chakra Petch",sans-serif; font-size:17px; }

        /* Page */
        main { max-width:1100px; margin:30px auto 60px; padding:0 20px; }
        h1 { font-size:32px; line-height:1.1; font-weight:700; margin:0 0 10px; color:#fff; }
        h2 { font-size:20px; font-weight:600; margin:0 0 10px; color:#fff; }
        .card { position:relative; background:var(--card); backdrop-filter:blur(10px); border:1px solid var(--line); border-top-color:rgba(224,174,82,.5); border-radius:var(--r-lg); padding:26px 24px 24px; margin-bottom:20px; box-shadow:0 18px 44px rgba(0,0,0,.4); overflow-x:auto; }
        .card::before, .card::after { content:""; position:absolute; top:8px; width:7px; height:7px; border-radius:50%; background:#0a0d12; box-shadow:inset 0 0 0 1.5px rgba(224,174,82,.6); }
        .card::before { left:8px; } .card::after { right:8px; }
        .grid { display:grid; gap:20px; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); }

        /* Tables */
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:11px 12px; border-bottom:1px solid rgba(224,174,82,.12); vertical-align:top; }
        th { font-size:13px; font-weight:600; color:var(--gold-hi); background:rgba(224,174,82,.07); white-space:nowrap; }
        tbody tr:hover td { background:rgba(224,174,82,.06); }
        .num { text-align:right; font-family:"IBM Plex Mono",monospace; }

        /* Forms */
        label { display:block; font-weight:500; margin:14px 0 5px; color:#cbd5e1; }
        input, select, textarea { width:100%; padding:11px 13px; border:1px solid rgba(224,174,82,.3); border-radius:var(--r-md); font:inherit; background:rgba(5,8,12,.7); color:var(--ink); transition:border-color .15s, box-shadow .15s; }
        select option { background:#121820; color:var(--ink); }
        input::placeholder, textarea::placeholder { color:#5d6b7c; }
        input:focus, select:focus, textarea:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 3px rgba(224,174,82,.22); }
        input[type=checkbox] { width:auto; accent-color:var(--brand); }
        textarea { min-height:100px; }
        .row { display:grid; gap:16px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
        .err-text { color:var(--bad); font-size:13px; margin-top:4px; }

        /* Buttons */
        .btn { display:inline-block; background:var(--brand); color:#1a1204; border:0; border-radius:var(--r-md); padding:11px 22px; font:inherit; font-weight:700; cursor:pointer; text-decoration:none; box-shadow:0 8px 22px rgba(224,174,82,.25); transition:transform .15s, box-shadow .15s, background .15s; }
        .btn:hover { background:var(--gold-hi); box-shadow:0 10px 28px rgba(224,174,82,.4); transform:translateY(-1px); }
        .btn.sec { background:rgba(255,255,255,.09); color:var(--ink); box-shadow:none; } .btn.sec:hover { background:rgba(255,255,255,.16); box-shadow:none; }
        .btn.red { background:var(--bad); color:#250707; box-shadow:0 8px 22px rgba(255,92,92,.25); } .btn.red:hover { background:#ff8585; }
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
        .tl li::before { content:""; position:absolute; left:-9px; top:9px; width:16px; height:16px; border-radius:50%; background:#0a0d12; border:4px solid var(--brand); box-shadow:0 0 12px rgba(224,174,82,.45); }

        @media (max-width:800px) {
            .hero { padding:40px 20px 24px; }
            .hero-in { grid-template-columns:1fr; gap:34px; }
            .hero h1 { font-size:44px; }
            .board { padding:0; } .tr { display:none; }
        }
        @media (prefers-reduced-motion:reduce) { .tr path, .badge.big::before { animation:none; } .btn:hover { transform:none; } }

        /* ===== HERO FX: the cursor is a probe on a live circuit board ===== */

        /* 1. Circuit traces light up under the mouse */
        body::after {
            content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
            background:
                radial-gradient(300px circle at var(--mx,-999px) var(--my,-999px), rgba(224,174,82,.10), transparent 70%),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240' fill='none' stroke='rgb(243,200,114)' stroke-opacity='.95' stroke-width='1.6'%3E%3Cpath d='M0 40h70l20 20h60M0 120h40l24-24h90l20 20h66M60 240v-60l20-20h40M180 0v50l-20 20h-30M240 190h-50l-20 20v30'/%3E%3Ccircle cx='70' cy='40' r='3'/%3E%3Ccircle cx='150' cy='60' r='3'/%3E%3Ccircle cx='40' cy='120' r='3'/%3E%3Ccircle cx='226' cy='116' r='3'/%3E%3Ccircle cx='120' cy='160' r='3'/%3E%3Ccircle cx='170' cy='210' r='3'/%3E%3C/svg%3E");
            -webkit-mask-image:radial-gradient(340px circle at var(--mx,-999px) var(--my,-999px), #000 0, transparent 70%);
            mask-image:radial-gradient(340px circle at var(--mx,-999px) var(--my,-999px), #000 0, transparent 70%);
        }
        @media (pointer:coarse) { body::after { display:none; } }

        /* Canvas for sparks and cursor ring (created by JS) */
        .rt-fx { position:fixed; inset:0; width:100%; height:100%; pointer-events:none; z-index:15; }

        /* 2. Headline: letters drop in, then lift and glow gold near the cursor */
        .hero h1 { position:relative; perspective:600px; }
        .hero h1 .rt-w { display:inline-block; white-space:nowrap; }
        .hero h1 .rt-ch {
            display:inline-block; --p:0;
            transform:translateY(calc(var(--p) * -14px)) scale(calc(1 + var(--p) * .12));
            color:#fff;
            color:color-mix(in srgb, var(--gold-hi) calc(var(--p) * 100%), #fff);
            text-shadow:0 0 calc(var(--p) * 28px) rgba(243,200,114, calc(var(--p) * .95));
            transition:transform .12s ease-out, color .12s, text-shadow .12s;
            will-change:transform;
            animation:rt-drop .8s cubic-bezier(.2,.8,.2,1) backwards;
            animation-delay:calc(var(--i) * 45ms + .2s);
        }
        @keyframes rt-drop {
            from { opacity:0; transform:translateY(46px) rotateX(-80deg); }
            to   { opacity:1; transform:none; }
        }

        /* 3. Chip: rises in, tilts toward the cursor, glare follows, pins power on */
        .chip {
            background:
                radial-gradient(360px circle at var(--gx,50%) var(--gy,0%), rgba(255,236,190,.15), transparent 62%),
                linear-gradient(180deg, rgba(30,38,50,.95), rgba(14,19,27,.95));
            transform:perspective(900px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg));
            box-shadow:var(--sx,0px) var(--sy,24px) 60px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.06);
            transition:transform .18s ease-out, border-color .25s, box-shadow .25s;
        }
        .hero .chip { animation:rt-rise .9s .55s cubic-bezier(.2,.8,.2,1) backwards; }
        @keyframes rt-rise {
            from { opacity:0; transform:perspective(900px) translateY(46px) rotateX(18deg); }
            to   { opacity:1; transform:perspective(900px); }
        }
        .chip:hover {
            border-color:rgba(224,174,82,.6);
            box-shadow:var(--sx,0px) var(--sy,24px) 60px rgba(0,0,0,.5), 0 0 46px rgba(224,174,82,.16), inset 0 1px 0 rgba(255,255,255,.06);
        }
        .chip::before, .chip::after { transition:filter .3s; }
        .chip::before { animation:rt-boot 1.2s .9s both; }
        .chip::after  { animation:rt-boot 1.2s 1.15s both; }
        .chip:hover::before, .chip:hover::after { filter:drop-shadow(0 0 7px rgba(243,200,114,.95)) brightness(1.25); }
        @keyframes rt-boot {
            0% { opacity:0; } 20% { opacity:.9; } 30% { opacity:.2; }
            55% { opacity:.9; } 65% { opacity:.35; } 100% { opacity:.85; }
        }

        /* Current flows through the side traces while you are on the board */
        .tr path { transition:stroke .35s, filter .35s; }
        .board:hover .tr path { stroke:var(--led); animation-duration:.7s; filter:drop-shadow(0 0 5px var(--led)); }

        /* Input goes live, button gets a magnetic pull and a shine */
        .chip input { caret-color:var(--led); }
        .chip input:focus { border-color:var(--led); box-shadow:0 0 0 3px rgba(45,226,200,.18), 0 0 26px rgba(45,226,200,.18); }
        .chip .btn { position:relative; overflow:hidden; will-change:transform; }
        .chip .btn::after {
            content:""; position:absolute; inset:0; pointer-events:none;
            background:linear-gradient(105deg, transparent 40%, rgba(255,255,255,.6) 50%, transparent 60%);
            transform:translateX(-120%); transition:transform .6s;
        }
        .chip .btn:hover::after { transform:translateX(120%); }

        /* 4. Feature cards: 3D tilt, spotlight under the cursor, teal current along the top */
        .facts div {
            position:relative; isolation:isolate; padding:16px 16px 20px; border-radius:6px;
            transform:perspective(700px) rotateX(var(--crx,0deg)) rotateY(var(--cry,0deg)) translateY(var(--lift,0px));
            transition:transform .2s ease-out, box-shadow .3s;
        }
        .facts div::before {
            content:""; position:absolute; inset:0; z-index:-1; pointer-events:none; opacity:0; transition:opacity .25s;
            background:radial-gradient(240px circle at var(--fx,50%) var(--fy,0%), rgba(224,174,82,.2), transparent 70%);
        }
        .facts div::after {
            content:""; position:absolute; left:0; top:-2px; height:2px; width:0;
            background:var(--led); box-shadow:0 0 14px var(--led);
            transition:width .5s cubic-bezier(.2,.7,.2,1);
        }
        .facts div:hover { --lift:-6px; box-shadow:inset 0 0 0 1px rgba(224,174,82,.3), 0 20px 40px rgba(0,0,0,.4); }
        .facts div:hover::before { opacity:1; }
        .facts div:hover::after { width:100%; }
        .facts strong { transition:color .25s; }
        .facts div:hover strong { color:var(--gold-hi); }

        @media (prefers-reduced-motion:reduce) {
            .hero .chip, .chip::before, .chip::after, .hero h1 .rt-ch { animation:none; }
            .chip { transform:none !important; }
            .facts div { transform:none !important; }
        }

        /* ===== PARTS ART: drawn hardware icons in the inventory table ===== */
        .pt-name { display:flex; align-items:center; gap:16px; }
        .pa { flex:none; width:84px; height:54px; overflow:visible; transition:transform .35s cubic-bezier(.3,.8,.2,1), filter .35s; filter:drop-shadow(0 6px 8px rgba(0,0,0,.55)); }
        tbody tr:hover .pa { transform:translateY(-3px) scale(1.22) rotate(-3deg); filter:drop-shadow(0 10px 14px rgba(0,0,0,.6)) drop-shadow(0 0 10px rgba(224,174,82,.45)); }
        .pa-spin { transform-origin:0 0; animation:pa-spin 3s linear infinite; }
        tbody tr:hover .pa-spin { animation-duration:.5s; }
        .pa-rgb { animation:pa-hue 7s linear infinite; }
        .pa-led { animation:pa-blink 1.6s ease-in-out infinite; }
        .pa-arm { transform-origin:74px 18px; transition:transform .6s cubic-bezier(.3,.7,.2,1); }
        tbody tr:hover .pa-arm { transform:rotate(-14deg); }
        .pa-scr { transition:filter .3s; } tbody tr:hover .pa-scr { filter:brightness(1.5); }
        @keyframes pa-spin { to { transform:rotate(360deg); } }
        @keyframes pa-hue { to { filter:hue-rotate(360deg); } }
        @keyframes pa-blink { 50% { opacity:.35; } }
        @media (prefers-reduced-motion:reduce) { .pa-spin, .pa-rgb, .pa-led { animation:none; } tbody tr:hover .pa { transform:none; } }
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

<script>
(function () {
    // Desktop mouse only; skip touch screens and reduced-motion users
    if (!matchMedia('(pointer:fine)').matches || matchMedia('(prefers-reduced-motion:reduce)').matches) return;

    var root = document.documentElement;
    var hero = document.querySelector('.hero');
    var h1 = document.querySelector('.hero h1');
    var chip = document.querySelector('.chip');
    var btn = chip ? chip.querySelector('.btn') : null;
    var cards = document.querySelectorAll('.facts div');
    var x = -9999, y = -9999, px = -9999, py = -9999;
    var idle = 0, running = false, big = false, tx = 0, ty = 0;
    var gx = 0, gy = 0, ga = 0, gr = 17;
    var S = [], L = [];

    function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }

    // Split the headline into letters (screen readers still get the full text)
    if (h1) {
        var text = h1.textContent.trim(), n = 0;
        h1.setAttribute('aria-label', text);
        h1.textContent = '';
        text.split(' ').forEach(function (word, wi) {
            if (wi) h1.appendChild(document.createTextNode(' '));
            var w = document.createElement('span');
            w.className = 'rt-w'; w.setAttribute('aria-hidden', 'true');
            word.split('').forEach(function (ch) {
                var s = document.createElement('span');
                s.className = 'rt-ch'; s.style.setProperty('--i', n++);
                s.textContent = ch; w.appendChild(s); L.push({ el: s, p: 0 });
            });
            h1.appendChild(w);
        });
    }

    // Canvas for sparks and the follower ring
    var cv = document.createElement('canvas'), ctx = cv.getContext('2d');
    cv.className = 'rt-fx'; document.body.appendChild(cv);
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    function size() { cv.width = innerWidth * dpr; cv.height = innerHeight * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0); }
    size(); addEventListener('resize', size);

    function inHero() {
        if (!hero) return false;
        var r = hero.getBoundingClientRect();
        return x >= r.left && x <= r.right && y >= r.top && y <= r.bottom;
    }

    function spawn(k) {
        for (var i = 0; i < k && S.length < 220; i++) {
            S.push({
                x: x + (Math.random() - .5) * 8, y: y + (Math.random() - .5) * 8,
                vx: (Math.random() - .5) * 1.6, vy: (Math.random() - .5) * 1.6 - .35,
                l: 0, m: 40 + Math.random() * 34, r: 1 + Math.random() * 1.8, t: Math.random() < .28
            });
        }
    }

    function loop() {
        running = true; idle++;

        root.style.setProperty('--mx', x + 'px');
        root.style.setProperty('--my', y + 'px');

        if (h1) {
            var hr = h1.getBoundingClientRect();
            for (var i = 0; i < L.length; i++) {
                var e = L[i].el;
                var cx = hr.left + e.offsetLeft + e.offsetWidth / 2;
                var cy = hr.top + e.offsetTop + e.offsetHeight / 2;
                var d = Math.hypot(x - cx, (y - cy) * .7);
                var p = Math.max(0, 1 - d / 150);
                p = p * p * (3 - 2 * p);
                if (Math.abs(p - L[i].p) > .005) { L[i].p = p; e.style.setProperty('--p', p.toFixed(3)); }
            }
        }

        if (chip) {
            var c = chip.getBoundingClientRect();
            var near = x > c.left - 160 && x < c.right + 160 && y > c.top - 160 && y < c.bottom + 160;
            var nx = near ? clamp((x - (c.left + c.width / 2)) / (c.width / 2), -1, 1) : 0;
            var ny = near ? clamp((y - (c.top + c.height / 2)) / (c.height / 2), -1, 1) : 0;
            chip.style.setProperty('--ry', (nx * 7).toFixed(2) + 'deg');
            chip.style.setProperty('--rx', (-ny * 6).toFixed(2) + 'deg');
            chip.style.setProperty('--sx', (-nx * 16).toFixed(1) + 'px');
            chip.style.setProperty('--sy', (24 - ny * 10).toFixed(1) + 'px');
            chip.style.setProperty('--gx', clamp((x - c.left) / c.width * 100, -20, 120).toFixed(1) + '%');
            chip.style.setProperty('--gy', clamp((y - c.top) / c.height * 100, -20, 120).toFixed(1) + '%');
            if (btn) {
                var b = btn.getBoundingClientRect();
                var dx = x - (b.left + b.width / 2 - tx), dy = y - (b.top + b.height / 2 - ty);
                if (Math.hypot(dx, dy) < 120) {
                    tx = dx * .25; ty = dy * .25;
                    btn.style.transform = 'translate(' + tx.toFixed(1) + 'px,' + ty.toFixed(1) + 'px)';
                } else { tx = 0; ty = 0; btn.style.transform = ''; }
            }
        }

        ctx.clearRect(0, 0, innerWidth, innerHeight);
        ctx.globalCompositeOperation = 'lighter';
        for (var j = S.length - 1; j >= 0; j--) {
            var q = S[j]; q.l++;
            if (q.l >= q.m) { S.splice(j, 1); continue; }
            q.x += q.vx; q.y += q.vy; q.vy += .012; q.vx *= .985;
            var a = 1 - q.l / q.m, col = q.t ? '45,226,200' : '243,200,114';
            ctx.fillStyle = 'rgba(' + col + ',' + (a * .14).toFixed(3) + ')';
            ctx.beginPath(); ctx.arc(q.x, q.y, q.r * 4, 0, 6.2832); ctx.fill();
            ctx.fillStyle = 'rgba(' + col + ',' + (a * .95).toFixed(3) + ')';
            ctx.beginPath(); ctx.arc(q.x, q.y, q.r * Math.max(a, .3), 0, 6.2832); ctx.fill();
        }

        var inside = inHero();
        ga += ((inside ? 1 : 0) - ga) * .12;
        if (ga > .01) {
            if (inside) { if (!gx && !gy) { gx = x; gy = y; } gx += (x - gx) * .2; gy += (y - gy) * .2; }
            gr += ((big ? 34 : 17) - gr) * .18;
            ctx.strokeStyle = 'rgba(243,200,114,' + (ga * .75).toFixed(3) + ')';
            ctx.lineWidth = 1.5;
            ctx.beginPath(); ctx.arc(gx, gy, gr, 0, 6.2832); ctx.stroke();
        }

        if (idle > 150 && !S.length && ga < .01) { running = false; return; }
        requestAnimationFrame(loop);
    }

    function kick() { idle = 0; if (!running) requestAnimationFrame(loop); }

    document.addEventListener('mousemove', function (e) {
        px = x; py = y; x = e.clientX; y = e.clientY;
        big = !!(e.target.closest && e.target.closest('.btn,input,.chip,.facts div,a'));
        if (inHero() && px > -9000) spawn(Math.min(4, 1 + Math.floor(Math.hypot(x - px, y - py) / 18)));
        kick();
    }, { passive: true });
    document.addEventListener('scroll', kick, { passive: true });
    root.addEventListener('mouseleave', function () { x = -9999; y = -9999; kick(); });

    cards.forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var r = card.getBoundingClientRect();
            var nx = (e.clientX - r.left) / r.width - .5, ny = (e.clientY - r.top) / r.height - .5;
            card.style.setProperty('--fx', (e.clientX - r.left) + 'px');
            card.style.setProperty('--fy', (e.clientY - r.top) + 'px');
            card.style.setProperty('--cry', (nx * 8).toFixed(2) + 'deg');
            card.style.setProperty('--crx', (-ny * 7).toFixed(2) + 'deg');
        }, { passive: true });
        card.addEventListener('mouseleave', function () {
            card.style.setProperty('--cry', '0deg'); card.style.setProperty('--crx', '0deg');
        });
    });
})();
</script>

<script>
/* PARTS ART: adds a drawn part to each row of the inventory table (SKU + Name columns) */
(function () {
    var tables = [].filter.call(document.querySelectorAll('table'), function (t) {
        var h = t.querySelectorAll('thead th');
        return h.length > 1 && /^sku$/i.test(h[0].textContent.trim()) && /name/i.test(h[1].textContent);
    });
    if (!tables.length) return;

    var ART = {"ram": "<rect x=\"3\" y=\"44\" width=\"94\" height=\"10\" fill=\"#12372a\"/><rect x=\"5\" y=\"46\" width=\"42\" height=\"8\" fill=\"url(#paGold)\"/><rect x=\"52\" y=\"46\" width=\"43\" height=\"8\" fill=\"url(#paGold)\"/>\n<rect x=\"3\" y=\"8\" width=\"94\" height=\"40\" rx=\"4\" fill=\"url(#paBody)\" stroke=\"#4b5361\"/><path d=\"M3 34L30 48H3Z\" fill=\"#e0ae52\" opacity=\".25\"/>\n<rect x=\"8\" y=\"11\" width=\"84\" height=\"3\" rx=\"1.5\" fill=\"url(#paRgb)\" class=\"pa-rgb\"/><text x=\"10\" y=\"31\" font-size=\"9\" font-family=\"IBM Plex Mono, monospace\" font-weight=\"600\" fill=\"#f3c872\">DDR4</text>", "sodimm": "<rect x=\"8\" y=\"12\" width=\"84\" height=\"36\" rx=\"3\" fill=\"url(#paGreen)\" stroke=\"#2b5a46\"/>\n<rect x=\"15\" y=\"18\" width=\"14\" height=\"20\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/><rect x=\"32\" y=\"18\" width=\"14\" height=\"20\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/><rect x=\"54\" y=\"18\" width=\"14\" height=\"20\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/><rect x=\"71\" y=\"18\" width=\"14\" height=\"20\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/>\n<rect x=\"8\" y=\"48\" width=\"36\" height=\"9\" fill=\"url(#paGold)\"/><rect x=\"50\" y=\"48\" width=\"42\" height=\"9\" fill=\"url(#paGold)\"/>\n<circle cx=\"8\" cy=\"30\" r=\"3\" fill=\"#0a0d12\"/><circle cx=\"92\" cy=\"30\" r=\"3\" fill=\"#0a0d12\"/>", "nvme": "<rect x=\"4\" y=\"14\" width=\"92\" height=\"36\" rx=\"3\" fill=\"url(#paGreen)\" stroke=\"#2b5a46\"/><path d=\"M9 18V46\" stroke=\"#e0ae52\" stroke-width=\"10\" stroke-dasharray=\"2.5 2\"/>\n<rect x=\"20\" y=\"20\" width=\"14\" height=\"24\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/><rect x=\"38\" y=\"18\" width=\"26\" height=\"28\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/><rect x=\"68\" y=\"18\" width=\"24\" height=\"28\" rx=\"1.5\" fill=\"#16181d\" stroke=\"#3b4350\"/>\n<text x=\"51\" y=\"35\" text-anchor=\"middle\" font-size=\"7\" font-family=\"IBM Plex Mono, monospace\" font-weight=\"600\" fill=\"#f3c872\">NVMe</text><circle cx=\"96\" cy=\"32\" r=\"3\" fill=\"#0a0d12\"/>", "ssd": "<rect x=\"6\" y=\"6\" width=\"88\" height=\"48\" rx=\"5\" fill=\"url(#paMetal)\" stroke=\"#5b626e\"/><rect x=\"14\" y=\"14\" width=\"72\" height=\"32\" rx=\"3\" fill=\"#12161c\"/>\n<text x=\"22\" y=\"36\" font-family=\"Chakra Petch, sans-serif\" font-size=\"14\" font-weight=\"700\" fill=\"#e0ae52\">SSD</text><text x=\"64\" y=\"33\" font-size=\"8\" font-family=\"IBM Plex Mono, monospace\" fill=\"#8a98a9\">SATA</text>\n<rect x=\"62\" y=\"54\" width=\"30\" height=\"7\" rx=\"1\" fill=\"#1a1d23\" stroke=\"#3b4350\"/><path d=\"M66 57.5H88\" stroke=\"#e0ae52\" stroke-width=\"2\" stroke-dasharray=\"2 1.5\"/>", "hdd": "<rect x=\"6\" y=\"1\" width=\"88\" height=\"62\" rx=\"6\" fill=\"url(#paMetal)\" stroke=\"#5b626e\"/>\n<circle cx=\"38\" cy=\"32\" r=\"25\" fill=\"#12161c\" stroke=\"#3a414d\" stroke-width=\"1.5\"/><circle cx=\"38\" cy=\"32\" r=\"19\" fill=\"none\" stroke=\"#2b313b\" stroke-width=\".7\"/><circle cx=\"38\" cy=\"32\" r=\"13\" fill=\"none\" stroke=\"#2b313b\" stroke-width=\".7\"/>\n<circle cx=\"38\" cy=\"32\" r=\"7\" fill=\"url(#paMetal)\"/><circle cx=\"38\" cy=\"32\" r=\"2\" fill=\"#12161c\"/>\n<g class=\"pa-arm\"><path d=\"M74 18L46 30\" stroke=\"#aeb4bf\" stroke-width=\"3.5\" stroke-linecap=\"round\"/><circle cx=\"74\" cy=\"18\" r=\"4.5\" fill=\"url(#paMetal)\" stroke=\"#5b626e\"/></g>\n<rect x=\"62\" y=\"40\" width=\"26\" height=\"18\" rx=\"2\" fill=\"#f2f4f7\"/><text x=\"75\" y=\"52\" text-anchor=\"middle\" font-size=\"8\" font-family=\"IBM Plex Mono, monospace\" font-weight=\"700\" fill=\"#1a1204\">1TB</text>\n<circle cx=\"11\" cy=\"6\" r=\"1.8\" fill=\"#6b7280\"/><circle cx=\"89\" cy=\"6\" r=\"1.8\" fill=\"#6b7280\"/><circle cx=\"11\" cy=\"58\" r=\"1.8\" fill=\"#6b7280\"/><circle cx=\"89\" cy=\"58\" r=\"1.8\" fill=\"#6b7280\"/>", "battery": "<rect x=\"4\" y=\"12\" width=\"90\" height=\"40\" rx=\"5\" fill=\"url(#paBody)\" stroke=\"#4b5361\"/><path d=\"M28 14V50M52 14V50M76 14V50\" stroke=\"#000\" stroke-opacity=\".45\"/><path d=\"M10 13.5H88\" stroke=\"#fff\" stroke-opacity=\".2\"/>\n<rect x=\"92\" y=\"22\" width=\"6\" height=\"20\" rx=\"1\" fill=\"#0f1319\"/><path d=\"M95 24V40\" stroke=\"#e0ae52\" stroke-width=\"3\" stroke-dasharray=\"2.5 1.5\"/>\n<rect x=\"11\" y=\"22\" width=\"40\" height=\"20\" rx=\"2\" fill=\"#1c2129\"/><text x=\"31\" y=\"35\" text-anchor=\"middle\" font-size=\"8\" font-family=\"IBM Plex Mono, monospace\" font-weight=\"600\" fill=\"#f3c872\">Li-ion</text>\n<rect x=\"58\" y=\"26\" width=\"7\" height=\"12\" rx=\"1\" fill=\"#3df08f\"/><rect x=\"67\" y=\"26\" width=\"7\" height=\"12\" rx=\"1\" fill=\"#3df08f\"/><rect x=\"76\" y=\"26\" width=\"7\" height=\"12\" rx=\"1\" fill=\"#3df08f\" class=\"pa-led\"/>", "charger": "<rect x=\"4\" y=\"14\" width=\"52\" height=\"36\" rx=\"6\" fill=\"url(#paBody)\" stroke=\"#4b5361\"/><path d=\"M10 15.5H50\" stroke=\"#fff\" stroke-opacity=\".2\"/>\n<text x=\"30\" y=\"37\" text-anchor=\"middle\" font-family=\"Chakra Petch, sans-serif\" font-size=\"12\" font-weight=\"700\" fill=\"#e0ae52\">65W</text><circle cx=\"13\" cy=\"22\" r=\"2\" fill=\"#3df08f\" class=\"pa-led\"/>\n<path d=\"M56 32C70 32 70 50 84 50\" fill=\"none\" stroke=\"#1a1d23\" stroke-width=\"4.5\" stroke-linecap=\"round\"/><path d=\"M56 31C70 31 70 49 84 49\" fill=\"none\" stroke=\"#3b4350\" stroke-width=\"1.2\" stroke-linecap=\"round\"/>\n<rect x=\"82\" y=\"45\" width=\"14\" height=\"9\" rx=\"2\" fill=\"url(#paMetal)\"/><rect x=\"95\" y=\"47.5\" width=\"4\" height=\"4\" fill=\"#222\"/>", "keyboard": "<rect x=\"3\" y=\"8\" width=\"94\" height=\"48\" rx=\"5\" fill=\"url(#paBody)\" stroke=\"#4b5361\"/><rect x=\"6\" y=\"11\" width=\"88\" height=\"34\" rx=\"3\" fill=\"url(#paRgb)\" opacity=\".4\" class=\"pa-rgb\"/><rect x=\"7.0\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"15.8\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"24.6\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"33.4\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"42.2\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"51.0\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"59.8\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"68.6\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"77.4\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"86.2\" y=\"13.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"7.0\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"15.8\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"24.6\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"33.4\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"42.2\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"51.0\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"59.8\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"68.6\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"77.4\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"86.2\" y=\"23.5\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"7.0\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"15.8\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"24.6\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"33.4\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"42.2\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"51.0\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"59.8\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"68.6\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"77.4\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"86.2\" y=\"34.0\" width=\"7.4\" height=\"8\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/><rect x=\"24\" y=\"46\" width=\"48\" height=\"7\" rx=\"1.6\" fill=\"#1b2027\" stroke=\"#4b5361\" stroke-width=\".5\"/>", "lcd": "<rect x=\"4\" y=\"5\" width=\"92\" height=\"48\" rx=\"3\" fill=\"#0c0e12\" stroke=\"#4b5361\"/><g class=\"pa-scr\"><rect x=\"8\" y=\"9\" width=\"84\" height=\"40\" rx=\"1\" fill=\"url(#paScreen)\"/><path d=\"M8 9H50L26 49H8Z\" fill=\"#fff\" opacity=\".08\"/></g>\n<rect x=\"34\" y=\"53\" width=\"32\" height=\"8\" rx=\"1\" fill=\"#e0ae52\"/><path d=\"M36 57H64\" stroke=\"#7a5f26\" stroke-width=\"3.5\" stroke-dasharray=\"1 1.2\"/>", "cable": "<path d=\"M16 46C18 18 50 58 84 18\" fill=\"none\" stroke=\"#9c2f2f\" stroke-width=\"5.5\" stroke-linecap=\"round\"/><path d=\"M16 45C18 17 50 57 84 17\" fill=\"none\" stroke=\"#e45b5b\" stroke-width=\"1.6\" stroke-linecap=\"round\" opacity=\".8\"/>\n<rect x=\"5\" y=\"42\" width=\"18\" height=\"13\" rx=\"2\" fill=\"#15181d\" stroke=\"#3b4350\"/><path d=\"M8 48.5H20\" stroke=\"#e0ae52\" stroke-width=\"3\" stroke-dasharray=\"1.6 1.2\"/>\n<rect x=\"77\" y=\"9\" width=\"18\" height=\"13\" rx=\"2\" fill=\"#15181d\" stroke=\"#3b4350\"/><path d=\"M80 15.5H92\" stroke=\"#e0ae52\" stroke-width=\"3\" stroke-dasharray=\"1.6 1.2\"/>", "paste": "<rect x=\"12\" y=\"22\" width=\"56\" height=\"20\" rx=\"4\" fill=\"#dfe3ea\" fill-opacity=\".18\" stroke=\"#8b929e\"/><rect x=\"15\" y=\"25\" width=\"38\" height=\"14\" rx=\"2\" fill=\"url(#paPaste)\"/>\n<rect x=\"56\" y=\"22\" width=\"6\" height=\"20\" fill=\"#e0ae52\" opacity=\".85\"/><rect x=\"2\" y=\"28\" width=\"10\" height=\"8\" rx=\"1\" fill=\"url(#paMetal)\"/><rect x=\"0\" y=\"30\" width=\"3\" height=\"4\" fill=\"#8b929e\"/>\n<path d=\"M68 26L86 31V33L68 38Z\" fill=\"#cfd4dc\" stroke=\"#8b929e\" stroke-width=\".6\"/><rect x=\"86\" y=\"30\" width=\"8\" height=\"4\" rx=\"1\" fill=\"#e0ae52\"/><ellipse cx=\"97\" cy=\"32\" rx=\"2.6\" ry=\"2\" fill=\"#aab2bd\"/>", "psu": "<rect x=\"6\" y=\"6\" width=\"88\" height=\"52\" rx=\"5\" fill=\"url(#paBody)\" stroke=\"#4b5361\"/><circle cx=\"50\" cy=\"32\" r=\"23\" fill=\"#080a0e\" stroke=\"#46505f\" stroke-width=\"1.5\"/>\n<g transform=\"translate(50 32) scale(.38)\"><g class=\"pa-spin\"><use href=\"#paRotor\"/></g></g><circle cx=\"50\" cy=\"32\" r=\"19\" fill=\"none\" stroke=\"#5a6270\" stroke-width=\".7\" opacity=\".6\"/><circle cx=\"50\" cy=\"32\" r=\"5\" fill=\"url(#paMetal)\"/>\n<text x=\"11\" y=\"17\" font-size=\"7\" font-family=\"IBM Plex Mono, monospace\" font-weight=\"600\" fill=\"#f3c872\">500W</text>"};

    document.body.insertAdjacentHTML('beforeend',
        '<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs><linearGradient id="paMetal" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#8b929e"/><stop offset=".5" stop-color="#dfe3ea"/><stop offset="1" stop-color="#7d8490"/></linearGradient><linearGradient id="paBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b4350"/><stop offset="1" stop-color="#12161c"/></linearGradient><linearGradient id="paGreen" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1f5f46"/><stop offset="1" stop-color="#0f2a22"/></linearGradient><linearGradient id="paRgb" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#2de2c8"/><stop offset=".35" stop-color="#f3c872"/><stop offset=".7" stop-color="#ff5c9a"/><stop offset="1" stop-color="#7a5cff"/></linearGradient><linearGradient id="paScreen" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#1c4a7a"/><stop offset=".55" stop-color="#0b1220"/><stop offset="1" stop-color="#4a2f8a"/></linearGradient><linearGradient id="paPaste" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d5dae2"/><stop offset="1" stop-color="#8e96a3"/></linearGradient><radialGradient id="paBlade" cx=".3" cy=".3" r=".9"><stop offset="0" stop-color="#4a5260"/><stop offset="1" stop-color="#171b22"/></radialGradient><pattern id="paGold" width="6" height="14" patternUnits="userSpaceOnUse"><rect width="4" height="14" fill="#e0ae52"/></pattern><g id="paRotor"><path id="paBl" d="M3 -9C17 -14 35 -25 44 -43C25 -47 8 -39 -3 -23Z" fill="url(#paBlade)" stroke="#6b7482" stroke-opacity=".5" stroke-width=".6"/><use href="#paBl" transform="rotate(40)"/><use href="#paBl" transform="rotate(80)"/><use href="#paBl" transform="rotate(120)"/><use href="#paBl" transform="rotate(160)"/><use href="#paBl" transform="rotate(200)"/><use href="#paBl" transform="rotate(240)"/><use href="#paBl" transform="rotate(280)"/><use href="#paBl" transform="rotate(320)"/></g></defs></svg>');

    function kind(s) {
        s = s.toLowerCase();
        if (/so-?dimm/.test(s)) return 'sodimm';
        if (/\bram\b|ddr/.test(s)) return 'ram';
        if (/nvme|m\.2/.test(s)) return 'nvme';
        if (/ssd/.test(s)) return 'ssd';
        if (/hdd|hard ?drive|hard disk/.test(s)) return 'hdd';
        if (/batter/.test(s)) return 'battery';
        if (/charger|adapter/.test(s)) return 'charger';
        if (/keyboard/.test(s)) return 'keyboard';
        if (/lcd|screen|display panel/.test(s)) return 'lcd';
        if (/power supply|psu/.test(s)) return 'psu';
        if (/cable/.test(s)) return 'cable';
        if (/paste|thermal/.test(s)) return 'paste';
        return '';
    }

    tables.forEach(function (t) {
        [].forEach.call(t.querySelectorAll('tbody tr'), function (tr) {
            var c = tr.children;
            if (c.length < 2 || c[1].querySelector('.pt-name')) return;
            var k = kind(c[0].textContent + ' ' + c[1].textContent);
            if (!k) return;
            var wrap = document.createElement('div');
            wrap.className = 'pt-name';
            wrap.innerHTML = '<svg class="pa" viewBox="0 0 100 64" preserveAspectRatio="xMidYMid meet" aria-hidden="true">' + ART[k] + '</svg>';
            var txt = document.createElement('span');
            while (c[1].firstChild) txt.appendChild(c[1].firstChild);
            wrap.appendChild(txt);
            c[1].appendChild(wrap);
        });
    });
})();
</script>
</body>
</html>