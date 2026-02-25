<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('pages.category.index', compact('categories'));
    }
    public function create()
    {
        return view('pages.category.create');
    }
    public function store(Request $request)
    {
        $category = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
        ]);
        Category::create($category);
        return redirect(route('category.index'));
    }
    public function edit(Category $category)
    {
        return view('pages.category.edit', compact('category'));
    }
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);
        $category->update($validated);
        return redirect(route('category.index'));
    }
    public function destroy(Category $category)
    {
        Category::destroy($category->id);
        return redirect(route('category.index'));
    }
}
