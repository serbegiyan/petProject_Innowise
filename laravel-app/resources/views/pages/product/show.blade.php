@extends('layouts.main')

@section('title', 'Просмотр продукта: ' . $product->name)

@section('content')
    <h2 class="text-xl font-bold mb-4">Просмотр продукта: {{ $product->name }}</h2>
    <x-title-link href="{{ route('product.index') }}">Вернуться к списку продуктов</x-title-link>

    <p class="p-2"><span class="font-bold">Название продукта:</span> {{ $product->name }}</p>
    <p class="p-2"><span class="font-bold">Производитель:</span> {{ $product->brand }}</p>
    <p class="p-2"><span class="font-bold">Slug:</span> {{ $product->slug }}</p>
    <p class="p-2"><span class="font-bold">Описание продукта:</span> {{ $product->description }}</p>
    <p class="p-2"><span class="font-bold">Цена продукта:</span>
        {{ Number::format($product->price, precision: 2, locale: 'ru') }} BYN
    </p>
    <p class="p-2"><span class="font-bold">Дата выпуска:</span> {{ $product->release_date }}</p>
    <p class="p-2"><span class="font-bold">Категория продукта:</span>
        {{ $product->categories->pluck('name')->implode(', ') }}
    </p>
    <p class="p-2 font-bold">Услуги, связанные с продуктом:</p>
    @foreach ($product->services as $service)
        <li class="p-2 list-none pl-5">
            {{ $service->name }} —
            <strong>{{ $service->pivot->price }} BYN</strong>
            (Срок: {{ $service->pivot->term }})
        </li>
    @endforeach


    @if ($product->image)
        <div class="p-2"><img
                src="{{ $product->image && Storage::disk('public')->exists($product->image)
                    ? Storage::url($product->image)
                    : asset('images/product-image.png') }}"
                alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
        </div>
    @endif

@endsection
