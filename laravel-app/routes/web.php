<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\OrderAdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::post('/currency/change', [CurrencyController::class, 'change'])->name('currency.change');

Route::get('/dashboard', [OrderController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::post('/basket/store', [BasketController::class, 'store'])->name('basket.store');
    Route::patch('/basket/{id}', [BasketController::class, 'update'])->name('basket.update');
    Route::delete('/basket/{id}', [BasketController::class, 'destroy'])->name('basket.destroy');
    Route::get('/basket', [BasketController::class, 'index'])->name('basket.index');
    Route::get('/order', [OrderController::class, 'create'])->name('order.create');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function (): void {
        Route::get('/', [MainController::class, 'main'])->name('admin.main');

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

        Route::get('/export/index', [ExportController::class, 'index'])->name('export.index');
        Route::post('/export/run', [ExportController::class, 'export'])->name('export.run');
        Route::delete('/exports/{id}', [ExportController::class, 'destroy'])->name('export.destroy');

        Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.order.index');
        Route::get('/orders/create', [OrderAdminController::class, 'create'])->name('admin.order.create');
        Route::get('/orders/{order}/edit', [OrderAdminController::class, 'edit'])->name('admin.order.edit');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('admin.order.show');
        Route::patch('/orders/{order}', [OrderAdminController::class, 'update'])->name('admin.order.update');
        Route::post('/orders/store', [OrderAdminController::class, 'store'])->name('admin.order.store');
        Route::delete('/orders/{order}', [OrderAdminController::class, 'destroy'])->name('admin.order.destroy');
    });

require __DIR__.'/auth.php';
