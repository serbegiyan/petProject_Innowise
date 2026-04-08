<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function main()
    {
        $products = Product::count();
        $categories = Category::count();
        $services = Service::count();
        $users = User::count();
        $orders = Order::count();
        $exports = count(Storage::disk('s3')->files('exports'));

        return view('pages.home', ['products' => $products, 'categories' => $categories, 'services' => $services, 'users' => $users, 'orders' => $orders, 'exports' => $exports]);
    }
}
