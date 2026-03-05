<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.user.index', compact('users'));
    }
    public function create()
    {
        return view('pages.user.create');
    }
    public function edit()
    {
        return view('pages.user.edit');
    }
    public function update()
    {
        return view('pages.user.update');
    }
    public function destroy()
    {
        return view('pages.user.destroy');
    }
    public function store()
    {
        return view('pages.user.store');
    }
}
