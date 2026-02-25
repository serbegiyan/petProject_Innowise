@extends('layouts.main')

@section('title', 'Создать продукт')

@section('content')
    <h2 class="text-xl font-bold mb-4">Создание нового продукта</h2>
    <x-title-link href="{{ route('product.index') }}">Вернуться к списку продуктов</x-title-link>
    <form method="POST" enctype="multipart/form-data" action="{{ route('product.store') }}" class="flex flex-col gap-3">
        @csrf
        <x-label for="category_id">Категория продукта</x-label>
        <x-select name="category_id" :options="$categories" :selected="$product->category_id ?? null">
            Выберите категорию
        </x-select>
        @error('category_id')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="services">Услуги, связанные с продуктом</x-label>
        @foreach ($services as $service)
            <div class="flex flex-row gap-2 items-center justify-between">
                <div class="w-1/5 flex items-center gap-2">
                    <input type="checkbox" name="services[]" value="{{ $service->id }}">
                    <span class="font-bold">{{ $service->name }}</span>
                </div>
                <x-input class="w-1/3" type="number" name="service_prices[{{ $service->id }}]" placeholder="Цена BYN" />
                <x-input class="w-1/3" type="text" name="service_terms[{{ $service->id }}]" placeholder="Срок" />
            </div>
        @endforeach

        <x-label for="name">Название продукта</x-label>
        <x-input placeholder="Введите название продукта" name="name" type="text" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="brand">Производитель</x-label>
        <x-input placeholder="Введите название производителя" name="brand" type="text" />
        @error('brand')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="description">Описание продукта</x-label>
        <x-textarea placeholder="Введите описание продукта" name="description" type="text" />
        @error('description')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="price">Цена продукта</x-label>
        <x-input placeholder="Введите цену продукта" name="price" type="number" step="0.01" min="0" />
        @error('price')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="release_date">Дата выпуска</x-label>
        <x-input placeholder="Введите дату выпуска" name="release_date" type="date" />
        @error('release_date')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="image">Изображение продукта</x-label>
        <x-input name="image" type="file" />
        @error('image')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
