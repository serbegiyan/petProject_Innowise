<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Order;

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
