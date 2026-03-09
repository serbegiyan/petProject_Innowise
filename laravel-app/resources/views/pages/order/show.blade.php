@extends('layouts.main')

@section('title', 'Просмотр заказа: ' . $order->id)

@section('content')
    <h2 class="text-xl font-bold mb-4">Просмотр заказа: {{ $order->id }}</h2>
    <x-title-link href="{{ route('admin.order.index') }}">Вернуться к списку заказов</x-title-link>

    <div class="flex flex-row gap-3">
        <div class="w-1/2">
            <h3 class="p-2 font-bold text-center text-lg">Данные покупателя</h3>
            <p class="p-2"><span class="font-bold">Имя покупателя:</span> {{ $order->customer_name }}</p>
            <p class="p-2"><span class="font-bold">Телефон:</span> {{ $order->customer_phone }}</p>
            <p class="p-2"><span class="font-bold">Email:</span> {{ $order->customer_email }}</p>
            <p class="p-2"><span class="font-bold">Адрес доставки:</span> {{ $order->customer_address }}</p>
            @if ($order->comment)
                <p class="p-2"><span class="font-bold">Комментарий:</span> {{ $order->comment }}</p>
            @endif
        </div>
        <div class="w-1/2">
            <h3 class="p-2 font-bold text-center text-lg">Детали заказа</h3>
            <p class="p-2"><span class="font-bold">Номер заказа:</span> {{ $order->id }}</p>
            <p class="p-2"><span class="font-bold">Дата заказа:</span> {{ $order->created_at }}</p>
            <p class="p-2"><span class="font-bold">Общая сумма заказа:</span>
                <strong>{{ Number::format($order->total_price, precision: 2, locale: 'ru') }}
                    BYN</strong>
            </p>
            <p class="p-2"><span class="font-bold">Статус:</span>
                <span class="badge {{ $order->status_class }} py-1 px-2 rounded-lg">{{ $order->status_label }}</span>
            </p>
        </div>
    </div>

    <h3 class="p-2 font-bold text-center text-lg">Список товаров</h3>
    <ul>
        @foreach ($order->items as $item)
            <li class="list-decimal list-inside even:bg-stone-200 p-2">
                <span class="p-2"><span class="font-bold">Название продукта:</span> {{ $item->product_name }}</span>
                <p class="p-2"><span class="font-bold">Цена товара:</span><strong>
                        {{ Number::format($item->price, precision: 2, locale: 'ru') }} BYN</p></strong>
                @if (!empty($item->services) && count($item->services) > 0)
                    <div>
                        <p class="px-2 font-bold">Выбранные услуги:</p>
                        <ul>
                            @foreach ($item->services as $service)
                                <li class="p-2 pl-5 list-disc list-inside">
                                    {{ $service['name'] }} —
                                    <strong>{{ Number::format($service['price'], precision: 2, locale: 'ru') }}
                                        BYN</strong>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>






@endsection
