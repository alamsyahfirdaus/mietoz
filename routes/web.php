<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // Redirect pengguna dari halaman utama ke halaman login
    return redirect('shop');
});

// Login Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login')->middleware('guest');
    Route::post('login', 'authenticate');
    Route::get('logout', 'logout')->name('logout');
});

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('setting', [HomeController::class, 'setting'])->name('home.setting');
        Route::match(['post', 'put'], 'bank/save', [HomeController::class, 'saveBank'])->name('bank.save');
        Route::delete('bank/{id}', [HomeController::class, 'deleteBank'])->name('bank.delete');
        Route::match(['post', 'put'], 'carousel/save', [HomeController::class, 'saveCarousel'])->name('carousel.save');
        Route::delete('carousel/{id}', [HomeController::class, 'deleteCarousel'])->name('carousel.delete');
        Route::post('nochatwa', [HomeController::class, 'updateNoWhatsApp'])->name('wa.save');
    });
});

// Guest Routes
Route::get('shop', [HomeController::class, 'shop'])->name('home.shop')->middleware('guest');
Route::post('shop', [OrderController::class, 'save'])->name('add.order')->middleware('guest');
Route::get('order/{id}/payment', [HomeController::class, 'payOrder'])->name('home.show')->middleware('guest');
Route::post('payment', [HomeController::class, 'confirmPayment'])->name('home.payment')->middleware('guest');
Route::post('message', [HomeController::class, 'sendMessage'])->name('home.message');
Route::match(['get', 'post'], 'order/{id}/chat', [HomeController::class, 'chatList'])->name('home.chat');


// Auth and Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    // User Routes
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('user/add', [UserController::class, 'create'])->name('user.add');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::match(['post', 'put'], 'user/save/{id?}', [UserController::class, 'save'])->name('user.save');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.delete');

    // CategoryController Routes
    Route::get('category', [CategoryController::class, 'index'])->name('category');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::match(['post', 'put'], 'category/save/{id?}', [CategoryController::class, 'save'])->name('category.save');
    Route::delete('category/{id}', [CategoryController::class, 'destroy'])->name('category.delete');

    // Product Routes
    Route::get('product', [ProductController::class, 'index'])->name('product');
    Route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::match(['post', 'put'], 'product/save/{id?}', [ProductController::class, 'save'])->name('product.save');
    Route::delete('product/{id}', [ProductController::class, 'destroy'])->name('product.delete');
    Route::match(['get', 'post'], 'product/{id}/sold', [ProductController::class, 'productSold'])->name('product.sold');

    // Customer Routes
    Route::get('customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::match(['post', 'put'], 'customer/save/{id?}', [CustomerController::class, 'save'])->name('customer.save');
    Route::delete('customer/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');

    // Order Routes
    // Route::get('order', [OrderController::class, 'index'])->name('order');
    Route::match(['get', 'post'], 'order', [OrderController::class, 'index'])->name('order');
    Route::get('order/{id}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('order/update', [OrderController::class, 'update'])->name('order.update');
    Route::match(['post', 'put'], 'order/save/{id?}', [OrderController::class, 'save'])->name('order.save');
    Route::get('order/{id}/show', [OrderController::class, 'show'])->name('order.show');
    Route::delete('order/{id}', [OrderController::class, 'destroy'])->name('order.delete');
    Route::get('order/{id}/invoice', [OrderController::class, 'invoice'])->name('order.invoice');
});
