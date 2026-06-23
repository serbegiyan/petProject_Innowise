<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('pages.user.index', ['users' => $users]);
    }

    public function show(User $user)
    {
        return view('pages.user.show', ['user' => $user]);
    }

    public function create()
    {
        $roles = $this->userService->getRolesForSelect();

        return view('pages.user.create', ['roles' => $roles]);
    }

    public function edit(User $user)
    {
        $roles = $this->userService->getRolesForSelect();

        return view('pages.user.edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (isset($data['role'])) {
            $data['role'] = UserRole::from($data['role']);
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
