@extends('layouts.main')

@section('title', 'Создать пользователя')

@section('content')
    <h2 class="text-xl font-bold mb-4">Создание нового пользователя</h2>
    <x-title-link href="{{ route('user.index') }}">Вернуться к списку пользователей</x-title-link>
    <form method="POST" action="{{ route('user.store') }}" class="flex flex-col gap-3">
        @csrf
        <x-label for="name">Имя пользователя</x-label>
        <x-input placeholder="Введите имя пользователя" name="name" type="text" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="role">Выберите роль пользователя</x-label>
        <x-select name="role" :options="$roles" :selected="old('role')">
            Выберите роль...
        </x-select>

        <x-label for="email">Email</x-label>
        <x-input placeholder="Email" name="email" type="email" />
        @error('email')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="password">Пароль</x-label>
        <x-input placeholder="Введите пароль" name="password" type="password" />
        @error('password')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="password_confirmation">Подтверждение пароля</x-label>
        <x-input placeholder="Введите подтверждение пароля" name="password_confirmation" type="password" />
        @error('password_confirmation')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
