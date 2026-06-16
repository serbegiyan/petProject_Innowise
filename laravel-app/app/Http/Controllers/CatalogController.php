<?php

namespace App\Http\Controllers;

use App\Enums\ProductSort;
use App\Http\Requests\CatalogRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\BasketService;
use App\Services\CategoryService;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function __construct(
        protected ProductFilterService $productFilterService,
        protected CategoryService $categoryService) {}

    public function index(CatalogRequest $request)
    {
        $products = $this->productFilterService->filter($request->validated());

        return Inertia::render('Catalog/Index', [
            'products' => ProductResource::collection($products),

            'categories' => $this->categoryService->getAll(),

            'filters' => $request->only(['search', 'category', 'sort']),

            'sortOptions' => ProductSort::options(),
        ]);
    }

    public function show(Product $product, Request $request, BasketService $basketService)
    {
        $product->load(['services', 'categories']);

        $currentProductCartItems = Auth::check()
            ? $basketService->getUserBasketItems()
                ->where('product.id', $product->id)
                ->map(fn ($item) => [
                    'product_id' => $item['product']->id,
                    'services' => collect($item['selected_services'])->map(fn ($s) => ['id' => $s->id])->toArray(),
                ])->values()->toArray()
            : [];

        return Inertia::render('Catalog/Show', [
            'product' => new ProductResource($product),
            'cartItemsForProduct' => $currentProductCartItems,
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
