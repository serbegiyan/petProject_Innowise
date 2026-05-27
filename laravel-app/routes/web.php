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

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->middleware('can:viewAny,App\Models\User')->name('user.index');
            Route::get('/create', [UserController::class, 'create'])->middleware('can:create,App\Models\User')->name('user.create');
            Route::post('/store', [UserController::class, 'store'])->middleware('can:create,App\Models\User')->name('user.store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('can:update,user')->name('user.edit');
            Route::get('/{user}', [UserController::class, 'show'])->middleware('can:view,user')->name('user.show');
            Route::patch('/{user}', [UserController::class, 'update'])->middleware('can:update,user')->name('user.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:delete,user')->name('user.destroy');
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category.index');
            Route::get('/create', [CategoryController::class, 'create'])->middleware('can:create,App\Models\Category')->name('category.create');
            Route::post('/store', [CategoryController::class, 'store'])->middleware('can:create,App\Models\Category')->name('category.store');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->middleware('can:update,category')->name('category.edit');
            Route::patch('/{category}', [CategoryController::class, 'update'])->middleware('can:update,category')->name('category.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('can:delete,category')->name('category.destroy');
        });

        Route::prefix('services')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('service.index');
            Route::get('/create', [ServiceController::class, 'create'])->middleware('can:create,App\Models\Service')->name('service.create');
            Route::post('/store', [ServiceController::class, 'store'])->middleware('can:create,App\Models\Service')->name('service.store');
            Route::get('/{service}/edit', [ServiceController::class, 'edit'])->middleware('can:update,service')->name('service.edit');
            Route::get('/{service}', [ServiceController::class, 'show'])->middleware('can:view,service')->name('service.show');
            Route::patch('/{service}', [ServiceController::class, 'update'])->middleware('can:update,service')->name('service.update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->middleware('can:delete,service')->name('service.destroy');
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('product.index');
            Route::get('/create', [ProductController::class, 'create'])->middleware('can:create,App\Models\Product')->name('product.create');
            Route::post('/store', [ProductController::class, 'store'])->middleware('can:create,App\Models\Product')->name('product.store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->middleware('can:update,product')->name('product.edit');
            Route::get('/{product}', [ProductController::class, 'show'])->middleware('can:view,product')->name('product.show');
            Route::patch('/{product}', [ProductController::class, 'update'])->middleware('can:update,product')->name('product.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('can:delete,product')->name('product.destroy');
        });

        Route::prefix('export')->group(function () {
            Route::get('/index', [ExportController::class, 'index'])->middleware('can:viewAny,App\Models\Export')->name('export.index');
            Route::post('/run', [ExportController::class, 'export'])->middleware('can:create,App\Models\Export')->name('export.run');
            Route::delete('/{id}', [ExportController::class, 'destroy'])->middleware('can:create,App\Models\Export')->name('export.destroy');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderAdminController::class, 'index'])->middleware('can:viewAny,App\Models\Order')->name('admin.order.index');
            Route::get('/create', [OrderAdminController::class, 'create'])->middleware('can:create,App\Models\Order')->name('admin.order.create');
            Route::post('/store', [OrderAdminController::class, 'store'])->middleware('can:create,App\Models\Order')->name('admin.order.store');
            Route::get('/{order}/edit', [OrderAdminController::class, 'edit'])->middleware('can:update,order')->name('admin.order.edit');
            Route::get('/{order}', [OrderAdminController::class, 'show'])->middleware('can:view,order')->name('admin.order.show');
            Route::patch('/{order}', [OrderAdminController::class, 'update'])->middleware('can:update,order')->name('admin.order.update');
            Route::delete('/{order}', [OrderAdminController::class, 'destroy'])->middleware('can:delete,order')->name('admin.order.destroy');
        });
    });

require __DIR__.'/auth.php';
