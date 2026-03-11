<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;

class OrderAdminController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->latest()->paginate(10);

        return view('pages.order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user']);

        return view('pages.order.show', compact('order'));
    }

    public function create()
    {
        //
    }

    public function store()
    {
        //
    }

    public function edit(Order $order)
    {
        $order->load(['items', 'user']);

        return view('pages.order.edit', compact('order'));
    }

    public function update(OrderUpdateRequest $request, Order $order)
    {
        $data = $request->validated();

        $order->update($data);

        return redirect()
            ->route('admin.order.index')
            ->with('success', 'Заказ '.$order->id.' успешно изменен!');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('admin.order.index')
            ->with('success', 'Заказ '.$order->id.' успешно удален!');
    }
}
