<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'orders' => auth()->user()->orders()->with('items')->latest()->get(),
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        $basketItems = $user
            ->baskets()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    // здесь лежат услуги из JSON
                    'services' => $item->services,
                    'item_total' => ($item->product->price + collect($item->services)->sum('price')) * $item->quantity,
                ];
            });

        if ($basketItems->isEmpty()) {
            return redirect()->route('basket.index')->with('error', 'Ваша корзина пуста');
        }

        return Inertia::render('Order/Create', [
            'items' => $basketItems,
            'totalAmount' => round($basketItems->sum('item_total'), 2),
            'userEmail' => $user->email,
        ]);
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        $basketItems = $user->baskets()->with('product')->get();

        if ($basketItems->isEmpty()) {
            return back()->withErrors(['error' => 'Корзина пуста']);
        }

        return DB::transaction(function () use ($user, $validated, $basketItems) {
            $totalPrice = $basketItems->sum(function ($item) {
                return ($item->product->price + collect($item->services)->sum('price')) * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'customer_address' => $validated['customer_address'],
                'comment' => $validated['comment'] ?? null,
            ]);

            foreach ($basketItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price + collect($item->services)->sum('price'),
                    'services' => $item->services,
                ]);
            }

            $user->baskets()->delete();

            return redirect()->route('dashboard')->with('success', 'Заказ успешно оформлен!');
        });
    }
}
