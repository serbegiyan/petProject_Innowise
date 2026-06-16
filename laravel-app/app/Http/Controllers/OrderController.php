<?php

namespace App\Http\Controllers;

use App\DTO\OrderData;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
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

        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'orders' => OrderResource::collection($orders)->response()->getData(true)['data'] ?? [],
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
        ]);
    }

    public function store(OrderStoreRequest $request, OrderService $orderService)
    {
        $orderData = OrderData::fromRequest($request->validated());

        $orderService->createFromBasket($request->user(), $orderData);

        return redirect()->route('dashboard');
    }
}
