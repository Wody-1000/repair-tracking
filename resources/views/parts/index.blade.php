@extends('layouts.app')
@section('title', 'Parts inventory')
@section('content')
<div class="actions" style="justify-content:space-between;margin-bottom:12px">
    <h1 style="margin:0">Parts inventory</h1>
    @if (auth()->user()->isAdmin())
        <a class="btn" href="{{ route('parts.create') }}">+ Add part</a>
    @endif
</div>

<form method="GET" class="card actions">
    <input name="q" value="{{ request('q') }}" placeholder="Search SKU or name" style="flex:2;min-width:220px">
    <label style="margin:0;font-weight:400"><input type="checkbox" name="low" value="1" style="width:auto" @checked(request()->boolean('low'))> Low stock only</label>
    <button class="btn">Filter</button>
    <a class="btn sec" href="{{ route('parts.index') }}">Reset</a>
</form>

<div class="card">
    <table>
        <thead><tr><th>SKU</th><th>Name</th><th>Category</th><th class="num">In stock</th><th class="num">Reorder at</th><th class="num">Price</th><th></th></tr></thead>
        <tbody>
        @forelse ($parts as $p)
            <tr>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name }}</td>
                <td class="num">{{ $p->quantity }} @if ($p->isLowStock())<span class="badge low">Low</span>@endif</td>
                <td class="num">{{ $p->reorder_level }}</td>
                <td class="num">{{ number_format($p->unit_price, 2) }}</td>
                <td class="num">
                    @if (auth()->user()->isAdmin())
                        <a class="btn sec sm" href="{{ route('parts.edit', $p) }}">Edit</a>
                        <form method="POST" action="{{ route('parts.destroy', $p) }}" class="inline" onsubmit="return confirm('Delete this part?')">
                            @csrf @method('DELETE') <button class="btn red sm">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="muted">No parts found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pager">{{ $parts->links() }}</div>
</div>
@endsection
