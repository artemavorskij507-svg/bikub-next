<?php

use App\Http\Controllers\PublicCmsController;
use App\Http\Controllers\PublicOrderRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home');
});

Route::get('/p/{slug}', [PublicCmsController::class, 'page'])->name('public.cms.page');
Route::get('/services/{serviceSlug}', [PublicCmsController::class, 'servicePage'])->name('public.cms.service-page');
Route::get('/services/{serviceSlug}/request', [PublicOrderRequestController::class, 'create'])->name('public.orders.request');
Route::post('/services/{serviceSlug}/request', [PublicOrderRequestController::class, 'store'])->name('public.orders.store');
Route::get('/order-requests/{orderNumber}/received', [PublicOrderRequestController::class, 'confirmation'])->name('public.orders.confirmation');
