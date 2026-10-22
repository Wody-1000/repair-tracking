@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1>Dashboard</h1>

@if ($lowStock->isNotEmpty())
    <div class="flash err">
        <strong>Low stock warning:</strong>
        @foreach ($lowStock as $p) {{ $p->name }} ({{ $p->quantity }} left){{ ! $loop->last ? ',' : '' }} @endforeach
        · <a href="{{ route('parts.index', ['low' => 1]) }}">view</a>
    </div>
@endif

<div class="grid">
    <div class="card">
        <h2>Tickets by status</h2>
        <table>
            @foreach ($statuses as $s)
                <tr><td>{{ $s->name }}</td><td class="num"><a href="{{ route('tickets.index', ['status' => $s->id]) }}">{{ $s->tickets_count }}</a></td></tr>
            @endforeach
        </table>
    </div>
    <div class="card">
        <h2>Recent tickets</h2>
        <table>
            @forelse ($recent as $t)
                <tr>
                    <td><a href="{{ route('tickets.show', $t) }}">{{ $t->ticket_number }}</a><br><span class="muted">{{ $t->customer->name }}</span></td>
                    <td class="num"><span class="badge">{{ $t->status->name }}</span></td>
                </tr>
            @empty
                <tr><td class="muted">No tickets yet.</td></tr>
            @endforelse
        </table>
    </div>
</div>

@if (auth()->user()->hasRole('admin', 'front_desk'))
    <a class="btn" href="{{ route('tickets.create') }}">+ New repair ticket</a>
@endif
@endsection
