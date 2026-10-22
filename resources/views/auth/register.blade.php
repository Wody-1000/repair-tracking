@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="card" style="max-width:420px;margin:auto">
    <h1>Create staff account</h1>
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <label for="name">Name</label>
        <input id="name" name="name" value="{{ old('name') }}" required autofocus>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <label for="password">Password (min 8 characters)</label>
        <input id="password" name="password" type="password" required>
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>
        <p><button class="btn">Register</button></p>
    </form>
    <p class="muted">Already registered? <a href="{{ route('login') }}">Log in</a>. An admin can change your role later.</p>
</div>
@endsection
