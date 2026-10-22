@extends('layouts.app')
@section('title', 'New ticket')
@section('content')
<h1>New repair ticket</h1>
<form method="POST" action="{{ route('tickets.store') }}" class="card">
    @csrf
    @include('tickets._form')
    <p class="actions" style="margin-top:18px">
        <button class="btn">Create ticket</button>
        <a class="btn sec" href="{{ route('tickets.index') }}">Cancel</a>
    </p>
</form>
@endsection
