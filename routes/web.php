<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JobCardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('job-cards', JobCardController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])
        ->name('customers.search');
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/job-cards', [JobCardController::class, 'jobCardsByCustomer'])
        ->name('customers.job-cards.index');
    
});

require __DIR__ . '/settings.php';
