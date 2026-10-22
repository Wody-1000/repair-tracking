@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h1>Reports</h1>
<p class="muted">These reports are read through the read-only database role, so this page can never change data.</p>

<div class="grid">
    <div class="card">
        <h2>Tickets by status</h2>
        <table>@foreach ($byStatus as $r)<tr><td>{{ $r->name }}</td><td class="num">{{ $r->total }}</td></tr>@endforeach</table>
    </div>
    <div class="card">
        <h2>Repairs received per month</h2>
        <table>
            @forelse ($perMonth as $r)<tr><td>{{ $r->month }}</td><td class="num">{{ $r->total }}</td></tr>
            @empty <tr><td class="muted">No data yet.</td></tr> @endforelse
        </table>
    </div>
</div>

<div class="card">
    <h2>Released repairs: income</h2>
    <table>
        <tr><td>Tickets released</td><td class="num">{{ $revenue->tickets }}</td></tr>
        <tr><td>Parts</td><td class="num">₱{{ number_format($revenue->parts, 2) }}</td></tr>
        <tr><td>Labor</td><td class="num">₱{{ number_format($revenue->labor, 2) }}</td></tr>
        <tr><td><strong>Total</strong></td><td class="num"><strong>₱{{ number_format($revenue->total, 2) }}</strong></td></tr>
    </table>
</div>

<div class="grid">
    <div class="card">
        <h2>Most used parts</h2>
        <table>
            <thead><tr><th>SKU</th><th>Part</th><th class="num">Used</th><th class="num">Value</th></tr></thead>
            @forelse ($topParts as $r)
                <tr><td>{{ $r->sku }}</td><td>{{ $r->name }}</td><td class="num">{{ $r->used }}</td><td class="num">{{ number_format($r->value, 2) }}</td></tr>
            @empty <tr><td colspan="4" class="muted">No parts used yet.</td></tr> @endforelse
        </table>
    </div>
    <div class="card">
        <h2>Low-stock parts</h2>
        <table>
            <thead><tr><th>SKU</th><th>Part</th><th class="num">Left</th><th class="num">Reorder at</th></tr></thead>
            @forelse ($lowStock as $r)
                <tr><td>{{ $r->sku }}</td><td>{{ $r->name }}</td><td class="num">{{ $r->quantity }}</td><td class="num">{{ $r->reorder_level }}</td></tr>
            @empty <tr><td colspan="4" class="muted">Everything is above its reorder level.</td></tr> @endforelse
        </table>
    </div>
</div>
@endsection
