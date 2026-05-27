<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        } else {
            $query
                ->when(
                    $request->category_id,
                    fn ($query, $categoryId) => $query->whereHas('categories', function ($q) use ($categoryId): void {
                        $q->where('categories.id', $categoryId);
                    }),
                )
                ->latest();
        }

        $products = $query->paginate(10);

        return view('pages.product.index', ['products' => $products]);
    }

    public function create()
    {
        $categories = Category::all();
        $services = Service::all();

        return view('pages.product.create', ['categories' => $categories, 'services' => $services]);
    }

    public function edit(Product $product)
    {
        $product->load('services');
        $categories = Category::all();
        $services = Service::all();

        return view('pages.product.edit', ['product' => $product, 'categories' => $categories, 'services' => $services]);
    }

    public function show(Product $product)
    {
        $product->load('services');

        return view('pages.product.show', ['product' => $product]);
    }

    public function store(ProductRequest $request, ProductService $service)
    {
        $validated = $request->validated();

        $relationData = [
            'category_ids' => $validated['category_id'] ?? [],
            'services' => $validated['services'] ?? [],
            'service_prices' => $validated['service_prices'] ?? [],
            'service_terms' => $validated['service_terms'] ?? [],
        ];

        $service->create(
            $validated,
            $request->file('image'),
            $relationData
        );

        return redirect()
            ->route('product.index')
            ->with('success', "Товар {$validated['name']} успешно создан!");
    }

    public function update(ProductRequest $request, Product $product, ProductService $service)
    {
        $validated = $request->validated();

        $relationData = [
            'category_ids' => $validated['category_id'] ?? [],
            'services' => $validated['services'] ?? [],
            'service_prices' => $validated['service_prices'] ?? [],
            'service_terms' => $validated['service_terms'] ?? [],
        ];

        $service->update(
            $product,
            $validated,
            $request->file('image'),
            $relationData
        );

        return redirect()
            ->route('product.index')
            ->with('success', "Товар {$product->name} успешно изменён!");
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('product.index')
            ->with('success', 'Товар '.$product->name.' успешно удален!');
    }
}
