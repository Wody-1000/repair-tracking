@extends('layouts.app')
@section('title', 'Check repair status')

@section('hero')
<style>
/* ===== Showcase: hardware art ===== */
.gpu { position:relative; margin-top:36px; max-width:560px; transform:perspective(1000px) rotateY(var(--gy,0deg)) rotateX(var(--gx,0deg)); transition:transform .2s ease-out; animation:gpu-in 1s .9s cubic-bezier(.2,.8,.2,1) backwards; }
.gpu svg { display:block; width:100%; height:auto; overflow:visible; filter:drop-shadow(0 28px 34px rgba(0,0,0,.65)); }
.gpu::before { content:""; position:absolute; left:8%; right:8%; bottom:-4%; height:46%; z-index:-1; background:radial-gradient(closest-side, rgba(45,226,200,.3), rgba(224,174,82,.12) 55%, transparent); filter:blur(18px); animation:gpu-pulse 3.2s ease-in-out infinite; }
@keyframes gpu-in { from { opacity:0; transform:perspective(1000px) translateX(-70px) rotateY(-35deg); } to { opacity:1; transform:perspective(1000px); } }
@keyframes gpu-pulse { 50% { opacity:.5; } }
.spin { transform-origin:0 0; animation:rt-spin 1.4s linear infinite; }
.gpu:hover .spin, .parts div:hover .spin { animation-duration:.45s; }
@keyframes rt-spin { to { transform:rotate(360deg); } }
.rgb { animation:rt-hue 7s linear infinite; }
@keyframes rt-hue { to { filter:hue-rotate(360deg); } }
.arm { transform-origin:164px 40px; transition:transform .6s cubic-bezier(.3,.7,.2,1); }
.parts div:hover .arm { transform:rotate(-14deg); }
.stock-title { margin:58px 0 0; }
.parts { margin-top:18px; }
.parts div svg { display:block; width:100%; height:92px; margin-bottom:14px; }
@media (max-width:800px) { .gpu { margin-top:26px; } }
@media (prefers-reduced-motion:reduce) { .spin, .rgb, .gpu, .gpu::before { animation:none; } .gpu { transform:none !important; } }
</style>
<section class="hero">
<div class="hero-in">
<div>
<h1>Track your repair.</h1>
<p>Enter the ticket number from your claim slip to see where your device is right now.</p>
<div class="gpu" aria-hidden="true">
<svg viewBox="0 0 560 240" preserveAspectRatio="xMidYMid meet">
<defs>
<linearGradient id="gShroud" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3a414d"/><stop offset=".5" stop-color="#1c2129"/><stop offset="1" stop-color="#0d1015"/></linearGradient>
<linearGradient id="gMetal" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#8b929e"/><stop offset=".5" stop-color="#dfe3ea"/><stop offset="1" stop-color="#7d8490"/></linearGradient>
<linearGradient id="gRgb" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#2de2c8"/><stop offset=".35" stop-color="#f3c872"/><stop offset=".7" stop-color="#ff5c9a"/><stop offset="1" stop-color="#7a5cff"/></linearGradient>
<radialGradient id="gBlade" cx=".3" cy=".3" r=".9"><stop offset="0" stop-color="#4a5260"/><stop offset="1" stop-color="#171b22"/></radialGradient>
<pattern id="gGold" width="6" height="14" patternUnits="userSpaceOnUse"><rect width="4" height="14" fill="#e0ae52"/></pattern>
<pattern id="gFins" width="5" height="8" patternUnits="userSpaceOnUse"><rect width="2" height="8" fill="#5a6270"/></pattern>
<filter id="gGlow" x="-20%" y="-300%" width="140%" height="700%"><feGaussianBlur stdDeviation="3"/></filter>
<g id="gRotor">
<path id="gBl" d="M3 -9C17 -14 35 -25 44 -43C25 -47 8 -39 -3 -23Z" fill="url(#gBlade)" stroke="#6b7482" stroke-opacity=".5" stroke-width=".6"/>
<use href="#gBl" transform="rotate(40)"/><use href="#gBl" transform="rotate(80)"/><use href="#gBl" transform="rotate(120)"/><use href="#gBl" transform="rotate(160)"/><use href="#gBl" transform="rotate(200)"/><use href="#gBl" transform="rotate(240)"/><use href="#gBl" transform="rotate(280)"/><use href="#gBl" transform="rotate(320)"/>
</g>
</defs>
<ellipse cx="280" cy="224" rx="250" ry="9" fill="#000" opacity=".55" filter="url(#gGlow)"/>
<rect x="0" y="28" width="12" height="168" rx="2" fill="url(#gMetal)"/>
<path d="M3 50h6M3 60h6M3 70h6M3 80h6" stroke="#2a2f38" stroke-width="3"/>
<rect x="70" y="190" width="100" height="13" fill="url(#gGold)"/><rect x="180" y="190" width="220" height="13" fill="url(#gGold)"/>
<rect x="40" y="26" width="430" height="10" rx="2" fill="url(#gFins)"/>
<rect x="468" y="22" width="44" height="14" rx="2" fill="#12151b" stroke="#3b4350"/><path d="M474 29h32" stroke="#e0ae52" stroke-width="3" stroke-dasharray="3 3"/>
<rect x="12" y="34" width="520" height="156" rx="14" fill="url(#gShroud)" stroke="#4b5361" stroke-width="1.2"/>
<path d="M26 35H518" stroke="#fff" stroke-opacity=".18"/>
<path d="M44 35L24 92" stroke="#e0ae52" stroke-width="2" opacity=".8"/>
<path d="M500 190L532 150V190Z" fill="#e0ae52" opacity=".16"/>
<rect x="526" y="48" width="8" height="128" rx="3" fill="#0b0e13"/>
<rect x="70" y="37" width="420" height="6" rx="3" fill="url(#gRgb)" opacity=".7" filter="url(#gGlow)" class="rgb"/>
<rect x="70" y="38" width="420" height="3" rx="1.5" fill="url(#gRgb)" class="rgb"/>
<g transform="translate(132 112)"><circle r="58" fill="#080a0e" stroke="#46505f" stroke-width="2"/><circle r="52" fill="#0c1016"/><g class="spin"><use href="#gRotor"/></g><circle r="14" fill="url(#gMetal)"/><circle r="14" fill="none" stroke="url(#gRgb)" stroke-width="3" class="rgb"/><circle r="5" fill="#0c1016"/></g>
<g transform="translate(272 112)"><circle r="58" fill="#080a0e" stroke="#46505f" stroke-width="2"/><circle r="52" fill="#0c1016"/><g class="spin"><use href="#gRotor"/></g><circle r="14" fill="url(#gMetal)"/><circle r="14" fill="none" stroke="url(#gRgb)" stroke-width="3" class="rgb"/><circle r="5" fill="#0c1016"/></g>
<g transform="translate(412 112)"><circle r="58" fill="#080a0e" stroke="#46505f" stroke-width="2"/><circle r="52" fill="#0c1016"/><g class="spin"><use href="#gRotor"/></g><circle r="14" fill="url(#gMetal)"/><circle r="14" fill="none" stroke="url(#gRgb)" stroke-width="3" class="rgb"/><circle r="5" fill="#0c1016"/></g>
<text x="44" y="180" font-family="Chakra Petch, sans-serif" font-size="13" font-weight="700" letter-spacing="2" fill="#e0ae52">GAMING OC</text>
<text x="146" y="180" font-family="IBM Plex Mono, monospace" font-size="10" fill="#8a98a9">16GB GDDR6X</text>
</svg>
</div>
</div>
<div class="board">
<svg class="tr l" viewBox="0 0 48 100" preserveAspectRatio="none" aria-hidden="true">
<path d="M0 20H14L24 30H48"/><path d="M0 44H30L38 52H48"/><path d="M0 68H10L20 58H48"/><path d="M0 86H22L30 78H48"/>
<path class="pad" d="M0 20h0M0 44h0M0 68h0M0 86h0"/>
</svg>
<svg class="tr r" viewBox="0 0 48 100" preserveAspectRatio="none" aria-hidden="true">
<path d="M0 26H18L28 36H48"/><path d="M0 50H26L34 42H48"/><path d="M0 72H12L22 62H48"/><path d="M0 90H30L38 82H48"/>
<path class="pad" d="M0 26h0M0 50h0M0 72h0M0 90h0"/>
</svg>
<div class="chip">
<div class="slip-top"><strong>Claim slip</strong><span>Example: RT-2026-0001</span></div>
<form method="GET" action="{{ route('status') }}" class="actions">
<input class="mono" name="ticket" value="{{ request('ticket') }}" placeholder="RT-2026-0001" style="flex:1" required maxlength="20" autocomplete="off" autocapitalize="characters" spellcheck="false" aria-label="Ticket number">
<button class="btn">Check</button>
</form>
</div>
</div>
</div>
</section>
<script>
(function () {
    var g = document.querySelector('.gpu');
    if (!g || !matchMedia('(pointer:fine)').matches || matchMedia('(prefers-reduced-motion:reduce)').matches) return;
    var q = false, mx = 0, my = 0;
    addEventListener('mousemove', function (e) {
        mx = e.clientX; my = e.clientY;
        if (q) return; q = true;
        requestAnimationFrame(function () {
            q = false;
            var r = g.getBoundingClientRect();
            var nx = Math.max(-1, Math.min(1, (mx - (r.left + r.width / 2)) / (innerWidth / 2)));
            var ny = Math.max(-1, Math.min(1, (my - (r.top + r.height / 2)) / (innerHeight / 2)));
            g.style.setProperty('--gy', (nx * 14).toFixed(1) + 'deg');
            g.style.setProperty('--gx', (-ny * 8).toFixed(1) + 'deg');
        });
    }, { passive: true });
})();
</script>
@endsection

