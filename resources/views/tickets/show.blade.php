@extends('layouts.app')
@section('title', $ticket->ticket_number)
@section('content')
@php $canWork = auth()->user()->hasRole('admin', 'technician') && $ticket->canBeWorkedOnBy(auth()->user()); @endphp
<div class="actions" style="justify-content:space-between;margin-bottom:12px">
    <h1 style="margin:0">{{ $ticket->ticket_number }} <span class="badge" style="font-size:14px">{{ $ticket->status->name }}</span></h1>
    <div class="actions">
        @if (auth()->user()->hasRole('admin', 'front_desk'))
            <a class="btn sec" href="{{ route('tickets.edit', $ticket) }}">Edit details</a>
        @endif
        @if (auth()->user()->isAdmin())
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Delete this ticket? Parts used will go back to stock.')">
                @csrf @method('DELETE') <button class="btn red">Delete</button>
            </form>
        @endif
    </div>
</div>

<div class="grid">
    <div class="card">
        <h2>Customer</h2>
        <p><strong>{{ $ticket->customer->name }}</strong><br>{{ $ticket->customer->phone }}<br>{{ $ticket->customer->email }}</p>
        <h2>Device</h2>
        <p>{{ $ticket->brand }} {{ $ticket->model }} ({{ $ticket->device_type }})<br>
           <span class="muted">Serial: {{ $ticket->serial_number ?: 'n/a' }}</span></p>
        <h2>Problem</h2>
        <p>{{ $ticket->problem_description }}</p>
        <p class="muted">Received by {{ $ticket->creator->name }} on {{ $ticket->created_at?->format('M d, Y h:i A') }}<br>
           Technician: {{ $ticket->technician?->name ?? 'not assigned yet' }}</p>
    </div>

    <div class="card">
        <h2>Status history</h2>
        <ul class="tl">
            @foreach ($ticket->histories as $h)
                <li><strong>{{ $h->status->name }}</strong> <span class="muted">· {{ $h->created_at?->format('M d, h:i A') }} · {{ $h->changedBy->name }}</span>
                    @if ($h->note)<br>{{ $h->note }}@endif</li>
            @endforeach
        </ul>
    </div>
</div>

@if (auth()->user()->hasRole('technician') && ! $canWork)
<div class="card"><p class="muted">This ticket is assigned to {{ $ticket->technician?->name }}. Only that technician or an admin can update it.</p></div>
@endif
@if ($canWork)
<div class="card">
    <h2>Update repair progress</h2>
    <form method="POST" action="{{ route('tickets.progress', $ticket) }}">
        @csrf @method('PUT')
        <div class="row">
            <div><label for="status_id">Status</label>
                <select id="status_id" name="status_id">
                    @foreach ($statuses as $s)
                        <option value="{{ $s->id }}" @selected($ticket->status_id === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select></div>
            <div><label for="assigned_to">Technician</label>
                <select id="assigned_to" name="assigned_to">
                    <option value="">Assign to me</option>
                    @foreach ($technicians as $u)
                        <option value="{{ $u->id }}" @selected($ticket->assigned_to === $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select></div>
            <div><label for="labor_cost">Labor cost (₱)</label>
                <input id="labor_cost" name="labor_cost" type="number" step="0.01" min="0" value="{{ old('labor_cost', $ticket->labor_cost) }}" required></div>
        </div>
        <label for="note">Note (optional)</label>
        <input id="note" name="note" maxlength="255" value="{{ old('note') }}">
        <p><button class="btn">Save progress</button></p>
    </form>
</div>
@endif

<div class="card">
    <h2>Parts used</h2>
    <table>
        <thead><tr><th>SKU</th><th>Part</th><th class="num">Qty</th><th class="num">Unit price</th><th class="num">Subtotal</th><th></th></tr></thead>
        <tbody>
        @forelse ($ticket->parts as $tp)
            <tr>
                <td>{{ $tp->part->sku }}</td><td>{{ $tp->part->name }}</td>
                <td class="num">{{ $tp->quantity }}</td>
                <td class="num">{{ number_format($tp->unit_price, 2) }}</td>
                <td class="num">{{ number_format($tp->quantity * $tp->unit_price, 2) }}</td>
                <td class="num">
                    @if ($canWork)
                        <form method="POST" action="{{ route('tickets.parts.destroy', [$ticket, $tp]) }}" class="inline">
                            @csrf @method('DELETE') <button class="btn red sm">Remove</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No parts used yet.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="num">Parts</td><td class="num">{{ number_format($ticket->partsCost(), 2) }}</td><td></td></tr>
            <tr><td colspan="4" class="num">Labor</td><td class="num">{{ number_format($ticket->labor_cost, 2) }}</td><td></td></tr>
            <tr><td colspan="4" class="num"><strong>Total</strong></td><td class="num"><strong>₱{{ number_format($ticket->totalCost(), 2) }}</strong></td><td></td></tr>
        </tfoot>
    </table>

    @if ($canWork && ! $ticket->isReleased())
        <form method="POST" action="{{ route('tickets.parts.store', $ticket) }}" class="row" style="margin-top:14px;align-items:end">
            @csrf
            <div><label for="part_id">Add part</label>
                <select id="part_id" name="part_id" required>
                    <option value="">Select a part</option>
                    @foreach ($parts as $p)
                        <option value="{{ $p->id }}">{{ $p->sku }} · {{ $p->name }} ({{ $p->quantity }} in stock)</option>
                    @endforeach
                </select></div>
            <div><label for="quantity">Quantity</label>
                <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required></div>
            <div><button class="btn">Add part</button></div>
        </form>
    @endif
</div>
@endsection
