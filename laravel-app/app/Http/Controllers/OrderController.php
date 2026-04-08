<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\BasketService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Dashboard', [
            'orders' => $user->orders()
                ->with('items')
                ->latest()
                ->get()
                ->map(fn ($order) => [
                    'id' => $order->id,
                    'total' => $order->total_price,
                    // Передаем всё необходимое из Enum
                    'status' => $order->status->value,       // код (напр. 'pending')
                    'status_label' => $order->status->label(), // текст (напр. 'Ожидает оплаты')
                    'status_css' => $order->status->cssClass(), // класс (напр. 'text-yellow-500')

                    'items' => $order->items,
                    'created_at' => $order->created_at->format('d.m.Y H:i'),
                ]),
            'currencies' => ExchangeRate::all(),
        ]);
    }

    public function create(BasketService $basketService)
    {
        $checkoutData = $basketService->getCheckoutDetails();

        if ($checkoutData['items']->isEmpty()) {
            return redirect()->route('basket.index');
        }

        return Inertia::render('Order/Create', [
            'items' => $checkoutData['items'],
            'totalAmount' => $checkoutData['totalAmount'],
            'currencies' => ExchangeRate::all(),
        ]);
    }

    public function store(OrderStoreRequest $request, OrderService $orderService)
    {
        $orderService->createFromBasket(Auth::user(), $request->validated());

        return redirect()->route('dashboard');
    }
}
