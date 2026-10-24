@extends('layouts.app')
@section('title', 'Check repair status')

@section('hero')
<style>
/* Page-specific: small static line illustration */
.gpu { margin-top:36px; max-width:320px; color:#3b4047; }
.gpu svg { display:block; width:100%; height:auto; }
@media (max-width:800px) { .gpu { margin-top:24px; max-width:240px; } }
</style>
<section class="hero">
<div class="hero-in">
<div>
<h1>Track your repair.</h1>
<p>Enter the ticket number from your claim slip to see where your device is right now.</p>
<div class="gpu" aria-hidden="true">
<svg viewBox="0 0 320 110" fill="none" stroke="currentColor" stroke-width="1.5">
<rect x="2" y="2" width="316" height="84" rx="8"/>
<circle cx="100" cy="44" r="30"/>
<circle cx="100" cy="44" r="16"/>
<circle cx="100" cy="44" r="3" fill="#c8a24a" stroke="none"/>
<circle cx="220" cy="44" r="30"/>
<circle cx="220" cy="44" r="16"/>
<circle cx="220" cy="44" r="3" fill="#c8a24a" stroke="none"/>
<path d="M60 86v10h200V86"/>
<path d="M24 14h56" stroke="#c8a24a"/>
</svg>
</div>
</div>
<div class="chip">
<div class="slip-top"><strong>Claim slip</strong><span>Example: RT-2026-0001</span></div>
<label class="slip-label" for="ticket">Ticket number</label>
<form method="GET" action="{{ route('status') }}" class="actions">
<input class="mono" id="ticket" name="ticket" value="{{ request('ticket') }}" placeholder="RT-2026-0001" required maxlength="20" autocomplete="off" autocapitalize="characters" spellcheck="false">
<button class="btn">Check</button>
</form>
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
<div><strong>Memory</strong><span class="muted">DDR4 8GB sticks for desktops and laptops.</span></div>
<div><strong>SSD storage</strong><span class="muted">SATA and NVMe drives, 256GB to 512GB.</span></div>
<div><strong>Hard drives</strong><span class="muted">1TB 2.5 inch drives for laptops and backups.</span></div>
<div><strong>Power</strong><span class="muted">500W power supplies, laptop chargers and batteries.</span></div>
</div>
    @endif
@endsection