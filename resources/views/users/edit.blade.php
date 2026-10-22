@extends('layouts.app')
@section('title', 'Edit user')
@section('content')
<h1>Edit {{ $user->name }}</h1>
<form method="POST" action="{{ route('users.update', $user) }}" class="card" style="max-width:480px">
    @csrf @method('PUT')
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
    <label for="role">Role</label>
    <select id="role" name="role">
        @foreach (\App\Models\User::ROLES as $r)
            <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ str_replace('_', ' ', $r) }}</option>
        @endforeach
    </select>
    <p class="actions" style="margin-top:18px">
        <button class="btn">Save</button>
        <a class="btn sec" href="{{ route('users.index') }}">Cancel</a>
    </p>
</form>
@endsection