@section('content')
    @if ($searched && ! $ticket)
<div class="flash err" role="alert">No repair found for that ticket number. Please check it and try again.</div>
    @endif

    @if ($ticket)
<div class="chip">
<div class="slip-head">
<div>
<div class="muted">Ticket number</div>
<h2 class="mono">{{ $ticket->ticket_number }}</h2>
</div>
<div>
<div class="muted">Current status</div>
<span class="badge big">{{ $ticket->status->name }}</span>
</div>
</div>
<div class="slip-body">
<p class="device">{{ $ticket->brand }} {{ $ticket->model }} ({{ $ticket->device_type }})</p>
<ul class="tl">
                    @foreach ($ticket->histories as $h)
<li><strong>{{ $h->status->name }}</strong> <span class="muted">· {{ $h->created_at?->format('M d, Y h:i A') }}</span></li>
                    @endforeach
</ul>
                @if ($ticket->status->name === 'Ready for Pickup')
<div class="flash ok" style="margin:8px 0 0">Your device is ready. Please bring your claim slip when picking it up.</div>
                @endif
</div>
</div>
    @elseif (! $searched)
<div class="facts">
<div><strong>Find your ticket number</strong><span class="muted">It is printed on the claim slip you received when you dropped off your device.</span></div>
<div><strong>See every update</strong><span class="muted">Each status change is listed with its date and time.</span></div>
<div><strong>Ready for pickup</strong><span class="muted">When your device is ready, bring your claim slip to collect it.</span></div>
</div>

