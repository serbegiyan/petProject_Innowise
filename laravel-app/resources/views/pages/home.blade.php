@extends('layouts.main')

@section('title', 'Главная')

@section('content')
    <div class="grid grid-cols-4 gap-4">
        <a href="{{ route('product.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Продукты</h2>
                <p class="text-center">Количество продуктов: {{ $products }}</p>
            </div>
        </a>
        <a href="{{ route('service.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Услуги</h2>
                <p class="text-center">Количество услуг: {{ $services }}</p>
            </div>
        </a>
        <a href="{{ route('category.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Категории</h2>
                <p class="text-center">Количество категорий: {{ $categories }}</p>
            </div>
        </a>
        <a href="{{ route('user.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Пользователи</h2>
                <p class="text-center">Количество пользователей: {{ $users }}</p>
            </div>
        </a>
        <a href="{{ route('admin.order.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Заказы</h2>
                <p class="text-center">Количество заказов: {{ $orders }}</p>
            </div>
        </a>
        <a href="{{ route('export.index') }}">
            <div class="border rounded-lg bg-white p-3">
                <h2 class="text-center text-xl font-bold mb-4">Экспорты</h2>
                <p class="text-center">Количество экспортов: {{ $exports }}</p>
            </div>
        </a>
    </div>

@endsection
