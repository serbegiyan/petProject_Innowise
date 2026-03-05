<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class CatalogController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalog/Index', [
            'products' => Product::latest()->get(),
        ]);
    }

    public function show(Product $product, Request $request)
    {
        $product->load('services', 'categories');
        return Inertia::render('Catalog/Show', [
            'product' => $product,
            'preSelectedIds' => $request->query('selected', []),
            'edit_cart_id' => $request->query('edit_cart_id'),
        ]);
    }
}
