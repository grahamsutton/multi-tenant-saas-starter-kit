<?php

use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\Billing\PlanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('billing/plans', [PlanController::class, 'index'])->name('billing.plans');
    Route::post('billing/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('billing/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});
