@extends('layouts.app')
@section('title', 'Check repair status')
@section('content')
<div class="card" style="max-width:560px;margin:auto">
    <h1>Check your repair status</h1>
    <p class="muted">Enter the ticket number from your claim slip (example: RT-2026-0001).</p>
    <form method="GET" action="{{ route('status') }}" class="actions">
        <input name="ticket" value="{{ request('ticket') }}" placeholder="RT-2026-0001" style="flex:1" required maxlength="20">
        <button class="btn">Check</button>
    </form>

    @if ($searched && ! $ticket)
        <div class="flash err" style="margin-top:16px">No repair found for that ticket number. Please check it and try again.</div>
    @endif

    @if ($ticket)
        <hr style="border:0;border-top:1px solid var(--line);margin:18px 0">
        <h2>{{ $ticket->ticket_number }}</h2>
        <p>{{ $ticket->brand }} {{ $ticket->model }} ({{ $ticket->device_type }})</p>
        <p>Current status: <span class="badge" style="font-size:14px">{{ $ticket->status->name }}</span></p>
        <ul class="tl">
            @foreach ($ticket->histories as $h)
                <li><strong>{{ $h->status->name }}</strong> <span class="muted">· {{ $h->created_at?->format('M d, Y h:i A') }}</span></li>
            @endforeach
        </ul>
        @if ($ticket->status->name === 'Ready for Pickup')
            <div class="flash ok" style="margin-top:16px">Your device is ready. Please bring your claim slip when picking it up.</div>
        @endif
    @endif
</div>
@endsection
