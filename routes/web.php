<?php

use App\Http\Controllers\PublicCmsController;
use App\Http\Controllers\PublicOrderRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicWorkerApplicationController;
use App\Http\Controllers\PublicWorkerInvitationController;
use App\Http\Controllers\WorkerCockpitController;
use App\Http\Controllers\AdminLiveOperationsMapDataController;

Route::pattern('order', '[0-9]+');

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
    Route::post('/orders/{order}/accept', [WorkerCockpitController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/start', [WorkerCockpitController::class, 'start'])->name('orders.start');
    Route::post('/orders/{order}/arrived-pickup', [WorkerCockpitController::class, 'arrivedPickup'])->name('orders.arrived-pickup');
    Route::post('/orders/{order}/picked-up', [WorkerCockpitController::class, 'pickedUp'])->name('orders.picked-up');
    Route::post('/orders/{order}/arrived-dropoff', [WorkerCockpitController::class, 'arrivedDropoff'])->name('orders.arrived-dropoff');
    Route::post('/orders/{order}/complete', [WorkerCockpitController::class, 'complete'])->name('orders.complete');
    Route::post('/presence/online', [WorkerCockpitController::class, 'online'])->name('presence.online');
    Route::post('/presence/offline', [WorkerCockpitController::class, 'offline'])->name('presence.offline');
    Route::post('/location-pings', [WorkerCockpitController::class, 'location'])->name('location-pings.store');
})->whereNumber('order');

Route::get('/admin/live-operations-map/data', AdminLiveOperationsMapDataController::class)
    ->middleware(['auth', 'admin.operator'])
    ->name('admin.live-operations-map.data');
