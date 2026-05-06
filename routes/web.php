<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\UcTestimonyController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\FeaturedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AboutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/', function () {
    return redirect('/featured');
})->name('home');

// Home route - redirect to appropriate page based on auth status

Route::get('/about', AboutController::class)->name('about');
Route::get('/featured', [FeaturedController::class, 'index'])->name('featured');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/intrapreneurs/{company}', [BusinessController::class, 'showIntrapreneur'])->name('intrapreneurs.show');
Route::get('/uc-testimonies', [UcTestimonyController::class, 'index'])->name('uc-testimonies.index');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/import-progress/{sessionId}', [ImportController::class, 'progress'])->name('import.progress');
    Route::get('/import-progress/check', [ImportController::class, 'checkActive'])->name('import.check');
    Route::post('/clear-active-import', [ImportController::class, 'clearActive'])->name('import.clear');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my-businesses', [BusinessController::class, 'my'])->name('businesses.my');
    Route::get('/businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])->name('businesses.store');
    Route::get('/businesses/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
    Route::put('/businesses/{business}', [BusinessController::class, 'update'])->name('businesses.update');

    Route::post('/uc-testimonies', [UcTestimonyController::class, 'store'])->name('uc-testimonies.store');
});

// ============================================================
// ADMIN-ONLY ROUTES
// ============================================================

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::post('/users/{user}/toggle-featured', [UserController::class, 'toggleFeatured'])->name('users.toggle-featured');
    
    Route::post('/businesses/import', [BusinessController::class, 'import'])->name('businesses.import');
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
    Route::post('/businesses/{business}/toggle-featured', [BusinessController::class, 'toggleFeatured'])->name('businesses.toggle-featured');
    Route::post('/businesses/{business}/approve', [BusinessController::class, 'approve'])->name('businesses.approve');
});

// This must be LAST to avoid matching static routes like 'create'
Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');



require __DIR__.'/auth.php';
