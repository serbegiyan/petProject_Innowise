<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    protected $productFilterService;

    public function __construct(ProductFilterService $productFilterService)
    {
        $this->productFilterService = $productFilterService;
    }

    public function index(CatalogRequest $request)
    {
        return Inertia::render('Catalog/Index', [
            'products' => $this->productFilterService->filter($request->validated()),
            'categories' => Category::all(),
            'currencies' => ExchangeRate::all(),
            'filters' => $request->only(['search', 'category', 'sort']),
        ]);
    }

    public function show(Product $product, Request $request)
    {
        $product->load('services', 'categories');

        return Inertia::render('Catalog/Show', [
            'currencies' => ExchangeRate::all(),
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
