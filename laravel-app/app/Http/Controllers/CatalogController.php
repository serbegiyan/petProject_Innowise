<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductFilterService;
use App\Services\StatsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function __construct(
        protected ProductFilterService $productFilterService,
        protected StatsService $statsService) {}

    public function index(CatalogRequest $request)
    {
        $products = $this->productFilterService->filter($request->validated());

        return Inertia::render('Catalog/Index', [
            'products' => ProductResource::collection($products),

            'categories' => $this->statsService->getAllCategories(),

            'filters' => $request->only(['search', 'category', 'sort']),
        ]);
    }

    public function show(Product $product, Request $request)
    {
        $product->load(['services', 'categories']);

        return Inertia::render('Catalog/Show', [
            'product' => new ProductResource($product),

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
