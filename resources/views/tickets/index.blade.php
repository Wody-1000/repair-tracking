@extends('layouts.app')
@section('title', 'Tickets')
@section('content')
<div class="actions" style="justify-content:space-between;margin-bottom:12px">
    <h1 style="margin:0">Repair tickets</h1>
    @if (auth()->user()->hasRole('admin', 'front_desk'))
        <a class="btn" href="{{ route('tickets.create') }}">+ New ticket</a>
    @endif
</div>

<form method="GET" class="card actions">
    <input name="q" value="{{ request('q') }}" placeholder="Search ticket no., customer, phone, brand, model" style="flex:2;min-width:220px">
    <select name="status" style="flex:1;min-width:160px">
        <option value="">All statuses</option>
        @foreach ($statuses as $s)
            <option value="{{ $s->id }}" @selected((int) request('status') === $s->id)>{{ $s->name }}</option>
        @endforeach
    </select>
    <button class="btn">Filter</button>
    <a class="btn sec" href="{{ route('tickets.index') }}">Reset</a>
</form>

<div class="card">
    <table>
        <thead><tr><th>Ticket</th><th>Customer</th><th>Device</th><th>Status</th><th>Technician</th><th>Received</th></tr></thead>
        <tbody>
        @forelse ($tickets as $t)
            <tr>
                <td><a href="{{ route('tickets.show', $t) }}"><strong>{{ $t->ticket_number }}</strong></a></td>
                <td>{{ $t->customer->name }}<br><span class="muted">{{ $t->customer->phone }}</span></td>
                <td>{{ $t->brand }} {{ $t->model }}<br><span class="muted">{{ $t->device_type }}</span></td>
                <td><span class="badge">{{ $t->status->name }}</span></td>
                <td>{{ $t->technician?->name ?? '-' }}</td>
                <td>{{ $t->created_at?->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No tickets found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pager">{{ $tickets->links() }}</div>
</div>
@endsection
