@extends('layouts.main')

@section('title', 'Продукты')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Продукты</h2>
    <x-title-link href="{{ route('product.create') }}">Создать новый продукт</x-title-link>
    <ul>
        <li class="flex flex-row font-bold p-2">
            <p class="w-1/3">Название продукта</p>
            <p class="w-1/3">Цена</p>
            <p class="w-1/3">Slug</p>
            <p class="w-1/3">Описание</p>
            <p>Действия</p>
        </li>
        @foreach ($products as $product)
            <li class="flex hover:bg-stone-300 flex-row p-2 justify-between items-center even:bg-stone-200">
                <p class="w-1/3"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></p>
                <p class="w-1/3">{{ $product->formatted_price }}</p>
                <p class="w-1/3">{{ $product->slug }}</p>
                <p class="w-1/3">{{ Str::limit($product->description, 30, '...') }}</p>
                <div class="flex flex-row">
                    <a class="ml-4 text-blue-600 hover:text-blue-800" href="{{ route('product.edit', $product) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('product.destroy', $product) }}" method="POST">
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
        {{ $products->links() }}
    </div>
@endsection
