<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service) {}

    public function index()
    {
        return view('pages.category.index', [
            'categories' => $this->service->getAllPaginated(),
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->service->create($request->validated());

        return to_route('category.index')->with('success', "Категория {$category->name} создана!");
    }

    public function destroy(Category $category)
    {
        $this->service->delete($category);

        return to_route('category.index')->with('success', "Категория {$category->name} удалена!");
    }

    public function create()
    {
        return view('pages.category.create');
    }

    public function edit(Category $category)
    {
        return view('pages.category.edit', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('category.index')
            ->with('success', 'Категория '.$category['name'].' успешно изменена!');
    }
}
