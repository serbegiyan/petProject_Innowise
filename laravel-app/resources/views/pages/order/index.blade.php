@extends('layouts.main')

@section('title', 'Заказы')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Заказы</h2>
    <ul>
        <li class="flex flex-row font-bold p-2">
            <p class="w-1/3">Номер заказа</p>
            <p class="w-1/3">Дата заказа</p>
            <p class="w-1/3">Сумма заказа</p>
            <p class="w-1/3">Статус</p>
            <p>Действия</p>
        </li>
        @foreach ($orders as $order)
            <li class="flex hover:bg-stone-300 flex-row p-2 justify-between items-center even:bg-stone-200">
                <a class="w-full flex flex-row items-center" href="{{ route('admin.order.show', $order) }}">
                    <p class="w-1/3">{{ $order->id }}</p>
                    <p class="w-1/3">{{ $order->created_at }}</p>
                    <p class="w-1/3">{{ Number::format($order->total_price, precision: 2, locale: 'ru') }} BYN</p>
                    <p class="w-1/3"><span
                            class="badge {{ $order->status_class }} py-1 px-2 rounded-lg">{{ $order->status_label }}</span>
                    </p>
                    <div class="flex flex-row">
                </a>
                <a class="ml-4 text-blue-600 hover:text-blue-800" href="{{ route('admin.order.edit', $order) }}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>
                <form action="{{ route('admin.order.destroy', $order) }}" method="POST">
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
        {{ $orders->links() }}
    </div>
@endsection
