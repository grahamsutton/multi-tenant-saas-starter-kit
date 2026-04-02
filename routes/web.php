<?php

use App\Http\Controllers\OrganizationSwitchController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::put('organizations/{organization}/switch', OrganizationSwitchController::class)->name('organization.switch');
});

Route::middleware(['auth', 'verified', 'subscribed'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/billing.php';
require __DIR__.'/settings.php';
