@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="card" style="max-width:420px;margin:auto">
    <h1>Staff login</h1>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        <label style="font-weight:400"><input type="checkbox" name="remember" value="1" style="width:auto"> Remember me</label>
        <p><button class="btn">Log in</button></p>
    </form>
    <p class="muted">Staff accounts are created by an administrator. Contact an admin for access.</p>
</div>
@endsection
