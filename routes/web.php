<?php

use App\Http\Controllers\PublicCmsController;
use App\Http\Controllers\PublicOrderRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicWorkerApplicationController;
use App\Http\Controllers\PublicWorkerInvitationController;
use App\Http\Controllers\WorkerCockpitController;
use App\Http\Controllers\AdminLiveOperationsMapDataController;
use App\Http\Controllers\AdminWorkerDocumentDownloadController;
use App\Http\Controllers\AccountSupportController;
use App\Http\Controllers\WorkerSupportController;
use App\Http\Controllers\AdminSupportActivityController;
use App\Http\Controllers\AdminSupportAttachmentDownloadController;
use App\Http\Controllers\AccountBillingController;
use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\ThemePaletteController;

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
    Route::get('/support', [WorkerSupportController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}', [WorkerSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [WorkerSupportController::class, 'reply'])->name('support.reply');
})->whereNumber('order');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::get('/billing', [AccountBillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/documents/{billingDocument}', [AccountBillingController::class, 'show'])->whereNumber('billingDocument')->name('billing.documents.show');
    Route::get('/support', [AccountSupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [AccountSupportController::class, 'create'])->name('support.create');
    Route::post('/support', [AccountSupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [AccountSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [AccountSupportController::class, 'reply'])->name('support.reply');
});

Route::middleware(['auth', 'throttle:30,1'])->prefix('theme-palette')->name('theme-palette.')->group(function () {
    Route::get('/config', [ThemePaletteController::class, 'config'])->name('config');
    Route::post('/save', [ThemePaletteController::class, 'save'])->name('save');
    Route::post('/reset', [ThemePaletteController::class, 'reset'])->name('reset');
});

Route::get('/admin/live-operations-map/data', AdminLiveOperationsMapDataController::class)
    ->middleware(['auth', 'admin.operator'])
    ->name('admin.live-operations-map.data');

Route::get('/admin/worker-documents/{workerDocument}/download', AdminWorkerDocumentDownloadController::class)
    ->middleware('auth')->whereNumber('workerDocument')->name('admin.worker-documents.download');

Route::get('/admin/support-activity', AdminSupportActivityController::class)
    ->middleware('auth')
    ->name('admin.support.activity');
Route::redirect('/admin/support', '/admin/support-center')->middleware('auth')->name('admin.support.redirect');
Route::get('/admin/support-attachments/{media}/download', AdminSupportAttachmentDownloadController::class)
    ->middleware('auth')->whereNumber('media')->name('admin.support.attachments.download');
