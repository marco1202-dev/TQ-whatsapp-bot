<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CampaignController;

// Auth routes (login, register, etc.)
require __DIR__.'/auth.php';

// Homepage redirects to dashboard if logged in
Route::get('/', function () {
    return redirect('/admin/dashboard');
});

// Admin routes with auth middleware
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // WhatsApp Bots CRUD
    Route::resource('/bots', BotController::class);

    // Contacts CRUD
    Route::resource('/contacts', ContactController::class);

    // Campaigns CRUD
    Route::resource('/campaigns', CampaignController::class);
});
