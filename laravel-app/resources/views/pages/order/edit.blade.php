@extends('layouts.main')

@section('title', 'Редактировать заказ ' . $order->id)

@section('content')
    <h2 class="text-xl font-bold mb-4">Редактирование заказа: {{ $order->id }}</h2>
    <x-title-link href="{{ route('admin.order.index') }}">Вернуться к списку заказов</x-title-link>
    <form method="POST" action="{{ route('admin.order.update', $order) }}" class="flex flex-col gap-3">
        @csrf
        @method('PATCH')

        <div class="flex flex-row gap-3">
            <div class="w-1/2 flex flex-col">
                <h3 class="p-2 font-bold text-center text-lg">Данные покупателя</h3>

                <x-label class="mt-3 " for="customer_name">Имя покупателя</x-label>
                <x-input id="customer_name" placeholder="Имя покупателя" name="customer_name" type="text"
                    value="{{ old('customer_name', $order->customer_name) }}" />

                <x-label class="mt-3 " for="customer_phone">Телефон покупателя</x-label>
                <x-input id="customer_phone" placeholder="Телефон покупателя" name="customer_phone" type="text"
                    value="{{ old('customer_phone', $order->customer_phone) }}" />

                <x-label class="mt-3 " for="customer_email">Email покупателя</x-label>
                <x-input id="customer_email" placeholder="Email покупателя" name="customer_email" type="text"
                    value="{{ old('customer_email', $order->customer_email) }}" />

                <x-label class="mt-3 " for="customer_address">Адрес доставки</x-label>
                <x-input id="customer_address" placeholder="Адрес доставки" name="customer_address" type="text"
                    value="{{ old('customer_address', $order->customer_address) }}" />

                <x-label class="mt-3 " for="comment">Комментарий</x-label>
                <x-textarea id="comment" placeholder="Комментарий" name="comment" type="text">
                    {{ old('comment', $order->comment) }}
                </x-textarea>
            </div>

            <div class="w-1/2 flex flex-col">
                <h3 class="p-2 font-bold text-center text-lg">Детали заказа</h3>

                <x-label class="mt-3 " for="id">Номер заказа</x-label>
                <x-input id="id" readonly placeholder="Номер заказа" name="id" type="text"
                    value="{{ old('id', $order->id) }}" />


                <x-label class="mt-3 " for="created_at">Дата заказа</x-label>
                <x-input id="created_at" readonly placeholder="Дата заказа" name="created_at" type="text"
                    value="{{ old('created_at', $order->created_at) }}" />

                <x-label class="mt-3 " for="total_price">Общая сумма заказа</x-label>
                <x-input readonly id="total_price"
                    value="{{ old('total_price', Number::format($order->total_price, precision: 2, locale: 'ru')) }} BYN" />

                <x-label class="mt-3 " for="status">Статус заказа</x-label>
                <x-select id="status" name="status" :options="\App\Models\Order::getStatusOptions()" :selected="old('status', $order->status->value ?? $order->status)" />

            </div>
        </div>
        <x-button-success class="self-end">Сохранить</x-button-success>
    </form>
    <h3 class="p-2 font-bold text-center text-lg">Список товаров</h3>
    <ul>
        @foreach ($order->items as $item)
            <li class="list-decimal list-inside even:bg-stone-200 p-2">
                <span class="p-2"><span class="font-bold">Название продукта:</span> {{ $item->product_name }}</span>
                <p class="p-2 font-bold">
                    Цена товара: {{ Number::format($item->price, precision: 2, locale: 'ru') }} BYN
                </p>
                @if (!empty($item->services) && count($item->services) > 0)
                    <div>
                        <p class="px-2 font-bold">Выбранные услуги:</p>
                        <ul>
                            @foreach ($item->services as $service)
                                <li class="p-2 pl-5 list-disc list-inside">
                                    {{ $service['name'] }} —
                                    <strong>{{ Number::format($service['price'], precision: 2, locale: 'ru') }}
                                        BYN
                                    </strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>


@endsection
