@extends('layouts.main')

@section('title', 'Редактировать продукт')

@section('content')
    <h2 class="text-xl font-bold mb-4">Редактирование продукта: {{ $product->name }}</h2>
    <x-title-link href="{{ route('product.index') }}">Вернуться к списку продуктов</x-title-link>
    <form method="POST" enctype="multipart/form-data" action="{{ route('product.update', $product) }}"
        class="flex flex-col gap-3">
        @csrf
        @method('PATCH')
        <x-label for="category_id">Категория продукта</x-label>
        <x-select name="category_id" :options="$categories" :selected="$product->categories->first()?->id">
            Выберите категорию
        </x-select>
        @error('category_id')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="services">Услуги, связанные с продуктом</x-label>
        @foreach ($services as $service)
            @php
                // Ищем, привязана ли услуга, чтобы достать данные из pivot
                $attached = $product->services->where('id', $service->id)->first();
                $currentPrice = old("service_prices.{$service->id}", $attached->pivot->price ?? '');
                $currentTerm = old("service_terms.{$service->id}", $attached->pivot->term ?? '');

            @endphp
            <div class="flex flex-row gap-2 items-center justify-between">
                <div class="w-1/5 flex items-center gap-2">
                    <input type="checkbox" name="services[]" value="{{ $service->id }}" {{ $attached ? 'checked' : '' }}>

                    <span class="w-1/4 font-bold">{{ $service->name }}</span>
                </div>
                <!-- Поле для цены (берем из pivot или из основной таблицы, если пусто) -->
                <x-input type="number" step="0.01" name="service_prices[{{ $service->id }}]"
                    value="{{ $currentPrice }}" class="w-1/3" placeholder="Цена BYN" />

                <!-- Поле для срока (term) -->
                <x-input type="text" name="service_terms[{{ $service->id }}]" value="{{ $currentTerm }}"
                    class="w-1/3" placeholder="Срок выполнения услуги" />
            </div>
        @endforeach
        <x-label for="name">Название продукта</x-label>
        <x-input placeholder="Введите название продукта" name="name" type="text"
            value="{{ old('name', $product->name) }}" />
        @error('name')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="brand">Производитель</x-label>
        <x-input placeholder="Введите название производителя" name="brand" type="text"
            value="{{ old('brand', $product->brand) }}" />
        @error('brand')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="description">Описание продукта</x-label>
        <x-textarea placeholder="Введите описание продукта" name="description"
            type="text">{{ old('description', $product->description) }}</x-textarea>
        @error('description')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="price">Цена продукта, BYN</x-label>
        <x-input placeholder="Введите цену продукта" name="price" type="number" step="0.01" min="0"
            value="{{ old('price', $product->price) }}" />
        @error('price')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="release_date">Дата выпуска</x-label>
        <x-input placeholder="Введите дату выпуска" name="release_date" type="date"
            value="{{ old('release_date', $product->release_date) }}" />
        @error('release_date')
            <span class="text-red-500 text-sm italic">{{ $message }}</span>
        @enderror

        <x-label for="image">Изображение продукта</x-label>
        @if ($product->image)
            <div class="p-2"><img
                    src="{{ $product->image && Storage::exists($product->image)
                        ? Storage::url($product->image)
                        : asset('images/product-image.png') }}"
                    alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg"></div>
        @endif
        <x-input name="image" type="file" />

        <x-button-success>Сохранить</x-button-success>
    </form>
@endsection
