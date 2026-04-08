@extends('layouts.main')

@section('title', 'Пользователи')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Пользователи</h2>
    <x-title-link href="{{ route('user.create') }}">Создать нового пользователя</x-title-link>
    <ul>
        <li class="flex flex-row font-bold p-2">
            <p class="w-1/3">Имя пользователя</p>
            <p class="w-1/3">Email</p>
            <p class="w-1/3">Права</p>
            <p>Действия</p>
        </li>
        @foreach ($users as $user)
            <li class="flex hover:bg-stone-300 flex-row p-2 justify-between items-center even:bg-stone-200">
                <p class="w-1/3"><a href="{{ route('user.show', $user) }}">{{ $user->name }}</a></p>
                <p class="w-1/3">{{ $user->email }}</p>
                <p class="w-1/3"><span
                        class="badge {{ $user->role_class }} py-1 px-2 rounded-lg">{{ $user->role_label }}</span></p>
                <div class="flex flex-row">
                    <a title="Редактировать" class="ml-4 text-blue-600 hover:text-blue-800"
                        href="{{ route('user.edit', $user) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('user.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button title="Удалить" type="submit" class="ml-4 text-red-500 hover:text-red-700 cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>

@endsection
