<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Service;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->category_id, function ($query, $categoryId) {
                // Ищем продукты, у которых в связанной таблице categories есть этот ID
                return $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->latest()
            ->paginate(10);
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

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $data, $product) {
            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            // Синхронизируем категории (sync заменяет все старые связи на новые)
            $product->categories()->sync($request->category_id);

            // Подготавливаем данные для услуг (Pivot)
            $servicesData = [];
            if ($request->has('services')) {
                foreach ($request->services as $serviceId) {
                    $servicesData[$serviceId] = [
                        'price' => $request->service_prices[$serviceId] ?? 0,
                        'term' => $request->service_terms[$serviceId] ?? 'не указан',
                    ];
                }
            }

            // Синхронизируем услуги
            $product->services()->sync($servicesData);

            return redirect()
                ->route('product.index')
                ->with('success', 'Товар ' . $product->name . ' успешно изменен!');
        });
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($data);

            $product->categories()->attach($data['category_id']);

            if ($request->has('services')) {
                $pivotData = [];

                foreach ($request->services as $serviceId) {
                    $pivotData[$serviceId] = [
                        'price' => $request->service_prices[$serviceId] ?? 0,
                        'term' => $request->service_terms[$serviceId] ?? 'не указан',
                    ];
                }

                $product->services()->attach($pivotData);
            }

            return redirect()
                ->route('product.index')
                ->with('success', 'Товар ' . $product->name . ' успешно создан!');
        });
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()
            ->route('product.index')
            ->with('success', 'Товар ' . $product->name . ' успешно удален!');
    }
}
