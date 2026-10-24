@extends('layouts.app')
@section('title', 'Check repair status')

@section('hero')
<style>
/* Hero art: one static graphics card, fans turn slowly */
.gpu { margin-top:32px; max-width:480px; }
.gpu svg { display:block; width:100%; height:auto; filter:drop-shadow(0 18px 22px rgba(0,0,0,.55)); }
.spin { transform-origin:0 0; animation:rt-spin 6s linear infinite; }
@keyframes rt-spin { to { transform:rotate(360deg); } }

/* Parts cards */
.stock-title { margin:48px 0 0; }
.parts { margin-top:16px; }
.spec { display:inline-block; margin-bottom:12px; padding:4px 10px; border:1px solid #3b4350; border-radius:4px; background:#12161c; color:#f3c872; font-family:'IBM Plex Mono', monospace; font-size:13px; font-weight:600; }

@media (max-width:800px) { .gpu { margin-top:24px; } }
@media (prefers-reduced-motion:reduce) { .spin { animation:none; } }
</style>
<section class="hero">
<div class="hero-in">
<div>
<h1>Track your repair.</h1>
<p>Enter the ticket number from your claim slip to see where your device is right now.</p>
<div class="gpu" aria-hidden="true">
<svg viewBox="0 0 480 170" preserveAspectRatio="xMidYMid meet">
<defs>
<linearGradient id="gShroud" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3a414d"/><stop offset="1" stop-color="#0d1015"/></linearGradient>
<g id="gRotor"><path d="M0 -34V34M-29 -17L29 17M-29 17L29 -17" stroke="#5a6270" stroke-width="7" stroke-linecap="round"/></g>
</defs>
<rect x="6" y="14" width="468" height="130" rx="12" fill="url(#gShroud)" stroke="#4b5361" stroke-width="1.2"/>
<rect x="40" y="14" width="400" height="4" rx="2" fill="#e0ae52"/>
<g transform="translate(130 80)"><circle r="46" fill="#080a0e" stroke="#46505f" stroke-width="2"/><g class="spin"><use href="#gRotor"/></g><circle r="9" fill="#e0ae52"/></g>
<g transform="translate(260 80)"><circle r="46" fill="#080a0e" stroke="#46505f" stroke-width="2"/><g class="spin"><use href="#gRotor"/></g><circle r="9" fill="#e0ae52"/></g>
<g transform="translate(390 80)"><circle r="46" fill="#080a0e" stroke="#46505f" stroke-width="2"/><g class="spin"><use href="#gRotor"/></g><circle r="9" fill="#e0ae52"/></g>
<text x="28" y="136" font-family="Chakra Petch, sans-serif" font-size="12" font-weight="700" letter-spacing="2" fill="#e0ae52">GAMING OC</text>
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
<div><span class="spec">DDR4 8GB</span><strong>Memory</strong><span class="muted">DDR4 8GB sticks for desktops and laptops.</span></div>
<div><span class="spec">256GB to 512GB</span><strong>SSD storage</strong><span class="muted">SATA and NVMe drives, 256GB to 512GB.</span></div>
<div><span class="spec">1TB 2.5 inch</span><strong>Hard drives</strong><span class="muted">1TB 2.5 inch drives for laptops and backups.</span></div>
<div><span class="spec">500W</span><strong>Power</strong><span class="muted">500W power supplies, laptop chargers and batteries.</span></div>
</div>
    @endif
@endsection