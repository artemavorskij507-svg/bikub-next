<?php

use App\Http\Controllers\PublicCmsController;
use App\Http\Controllers\PublicOrderRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicWorkerApplicationController;
use App\Http\Controllers\PublicWorkerInvitationController;

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
