@extends('layouts.main')

@section('title', 'Редактировать услугу')

@section('content')
    <h2 class="text-xl font-bold mb-4">Редактирование услуги: {{ $service->name }}</h2>
    <x-title-link href="{{ route('service.index') }}">Вернуться к списку услуг</x-title-link>

    <form method="POST" action="{{ route('service.update', $service) }}" class="flex flex-col gap-3">
        @csrf
        @method('PATCH')
        <x-label for="name">Название услуги</x-label>
        <x-input placeholder="Введите название услуги" name="name" type="text" value="{{ $service->name }}" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="description">Описание услуги</x-label>
        <x-textarea placeholder="Введите описание услуги" name="description"
            type="text">{{ old('description', $service->description) }}</x-textarea>
        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
