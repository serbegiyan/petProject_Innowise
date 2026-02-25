@extends('layouts.main')

@section('title', 'Редактировать категорию')

@section('content')
    <h2 class="text-xl font-bold mb-4">Редактирование категории: {{ $category->name }}</h2>
    <x-title-link href="{{ route('category.index') }}">Вернуться к списку категорий</x-title-link>

    <form method="POST" action="{{ route('category.update', $category) }}" class="flex flex-col gap-3">
        @csrf
        @method('PATCH')
        <x-label for="name">Категория</x-label>
        <x-input placeholder="Введите название категории" name="name" type="text" value="{{ $category->name }}" />
        @error('name')
            <span class="text-red-500 text-sm italic">Введите название категории</span>
        @enderror

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
