<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;

Route::get('/', 
[ProductController::class, 'index'])->name('product.index');

Route::post('/products/listAjax',
[ProductController::class, 'listAjax'])->name('product.list-ajax');

Route::post('/products/store',
[ProductController::class, 'store'])->name('product.store');

Route::put('/products/update',
[ProductController::class, 'update'])->name('product.update');

Route::delete('/products/delete/{id}',
[ProductController::class, 'delete'])->name('product.delete');

Route::post('/login',
[LoginController::class, 'login'])->name('login');

Route::post('/logout',
[LoginController::class, 'logout'])->name('logout');
