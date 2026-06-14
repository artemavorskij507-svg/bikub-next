<?php
use App\Http\Controllers\Api\VippsMobilePayWebhookController; use Illuminate\Support\Facades\Route;
Route::post('/payments/vipps-mobilepay/webhook',VippsMobilePayWebhookController::class)->middleware('throttle:60,1')->name('payments.vipps-mobilepay.webhook');
