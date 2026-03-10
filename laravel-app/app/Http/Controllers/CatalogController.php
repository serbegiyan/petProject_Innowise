<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Catalog/Index', [
            'products' => Product::query()
                ->with('categories')
                ->when($request->input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($request->input('category'), function ($query, $categoryId) {
                    $query->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('categories.id', $categoryId);
                    });
                })
                ->when($request->input('sort'), function ($query, $sort) {
                    if ($sort === 'price_asc') {
                        $query->orderBy('price', 'asc');
                    }
                    if ($sort === 'price_desc') {
                        $query->orderBy('price', 'desc');
                    }
                    if ($sort === 'release_asc') {
                        $query->orderBy('release_date', 'asc');
                    }
                    if ($sort === 'release_desc') {
                        $query->orderBy('release_date', 'desc');
                    }
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),

            'categories' => Category::all(),
            'filters' => $request->only(['search', 'category', 'sort']),
        ]);
    }

    public function show(Product $product, Request $request)
    {
        $product->load('services', 'categories');

        return Inertia::render('Catalog/Show', [
            'product' => $product,
            'filters' => [
                'search' => $request->query('search', ''),
                'category' => $request->query('category', ''),
                'sort' => $request->query('sort', ''),
            ],
            'preSelectedIds' => $request->query('selected', []),
            'edit_cart_id' => $request->query('edit_cart_id'),
        ]);
    }
}
