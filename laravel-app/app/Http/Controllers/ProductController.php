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
                ->when($request->category_id, function ($query, $categoryId) {
                    return $query->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('categories.id', $categoryId);
                    });
                })
                ->latest();
        }

        $products = $query->paginate(10);

        return view('pages.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $services = Service::all();

        return view('pages.product.create', compact('categories', 'services'));
    }

    public function edit(Product $product)
    {
        $product->load('services');
        $categories = Category::all();
        $services = Service::all();

        return view('pages.product.edit', compact('product', 'categories', 'services'));
    }

    public function show(Product $product)
    {
        $product->load('services');

        return view('pages.product.show', compact('product'));
    }

    public function store(ProductRequest $request, ProductService $service)
    {
        $product = $service->create($request);

        return redirect()
            ->route('product.index')
            ->with('success', "Товар {$product->name} успешно создан!");
    }

    public function update(ProductRequest $request, Product $product, ProductService $service)
    {
        $service->update($product, $request);

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
