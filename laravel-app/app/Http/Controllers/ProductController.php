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
    public function index()
    {
        $products = Product::latest()->paginate(10);
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
                // 1. Удаляем старое фото с диска, если оно существует
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                // 2. Сохраняем новое фото
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            // 3. Синхронизируем категории (sync заменяет все старые связи на новые)
            $product->categories()->sync($request->category_id);

            // 4. Подготавливаем данные для услуг (Pivot)
            $servicesData = [];
            if ($request->has('services')) {
                foreach ($request->services as $serviceId) {
                    $servicesData[$serviceId] = [
                        'price' => $request->service_prices[$serviceId] ?? 0,
                        'term' => $request->service_terms[$serviceId] ?? 'не указан',
                    ];
                }
            }

            // Синхронизируем услуги (удалит старые, добавит/обновит текущие)
            $product->services()->sync($servicesData);

            return redirect()->route('product.index');
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

            return redirect()->route('product.index')->with('success', 'Продукт успешно создан!');
        });
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('product.index');
    }
}
