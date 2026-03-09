@extends('layouts.main')

@section('title', 'Редактировать пользователя')

@section('content')
    <h2 class="text-xl font-bold mb-4">Редактировать пользователя</h2>
    <x-title-link href="{{ route('user.index') }}">Вернуться к списку пользователей</x-title-link>
    <form method="POST" action="{{ route('user.update', $user) }}" class="flex flex-col gap-3">
        @csrf
        @method('PATCH')
        <x-label for="name">Имя пользователя</x-label>
        <x-input placeholder="Имя пользователя" value="{{ old('name', $user->name) }}" name="name" type="text" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="role">Выберите роль пользователя</x-label>
        <x-select name="role" :options="$roles" :selected="old('role', $user->role ?? null)">
            {{ $user->role_label }}
        </x-select>

        <x-label for="email">Email</x-label>
        <x-input placeholder="Email" value="{{ old('email', $user->email) }}" name="email" type="email" />
        @error('email')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="password">Новый пароль</x-label>
        <x-input placeholder="Введите новый пароль" name="password" type="password" />
        @error('password')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="password_confirmation">Подтверждение нового пароля</x-label>
        <x-input placeholder="Введите нового подтверждение пароля" name="password_confirmation" type="password" />
        @error('password_confirmation')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
