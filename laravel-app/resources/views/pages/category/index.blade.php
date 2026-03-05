@extends('layouts.main')

@section('title', 'Категории')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Категории</h2>
    <x-title-link href="{{ route('category.create') }}">Создать новую категорию</x-title-link>
    <ul>
        <li class="flex flex-row font-bold p-2 justify-between">
            <p class="w-1/2">Название категории</p>
            <p class="w-1/2">Slug</p>
            <p class="">Действия</p>
        </li>
        @foreach ($categories as $category)
            <li class="flex hover:bg-stone-300 flex-row p-2 justify-between items-center even:bg-stone-200">
                <p class="w-1/2"><a href="{{ route('category.edit', $category) }}">{{ $category->name }}</a></p>
                <p class="w-1/2">{{ $category->slug }}</p>
                <div class="flex flex-row">
                    <a class="ml-4 text-blue-600 hover:text-blue-800" href="{{ route('category.edit', $category) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('category.destroy', $category) }}" method="POST">
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
        {{ $categories->links() }}
    </div>
@endsection
