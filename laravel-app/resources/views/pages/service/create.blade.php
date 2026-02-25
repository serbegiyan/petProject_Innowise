@extends('layouts.main')

@section('title', 'Создать услугу')

@section('content')
    <h2 class="text-xl font-bold mb-4">Создание новой услуги</h2>
    <x-title-link href="{{ route('service.index') }}">Вернуться к списку услуг</x-title-link>
    <form method="POST" action="{{ route('service.store') }}" class="flex flex-col gap-3">
        @csrf
        <x-label for="name">Название услуги</x-label>
        <x-input placeholder="Введите название услуги" name="name" type="text" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="description">Описание услуги</x-label>
        <x-textarea placeholder="Введите описание услуги" name="description" type="text" />


        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
