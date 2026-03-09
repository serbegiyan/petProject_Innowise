@extends('layouts.main')

@section('title', 'Пользователь' . $user->name)

@section('content')
    <h2 class="text-xl font-bold mb-4">Пользователь {{ $user->name }}</h2>
    <x-title-link href="{{ route('user.index') }}">Вернуться к списку пользователей</x-title-link>

    <p class="p-2"><span class="font-bold">Имя пользователя:</span> {{ $user->name }}</p>
    <p class="p-2"><span class="font-bold">Роль пользователя:</span> {{ $user->role }}</p>
    <p class="p-2"><span class="font-bold">Email пользователя:</span> {{ $user->email }}</p>


@endsection
