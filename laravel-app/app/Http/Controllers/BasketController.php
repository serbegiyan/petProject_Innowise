<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Basket;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Product;

class BasketController extends Controller
{
    public function index()
    {
        $items = [];
        $basketItems = Basket::where('user_id', auth()->id())
            ->with(['product.services'])
            ->get();

        foreach ($basketItems as $item) {
            $selectedIds = collect($item->services)->pluck('id')->toArray();

            $selectedServices = $item->product->services->whereIn('id', $selectedIds);

            $items[] = [
                'cart_id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'selected_services' => $selectedServices->values(),
            ];
        }
        return Inertia::render('Basket/Index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'services' => 'nullable|array',
            'services.*.id' => 'required|exists:services,id',
        ]);

        if ($request->has('edit_cart_id')) {
            Basket::where('id', $request->edit_cart_id)
                ->where('user_id', auth()->id())
                ->delete();
        }

        $services = collect($request->input('services', []))->sortBy('id')->values()->toArray();

        // Используем cast к массиву, чтобы Eloquent сам сравнил JSON
        $basketItem = Basket::where('user_id', auth()->id())
            ->where('product_id', $data['product_id'])
            ->get()
            ->filter(function ($item) use ($services) {
                return $item->services == $services;
            })
            ->first();

        if ($basketItem) {
            $basketItem->increment('quantity');
        } else {
            Basket::create([
                'user_id' => auth()->id(),
                'product_id' => $data['product_id'],
                'services' => $services,
                'quantity' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Товар добавлен!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = Basket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $item->update(['quantity' => $request->quantity]);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = Basket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $item->delete();

        return redirect()->back()->with('error', 'Товар удален из корзины');
    }
}
