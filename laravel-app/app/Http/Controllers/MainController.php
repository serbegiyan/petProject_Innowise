<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;

class MainController extends Controller
{
    public function main()
    {
        $products = Product::count();
        $categories = Category::count();
        $services = Service::count();
        $users = User::count();
        $orders = Order::count();

        return view('pages.home', compact('products', 'categories', 'services', 'users', 'orders'));
    }
}