<h2 class="stock-title">Parts we keep in stock</h2>
<div class="facts parts">
<div>
<svg viewBox="0 0 240 90" preserveAspectRatio="xMinYMid meet" aria-hidden="true">
<defs><linearGradient id="rBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b4350"/><stop offset="1" stop-color="#12161c"/></linearGradient></defs>
<rect x="4" y="66" width="232" height="18" fill="#12372a"/>
<rect x="8" y="70" width="100" height="14" fill="url(#gGold)"/><rect x="118" y="70" width="114" height="14" fill="url(#gGold)"/>
<rect x="4" y="6" width="232" height="66" rx="6" fill="url(#rBody)" stroke="#4b5361"/>
<path d="M4 44L60 72H4Z" fill="#e0ae52" opacity=".25"/>
<rect x="14" y="10" width="212" height="5" rx="2.5" fill="url(#gRgb)" class="rgb"/>
<text x="20" y="42" font-family="IBM Plex Mono, monospace" font-size="13" font-weight="600" fill="#f3c872">DDR4</text>
<text x="20" y="58" font-family="IBM Plex Mono, monospace" font-size="9" fill="#8a98a9">8GB</text>
</svg>
<strong>Memory</strong><span class="muted">DDR4 8GB sticks for desktops and laptops.</span></div>
<div>
<svg viewBox="0 0 240 90" preserveAspectRatio="xMinYMid meet" aria-hidden="true">
<rect x="4" y="12" width="232" height="66" rx="5" fill="#10281f" stroke="#2b5a46"/>
<path d="M15 15V75" stroke="#e0ae52" stroke-width="20" stroke-dasharray="3.5 2.5"/>
<rect x="40" y="26" width="30" height="38" rx="2" fill="#16181d" stroke="#3b4350"/>
<rect x="82" y="22" width="70" height="46" rx="2" fill="#16181d" stroke="#3b4350"/>
<rect x="160" y="22" width="60" height="46" rx="2" fill="#16181d" stroke="#3b4350"/>
<text x="117" y="49" text-anchor="middle" font-family="IBM Plex Mono, monospace" font-size="11" font-weight="600" fill="#f3c872">NVMe</text>
<text x="190" y="49" text-anchor="middle" font-family="IBM Plex Mono, monospace" font-size="11" font-weight="600" fill="#f3c872">512GB</text>
<circle cx="236" cy="45" r="7" fill="#0a0d12"/>
</svg>
<strong>SSD storage</strong><span class="muted">SATA and NVMe drives, 256GB to 512GB.</span></div>
<div>
<svg viewBox="0 0 200 140" preserveAspectRatio="xMinYMid meet" aria-hidden="true">
<rect x="4" y="4" width="192" height="132" rx="10" fill="url(#gMetal)" stroke="#5b626e"/>
<rect x="12" y="12" width="176" height="116" rx="6" fill="none" stroke="#fff" stroke-opacity=".4"/>
<circle cx="84" cy="70" r="54" fill="#12161c" stroke="#3a414d" stroke-width="3"/>
<circle cx="84" cy="70" r="44" fill="none" stroke="#2b313b"/><circle cx="84" cy="70" r="34" fill="none" stroke="#2b313b"/><circle cx="84" cy="70" r="24" fill="none" stroke="#2b313b"/>
<circle cx="84" cy="70" r="14" fill="url(#gMetal)"/><circle cx="84" cy="70" r="4" fill="#12161c"/>
<g class="arm"><path d="M164 40L98 66" stroke="#aeb4bf" stroke-width="7" stroke-linecap="round"/><circle cx="164" cy="40" r="9" fill="url(#gMetal)" stroke="#5b626e"/></g>
<rect x="138" y="88" width="48" height="38" rx="3" fill="#f2f4f7"/>
<text x="162" y="105" text-anchor="middle" font-family="IBM Plex Mono, monospace" font-size="11" font-weight="700" fill="#1a1204">1TB</text>
<text x="162" y="118" text-anchor="middle" font-family="IBM Plex Mono, monospace" font-size="8" fill="#555">2.5 inch</text>
<circle cx="17" cy="17" r="3.5" fill="#6b7280"/><circle cx="183" cy="17" r="3.5" fill="#6b7280"/><circle cx="17" cy="123" r="3.5" fill="#6b7280"/><circle cx="183" cy="123" r="3.5" fill="#6b7280"/>
</svg>
<strong>Hard drives</strong><span class="muted">1TB 2.5 inch drives for laptops and backups.</span></div>
<div>
<svg viewBox="0 0 200 140" preserveAspectRatio="xMinYMid meet" aria-hidden="true">
<rect x="4" y="10" width="192" height="120" rx="8" fill="url(#rBody)" stroke="#4b5361"/>
<circle cx="100" cy="70" r="50" fill="#080a0e" stroke="#46505f" stroke-width="2"/>
<g transform="translate(100 70) scale(.85)"><g class="spin"><use href="#gRotor"/></g></g>
<circle cx="100" cy="70" r="40" fill="none" stroke="#5a6270" stroke-width="1.2" opacity=".6"/><circle cx="100" cy="70" r="28" fill="none" stroke="#5a6270" stroke-width="1.2" opacity=".6"/>
<circle cx="100" cy="70" r="10" fill="url(#gMetal)"/>
<text x="12" y="28" font-family="Chakra Petch, sans-serif" font-size="11" font-weight="700" fill="#e0ae52">500W</text>
</svg>
<strong>Power</strong><span class="muted">500W power supplies, laptop chargers and batteries.</span></div>
</div>
    @endif
@endsection