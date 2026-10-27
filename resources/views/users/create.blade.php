@extends('layouts.app')
@section('title', 'Create staff account')
@section('content')
<h1>Create staff account</h1>
<form method="POST" action="{{ route('users.store') }}" class="card" style="max-width:480px">
    @csrf
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name') }}" required autofocus>
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
    <label for="role">Role</label>
    <select id="role" name="role" required>
        @foreach (\App\Models\User::ROLES as $role)
            <option value="{{ $role }}" @selected(old('role', \App\Models\User::ROLE_FRONT_DESK) === $role)>{{ str_replace('_', ' ', $role) }}</option>
        @endforeach
    </select>
    <label for="password">Password (min 8 characters)</label>
    <input id="password" name="password" type="password" required>
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" name="password_confirmation" type="password" required>
    <p class="actions" style="margin-top:18px">
        <button class="btn">Create account</button>
        <a class="btn sec" href="{{ route('users.index') }}">Cancel</a>
    </p>
</form>
@endsection
