<?php

namespace App\Http\Controllers;

use App\Http\Requests\BasketStoreRequest;
use App\Http\Requests\BasketUpdateRequest;
use App\Models\ExchangeRate;
use App\Services\BasketService;
use Inertia\Inertia;

class BasketController extends Controller
{
    public function index(BasketService $basketService)
    {
        return Inertia::render('Basket/Index', [
            'items' => $basketService->getUserBasketItems(),
            'currencies' => ExchangeRate::all(),
        ]);
    }

    public function store(BasketStoreRequest $request, BasketService $basketService)
    {
        $basketService->addToBasket($request->validated(), $request->integer('edit_cart_id') ?: null);

        return redirect()->back()->with('success', 'Товар добавлен!');
    }

    public function update(BasketUpdateRequest $request, int $id, BasketService $basketService)
    {
        $basketService->updateQuantity($id, $request->integer('quantity'));

        return redirect()->back();
    }

    public function destroy(int $id, BasketService $basketService)
    {
        $basketService->removeItem($id);

        return redirect()->back()->with('error', 'Товар удален из корзины');
    }
}
