@extends('layouts.main')

@section('title', 'Создать категорию')

@section('content')
    <h2 class="text-xl font-bold mb-4">Создание новой категории</h2>
    <x-title-link href="{{ route('category.index') }}">Вернуться к списку категорий</x-title-link>
    <form method="POST" action="{{ route('category.store') }}" class="flex flex-col gap-3">
        @csrf
        <x-label for="name">Категория</x-label>
        <x-input placeholder="Введите название категории" name="name" type="text" />
        @error('name')
            <span class="text-red-500 text-sm italic">Введите название категории</span>
        @enderror

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
