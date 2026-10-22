@extends('layouts.app')
@section('title', 'Edit ticket')
@section('content')
<h1>Edit {{ $ticket->ticket_number }}</h1>
<form method="POST" action="{{ route('tickets.update', $ticket) }}" class="card">
    @csrf @method('PUT')
    @include('tickets._form')
    <p class="actions" style="margin-top:18px">
        <button class="btn">Save changes</button>
        <a class="btn sec" href="{{ route('tickets.show', $ticket) }}">Cancel</a>
    </p>
</form>
@endsection
