@extends('layouts.app')
@section('title', $part->exists ? 'Edit part' : 'Add part')
@section('content')
<h1>{{ $part->exists ? 'Edit part' : 'Add part' }}</h1>
<form method="POST" action="{{ $part->exists ? route('parts.update', $part) : route('parts.store') }}" class="card">
    @csrf
    @if ($part->exists) @method('PUT') @endif
    <div class="row">
        <div><label for="sku">SKU</label>
            <input id="sku" name="sku" value="{{ old('sku', $part->sku) }}" required></div>
        <div><label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $part->name) }}" required></div>
        <div><label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((int) old('category_id', $part->category_id) === $c->id)>{{ $c->name }}</option>
                @endforeach
            </select></div>
    </div>
    <div class="row">
        <div><label for="quantity">Quantity in stock</label>
            <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $part->quantity) }}" required></div>
        <div><label for="reorder_level">Reorder level (low-stock warning)</label>
            <input id="reorder_level" name="reorder_level" type="number" min="0" value="{{ old('reorder_level', $part->reorder_level) }}" required></div>
        <div><label for="unit_price">Unit price (₱)</label>
            <input id="unit_price" name="unit_price" type="number" step="0.01" min="0" value="{{ old('unit_price', $part->unit_price) }}" required></div>
    </div>
    <p class="actions" style="margin-top:18px">
        <button class="btn">Save</button>
        <a class="btn sec" href="{{ route('parts.index') }}">Cancel</a>
    </p>
</form>
@endsection
