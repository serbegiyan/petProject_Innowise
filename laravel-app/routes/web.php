<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

Route::get('/catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/basket/store', [BasketController::class, 'store'])->name('basket.store');
    Route::patch('/basket/{id}', [BasketController::class, 'update'])->name('basket.update');
    Route::delete('/basket/{id}', [BasketController::class, 'destroy'])->name('basket.destroy');
    Route::get('/basket', [BasketController::class, 'index'])->name('basket.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', [MainController::class, 'main'])->name('admin.main');

        Route::get('/orders', [OrderController::class, 'index'])->name('order.index');

        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('user.update');
        Route::post('/users/store', [UserController::class, 'store'])->name('user.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('category.store');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

        Route::get('/services', [ServiceController::class, 'index'])->name('service.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('service.create');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('service.show');
        Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('service.update');
        Route::post('/services/store', [ServiceController::class, 'store'])->name('service.store');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');

        Route::get('/products', [ProductController::class, 'index'])->name('product.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');
        Route::patch('/products/{product}', [ProductController::class, 'update'])->name('product.update');
        Route::post('/products/store', [ProductController::class, 'store'])->name('product.store');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
    });

require __DIR__ . '/auth.php';
