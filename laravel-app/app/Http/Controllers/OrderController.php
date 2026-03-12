<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Services\BasketService;
use App\Services\OrderService;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'orders' => auth()->user()->orders()->with('items')->latest()->get(),
        ]);
    }

    public function create(BasketService $basketService)
    {
        $checkoutData = $basketService->getCheckoutDetails();

        if ($checkoutData['items']->isEmpty()) {
            return redirect()->route('basket.index');
        }

        return Inertia::render('Order/Create', $checkoutData);
    }

    public function store(OrderStoreRequest $request, OrderService $orderService)
    {
        $orderService->createFromBasket(auth()->user(), $request->validated());

        return redirect()->route('dashboard');
    }
}
