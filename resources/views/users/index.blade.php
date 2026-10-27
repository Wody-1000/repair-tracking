@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="actions" style="justify-content:space-between;margin-bottom:12px">
    <h1 style="margin:0">Staff accounts</h1>
    <a class="btn" href="{{ route('users.create') }}">+ Create staff account</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead>
        <tbody>
        @foreach ($users as $u)
            <tr>
                <td>{{ $u->name }}</td><td>{{ $u->email }}</td>
                <td><span class="badge">{{ str_replace('_', ' ', $u->role) }}</span></td>
                <td class="num">
                    <a class="btn sec sm" href="{{ route('users.edit', $u) }}">Edit</a>
                    @if ($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline" onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE') <button class="btn red sm">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
