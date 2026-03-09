<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $roles = [
        [
            'id' => 'user',
            'name' => 'Пользователь',
        ],
        [
            'id' => 'admin',
            'name' => 'Администратор',
        ],
    ];

    public function index()
    {
        $users = User::all();
        return view('pages.user.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('pages.user.show', compact('user'));
    }

    public function create()
    {
        $roles = collect($this->roles)->map(fn($role) => (object) $role);
        return view('pages.user.create', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles = collect($this->roles)->map(fn($role) => (object) $role);
        return view('pages.user.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->fill(collect($validated)->except('password')->toArray());

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->update();

        return redirect()->route('user.index')->with('success', 'Пользователь обновлен');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Пользователь удален');
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        return DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'user',
            ]);

            return redirect()
                ->route('user.index')
                ->with('success', 'Пользователь ' . $user->name . ' успешно создан!');
        });
    }
}
