@extends('layouts.main')

@section('title', 'Услуги')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Услуги</h2>
    <x-title-link href="{{ route('service.create') }}">Создать новую услугу</x-title-link>
    <ul>
        <li class="flex flex-row font-bold p-2">
            <p class="w-1/3">Название услуги</p>
            <p class="w-1/3">Slug</p>
            <p class="w-1/3">Описание</p>
            <p>Действия</p>
        </li>
        @foreach ($services as $service)
            <li class="flex hover:bg-stone-300 flex-row p-2 justify-between items-center even:bg-stone-200">
                <p class="w-1/3"><a href="{{ route('service.show', $service) }}">{{ $service->name }}</a></p>
                <p class="w-1/3">{{ $service->slug }}</p>
                <p class="w-1/3">{{ Str::limit($service->description, 30, '...') }}</p>
                <div class="flex flex-row">
                    <a class="ml-4 text-blue-600 hover:text-blue-800" href="{{ route('service.edit', $service) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('service.destroy', $service) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ml-4 text-red-500 hover:text-red-700 cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="mt-4">
        {{ $services->links() }}
    </div>
@endsection
