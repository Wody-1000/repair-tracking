<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Admin only: manage staff accounts and roles.
class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = new User($data);
        $user->role = $data['role'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.']);
        }

        $user->name = $data['name'];
        $user->role = $data['role']; // set explicitly: role is not mass-assignable
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }

        if ($user->assignedTickets()->exists() || Ticket::where('created_by', $user->id)->exists()) {
            return back()->withErrors(['delete' => 'This user has ticket history and cannot be deleted.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
