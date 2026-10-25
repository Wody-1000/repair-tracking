@extends('layouts.app')
@section('title', 'Available parts')
@section('content')
<h1>Available parts</h1>
@include('partials.shop-hours')
<p class="muted">Browse the parts we have in stock right now. Availability can change during the day, so please confirm with the shop before you come. Prices are for the part only.</p>

<form method="GET" action="{{ route('parts.available') }}" class="card actions">
    <input name="q" value="{{ request('q') }}" placeholder="Search by part name" style="flex:2;min-width:220px" maxlength="50">
    <select name="category" style="flex:1;min-width:160px">
        <option value="">All categories</option>
        @foreach ($categories as $c)
            <option value="{{ $c }}" @selected(request('category') === $c)>{{ $c }}</option>
        @endforeach
    </select>
    <button class="btn">Search</button>
    <a class="btn sec" href="{{ route('parts.available') }}">Reset</a>
</form>

<div class="card">
    <table>
        <thead><tr><th>Part</th><th>Category</th><th class="num">Price</th><th>Availability</th></tr></thead>
        <tbody>
        @forelse ($parts as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category }}</td>
                <td class="num">₱{{ number_format($p->price, 2) }}</td>
                <td>@if ($p->availability === 'Limited stock')<span class="badge low">Limited stock</span>@else<span class="badge">In stock</span>@endif</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No parts found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pager">{{ $parts->links() }}</div>
</div>
@endsection
