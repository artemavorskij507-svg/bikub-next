<?php

use App\Http\Controllers\PublicCmsController;
use App\Http\Controllers\PublicOrderRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicWorkerApplicationController;
use App\Http\Controllers\PublicWorkerInvitationController;
use App\Http\Controllers\WorkerCockpitController;

Route::get('/', function () {
    return view('public.home');
});

Route::get('/p/{slug}', [PublicCmsController::class, 'page'])->name('public.cms.page');
Route::get('/services/{serviceSlug}', [PublicCmsController::class, 'servicePage'])->name('public.cms.service-page');
Route::get('/services/{serviceSlug}/request', [PublicOrderRequestController::class, 'create'])->name('public.orders.request');
Route::post('/services/{serviceSlug}/request', [PublicOrderRequestController::class, 'store'])->name('public.orders.store');
Route::get('/order-requests/{orderNumber}/received', [PublicOrderRequestController::class, 'confirmation'])->name('public.orders.confirmation');
Route::get('/become-worker', [PublicWorkerApplicationController::class, 'create'])->name('public.workers.apply');
Route::post('/become-worker', [PublicWorkerApplicationController::class, 'store'])->name('public.workers.store');
Route::get('/become-worker/received', [PublicWorkerApplicationController::class, 'received'])->name('public.workers.received');
Route::get('/worker-invitations/received', [PublicWorkerInvitationController::class, 'received'])->name('public.worker-invitations.received');
Route::get('/worker-invitations/{token}', [PublicWorkerInvitationController::class, 'show'])->name('public.worker-invitations.show');
Route::post('/worker-invitations/{token}', [PublicWorkerInvitationController::class, 'store'])->name('public.worker-invitations.store');

Route::middleware(['auth', 'approved.worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerCockpitController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [WorkerCockpitController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [WorkerCockpitController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/{action}', [WorkerCockpitController::class, 'action'])->whereIn('action', ['accept', 'start', 'arrived-pickup', 'picked-up', 'arrived-dropoff', 'complete'])->name('orders.action');
    Route::post('/presence/online', [WorkerCockpitController::class, 'online'])->name('presence.online');
    Route::post('/presence/offline', [WorkerCockpitController::class, 'offline'])->name('presence.offline');
    Route::post('/location-pings', [WorkerCockpitController::class, 'location'])->name('location-pings.store');
});
