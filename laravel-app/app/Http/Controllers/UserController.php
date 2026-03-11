<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

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
        $roles = collect($this->roles)->map(fn ($role) => (object) $role);

        return view('pages.user.create', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles = collect($this->roles)->map(fn ($role) => (object) $role);

        return view('pages.user.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Данные обновлены');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user.index')->with('success', 'Пользователь удален');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return redirect()
            ->route('user.index')
            ->with('success', "Пользователь {$user->name} создан!");
    }
}
